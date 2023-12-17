# necessary Libraries
import numpy as np
import pandas as pd
import time
import pprint
from itertools import product

# Visualizations
import matplotlib.pyplot as plt
# import seaborn as sns
from pandas.plotting import scatter_matrix
# magic word for producing visualizations in notebook

# Preprocessing
from sklearn import preprocessing
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
from sklearn.decomposition import PCA
from scipy.sparse import lil_matrix

# Models
from sklearn.model_selection import train_test_split
from sklearn.neighbors import KNeighborsClassifier

# Scoring Metrics
from sklearn.model_selection import GridSearchCV
import sklearn.metrics as metrics
from sklearn.metrics import f1_score, fbeta_score
from sklearn.metrics import accuracy_score

#Models
from sklearn.naive_bayes import GaussianNB
from skmultilearn.problem_transform import BinaryRelevance
from skmultilearn.problem_transform import ClassifierChain
from skmultilearn.problem_transform import LabelPowerset
from skmultilearn.adapt import MLkNN

import os
import mysql.connector
from mysql.connector import Error  # Import the Error module

# Database configuration
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "rfid_ips"
}


def connect_to_database():
    try:
        connection = mysql.connector.connect(**db_config)
        if connection.is_connected():
            print("Connected to MySQL database")
            return connection
    except Error as e:
        print(f"Error: {e}")
    return None

# Function to connect to the MySQL database
def connect_to_database():
    try:
        connection = mysql.connector.connect(**db_config)
        if connection.is_connected():
            print("Connected to MySQL database")
            return connection
    except Error as e:
        print(f"Error: {e}")
    return None


# Function to save DataFrame to CSV, deleting the existing file if it exists
def save_dataframe_to_csv(df, file_path):
    if os.path.exists(file_path):
        os.remove(file_path)
        print(f"Previous file '{file_path}' deleted.")

    df.to_csv(file_path, index=False)
    print(f"Data extracted and saved to {file_path}")


def clean_data(df):
    """
    Perform feature trimming, and engineering for trainingData
    Will also be applied to validationData

    INPUT: trainingData DataFrame
    OUTPUT: Trimmed and cleaned trainingData DataFrame
    """

    # Reverse the representation for the values. 100=0 and teh values range from 0-105 (weakest to strongest)
    # "The intensity values are represented as negative integer values ranging -104dBm (extremely poor signal) to 0dbM.
    # The positive value 100 is used to denote when a WAP was not detected."
    '''
    df.iloc[:, 6:9] = np.where(df.iloc[:, 6:9] <= 0,
                                df.iloc[:, 6:9] + 105,
                                df.iloc[:, 6:9] - 100)
    '''

    # remove selected columns...
    columns_removed = ['mac_address', 'timestamp']
    for col in columns_removed:
        df.drop(col, axis=1, inplace=True)

    # Return the cleaned dataframe.
    return df

def clean_data_dataset(df):
    """
    Perform feature trimming, and engineering for trainingData
    Will also be applied to validationData

    INPUT: trainingData DataFrame
    OUTPUT: Trimmed and cleaned trainingData DataFrame
    """

    # Reverse the representation for the values. 100=0 and teh values range from 0-105 (weakest to strongest)
    # "The intensity values are represented as negative integer values ranging -104dBm (extremely poor signal) to 0dbM.
    # The positive value 100 is used to denote when a WAP was not detected."
    df.iloc[:, 5:8] = np.where(df.iloc[:, 5:8] <= 0,
                               df.iloc[:, 5:8] + 105,
                               df.iloc[:, 5:8] - 100)

    '''
    df.iloc[:, 6:9] = np.where(df.iloc[:, 6:9] > 2000, 
                df.iloc[:, 6:9] - 2300, 
                df.iloc[:, 6:9] - 0)
    '''

    # Remove rows if there are two zeroes in a row in the cap_channel
    df = df[
        ~((df['channel_cap1'] == 0) & (df['channel_cap2'] == 0)) &
        ~((df['channel_cap1'] == 0) & (df['channel_cap3'] == 0)) &
        ~((df['channel_cap2'] == 0) & (df['channel_cap3'] == 0))
        ]

    # Remove rows if there are one zeroes in a row in the cap_channel
    df = df[
        ~((df['channel_cap1'] == 0)) &
        ~((df['channel_cap2'] == 0)) &
        ~((df['channel_cap3'] == 0))
        ]

    # Return the cleaned dataframe.
    return df

def preprocess_data(df):
    """
    Separates trainingData into Features and Targets
    Will also be applied to validationData

    INPUT: Cleaned trainingData DataFrame
    OUTPUT: trainingData as Features and Targets
    """

    X = df

    # Extract unique channel values
    unique_channels = sorted(
        set(df['channel_cap1'].unique()) | set(df['channel_cap2'].unique()) | set(df['channel_cap3'].unique()))

    # Create new one-hot encoded columns
    for channel in unique_channels:
        X[f'channel_cap1_{channel}'] = (df['channel_cap1'] == channel).astype(int)
        X[f'channel_cap2_{channel}'] = (df['channel_cap2'] == channel).astype(int)
        X[f'channel_cap3_{channel}'] = (df['channel_cap3'] == channel).astype(int)

    # Drop the original 'channel_cap1', 'channel_cap2', and 'channel_cap3' columns
    X.drop(['channel_cap1', 'channel_cap2', 'channel_cap3'], axis=1, inplace=True)

    # Iterate over signal strength caps and channels to perform multiplication
    signal_columns = ['signal_strength_cap1', 'signal_strength_cap2', 'signal_strength_cap3']

    for signal_col in signal_columns:
        for channel in unique_channels:
            channel_col1 = f'channel_cap1_{channel}'
            channel_col2 = f'channel_cap2_{channel}'
            channel_col3 = f'channel_cap3_{channel}'

            if signal_col.endswith('cap1'):
                X[f'{signal_col}_{channel_col1}'] = df[signal_col] * X[channel_col1]
            elif signal_col.endswith('cap2'):
                X[f'{signal_col}_{channel_col2}'] = df[signal_col] * X[channel_col2]
            elif signal_col.endswith('cap3'):
                X[f'{signal_col}_{channel_col3}'] = df[signal_col] * X[channel_col3]

    # Drop the original 'signal_strength' columns
    X.drop(['signal_strength_cap1', 'signal_strength_cap2', 'signal_strength_cap3'], axis=1, inplace=True)

    # Drop unwanted columns
    unwanted_columns = [f'channel_cap{i}_{cap}' for i in range(1, 4) for cap in unique_channels]
    X.drop(unwanted_columns, axis=1, inplace=True)

    return X

def preprocess_data_dataset(df):
    """
    Separates trainingData into Features and Targets
    Will also be applied to validationData

    INPUT: Cleaned trainingData DataFrame
    OUTPUT: trainingData as Features and Targets
    """
    # split the data set into features and targets(Floor and BuildingID)
    X = df.drop(['floorid'], axis=1)
    y = df[['floorid']]

    # Extract unique channel values
    unique_channels = sorted(
        set(df['channel_cap1'].unique()) | set(df['channel_cap2'].unique()) | set(df['channel_cap3'].unique()))

    # Create new one-hot encoded columns
    for channel in unique_channels:
        X[f'channel_cap1_{channel}'] = (df['channel_cap1'] == channel).astype(int)
        X[f'channel_cap2_{channel}'] = (df['channel_cap2'] == channel).astype(int)
        X[f'channel_cap3_{channel}'] = (df['channel_cap3'] == channel).astype(int)

    # Drop the original 'channel_cap1', 'channel_cap2', and 'channel_cap3' columns
    X.drop(['channel_cap1', 'channel_cap2', 'channel_cap3'], axis=1, inplace=True)

    # Iterate over signal strength caps and channels to perform multiplication
    signal_columns = ['signal_strength_cap1', 'signal_strength_cap2', 'signal_strength_cap3']

    for signal_col in signal_columns:
        for channel in unique_channels:
            channel_col1 = f'channel_cap1_{channel}'
            channel_col2 = f'channel_cap2_{channel}'
            channel_col3 = f'channel_cap3_{channel}'

            if signal_col.endswith('cap1'):
                X[f'{signal_col}_{channel_col1}'] = df[signal_col] * X[channel_col1]
            elif signal_col.endswith('cap2'):
                X[f'{signal_col}_{channel_col2}'] = df[signal_col] * X[channel_col2]
            elif signal_col.endswith('cap3'):
                X[f'{signal_col}_{channel_col3}'] = df[signal_col] * X[channel_col3]

    # Drop the original 'signal_strength' columns
    X.drop(['signal_strength_cap1', 'signal_strength_cap2', 'signal_strength_cap3'], axis=1, inplace=True)

    # Drop unwanted columns
    unwanted_columns = [f'channel_cap{i}_{cap}' for i in range(1, 4) for cap in unique_channels]
    X.drop(unwanted_columns, axis=1, inplace=True)

    # create Dummies for the targets to feed into the model
    y = pd.get_dummies(data=y, columns=['floorid'])

    return X, y

def add_unique_channels(df):

    X_processed = df

    # Assuming X_processed is your existing DataFrame and X_train_cols is the list of columns in X_train
    X_train_cols = [
        "ssid",
        "signal_strength_cap1_channel_cap1_2412",
        "signal_strength_cap1_channel_cap1_2417",
        "signal_strength_cap1_channel_cap1_2422",
        "signal_strength_cap1_channel_cap1_2427",
        "signal_strength_cap1_channel_cap1_2432",
        "signal_strength_cap1_channel_cap1_2437",
        "signal_strength_cap1_channel_cap1_2442",
        "signal_strength_cap1_channel_cap1_2447",
        "signal_strength_cap1_channel_cap1_2452",
        "signal_strength_cap1_channel_cap1_2457",
        "signal_strength_cap1_channel_cap1_2462",
        "signal_strength_cap2_channel_cap2_2412",
        "signal_strength_cap2_channel_cap2_2417",
        "signal_strength_cap2_channel_cap2_2422",
        "signal_strength_cap2_channel_cap2_2427",
        "signal_strength_cap2_channel_cap2_2432",
        "signal_strength_cap2_channel_cap2_2437",
        "signal_strength_cap2_channel_cap2_2442",
        "signal_strength_cap2_channel_cap2_2447",
        "signal_strength_cap2_channel_cap2_2452",
        "signal_strength_cap2_channel_cap2_2457",
        "signal_strength_cap2_channel_cap2_2462",
        "signal_strength_cap3_channel_cap3_2412",
        "signal_strength_cap3_channel_cap3_2417",
        "signal_strength_cap3_channel_cap3_2422",
        "signal_strength_cap3_channel_cap3_2427",
        "signal_strength_cap3_channel_cap3_2432",
        "signal_strength_cap3_channel_cap3_2437",
        "signal_strength_cap3_channel_cap3_2442",
        "signal_strength_cap3_channel_cap3_2447",
        "signal_strength_cap3_channel_cap3_2452",
        "signal_strength_cap3_channel_cap3_2457",
        "signal_strength_cap3_channel_cap3_2462"
    ]

    # Iterate through each column in X_train_cols
    for col in X_train_cols:
        # Check if the column already exists in X_processed
        if col not in X_processed.columns:
            # If not, add the column and fill with 0
            X_processed[col] = 0

    # Rearrange the columns of X_train to match the order in X_train_cols
    X_processed = X_processed[X_train_cols]

    return X_processed

def split_data_dataset(preprocess_data_dataset):
    # TO AVOID OVERFITTING: Split the training data into training and testing sets
    global X_train
    global X_test
    global y_train
    global y_test

    X_train, X_test, y_train, y_test = train_test_split(X,
                                                        y,
                                                        test_size=0.20,
                                                        random_state=42,
                                                        shuffle=True)

    # Show the results of the split
    print("Training set has {} samples.".format(X_train.shape[0]))
    print("Testing set has {} samples.".format(X_test.shape[0]))
    return X_train, X_test, y_train, y_test
# Main function
def main():

    # Load the dataset
    #file_path = r'C:\Users\Thesis2.0\django_thesis\KNN Algorithm\ap_data_processed.csv'
    #trainingData = pd.read_csv(file_path)


    while True:  # Run forever

    # SCANNED DATA /////////////////////////////////////////////////////////////////////////////////////////////////////

        # Load scanned data
        scanned_data_cap1 = r'C:\Users\Thesis2.0\django_thesis\Position Scanner\scanned_aps_cap1.csv'
        scanned_data_cap2 = r'C:\Users\Thesis2.0\django_thesis\Position Scanner\scanned_aps_cap2.csv'
        scanned_data_cap3 = r'C:\Users\Thesis2.0\django_thesis\Position Scanner\scanned_aps_cap3.csv'

        data_cap1 = pd.read_csv(scanned_data_cap1)
        data_cap2 = pd.read_csv(scanned_data_cap2)
        data_cap3 = pd.read_csv(scanned_data_cap3)

        # Combine data from cap1, cap2, and cap3
        combined_data = pd.concat([data_cap1, data_cap2, data_cap3], ignore_index=True)
        validationData = pd.concat([data_cap1, data_cap2, data_cap3], ignore_index=True)

        #'''
        # Add a new column 'source_without_C' by removing 'C' from 'source'
        combined_data['ssid'] = combined_data['ssid'].str.replace('C', '')

        # Convert the 'source_without_C' column to numeric
        combined_data['ssid'] = pd.to_numeric(combined_data['ssid'], errors='coerce')
        #'''

        # Print or use the combined_data DataFrame as needed
        print(combined_data)

        # Replace 'output_file.csv' with the desired file name
        output_file = 'scan1_combined_data.csv'

        # Save the DataFrame to a CSV file
        combined_data.to_csv(output_file, index=False)

    # ARRANGED DATA /////////////////////////////////////////////////////////////////////////////////////////////////////

        # Assuming 'ap_data' is a DataFrame containing your data

        # Find unique SSIDs in the dataset
        unique_ssids = combined_data['ssid'].unique()

        print('unique ssids: ', unique_ssids)

        # Initialize an empty list to store dictionaries
        combinations_list = []

        # Iterate over unique SSIDs and extract unique channels for each 'cap' category
        for ssid in unique_ssids:
            # Filter data for the specific SSID
            specific_ssid_data = combined_data[combined_data['ssid'] == ssid]

            # Filter data for each 'cap' category within the specific SSID
            cap1_data = specific_ssid_data[specific_ssid_data['source'] == 'cap1']
            cap2_data = specific_ssid_data[specific_ssid_data['source'] == 'cap2']
            cap3_data = specific_ssid_data[specific_ssid_data['source'] == 'cap3']

            # Extract unique channels for each 'cap' category within the specific SSID
            unique_channels_cap1 = cap1_data['channel'].unique()
            unique_channels_cap2 = cap2_data['channel'].unique()
            unique_channels_cap3 = cap3_data['channel'].unique()

            # Generate all combinations of unique channels
            all_combinations = product(unique_channels_cap1, unique_channels_cap2, unique_channels_cap3)

            # Append combinations to the list
            for combination in all_combinations:
                combinations_list.append({
                    'mac_address': specific_ssid_data['mac_address'].iloc[0],
                    'ssid': ssid,
                    'timestamp': specific_ssid_data['timestamp'].iloc[0],
                    'channel_cap1': combination[0],
                    'channel_cap2': combination[1],
                    'channel_cap3': combination[2],
                    'signal_strength_cap1': cap1_data[cap1_data['channel'] == combination[0]]['signal_strength'].iloc[0],
                    'signal_strength_cap2': cap2_data[cap2_data['channel'] == combination[1]]['signal_strength'].iloc[0],
                    'signal_strength_cap3': cap3_data[cap3_data['channel'] == combination[2]]['signal_strength'].iloc[0],
                })

        # Create the DataFrame outside the loop
        combinations_df = pd.DataFrame(combinations_list, columns=[
            'mac_address', 'ssid', 'timestamp',
            'channel_cap1', 'channel_cap2', 'channel_cap3',
            'signal_strength_cap1', 'signal_strength_cap2', 'signal_strength_cap3'
        ])

        # Print the resulting DataFrame with all combinations
        print(combinations_df)

        # Replace 'output_file.csv' with the desired file name
        output_file = 'scan2_combinations_df.csv'

        # Save the DataFrame to a CSV file
        combinations_df.to_csv(output_file, index=False)

    # CLEANED DATA /////////////////////////////////////////////////////////////////////////////////////////////////////

        # Apply Cleaning

        cleaned_data = clean_data(combinations_df)

        print(cleaned_data)
        # trainingData.to_csv('trainingData.csv', index=False)

        # Replace 'output_file.csv' with the desired file name
        output_file = 'scan3_cleaned_data.csv'

        # Save the DataFrame to a CSV file
        cleaned_data.to_csv(output_file, index=False)

    # PREPROCESSED DATA /////////////////////////////////////////////////////////////////////////////////////////////////////

        # Apply preprocessing

        X_processed = preprocess_data(cleaned_data)

        print(X_processed)

        # Replace 'output_file.csv' with the desired file name
        output_file = 'scan4_preprocessed_data.csv'

        # Save the DataFrame to a CSV file
        X_processed.to_csv(output_file, index=False)

    # PREPROCESSED DATA /////////////////////////////////////////////////////////////////////////////////////////////////////

        # Apply preprocessing
        X_test = add_unique_channels(X_processed)
        print(X_test)

        # Replace 'output_file.csv' with the desired file name
        output_file = 'scan5_add_unique_channels_data.csv'

        # Save the DataFrame to a CSV file
        X_test.to_csv(output_file, index=False)

    # PREDICTED DATA /////////////////////////////////////////////////////////////////////////////////////////////////////

        # Load the dataset
        file_path = r'C:\Users\Thesis2.0\django_thesis\KNN Algorithm\combined_rssi_final.csv'
        trainingData = pd.read_csv(file_path)

        # Apply Cleaning
        trainingData = clean_data_dataset(trainingData)

        # Apply preprocessing
        X, y = preprocess_data_dataset(trainingData)

        print(X)
        print(y)

        # Apply split data
        X_train, X_test, y_train, y_test = split_data_dataset(preprocess_data_dataset)

    # Scale Data with Standard Scaler

        scaler = StandardScaler()

        # Fit only the training set
        # this will help us transform the validation data
        scaler.fit(X_train)

        # Apply transform to both the training set and the test set.
        X_train = scaler.transform(X_train)
        X_test = scaler.transform(X_test)

        k = 1  # You can adjust the value of k
        knn = KNeighborsClassifier(n_neighbors=k, p=2, metric='euclidean')  # p=2 for Euclidean metric
        knn.fit(X_train, y_train)

        # Assuming 'knn' is your trained KNN classifier
        # Assuming 'X_test' and 'y_test' are your test features and labels
        y_pred = knn.predict(X_test)

        # Calculate accuracy
        accuracy = accuracy_score(y_test, y_pred)
        print(f'Accuracy: {accuracy * 100:.2f}%')

    # See the predictions and translate them

        scanned_file_path = r'C:\Users\Thesis2.0\django_thesis\KNN Algorithm\scan5_add_unique_channels_data.csv'
        scanned_data = pd.read_csv(scanned_file_path)

        features = ['ssid'] + [
            f'signal_strength_{cap}_channel_{cap}_{channel}'
            for cap in ['cap1', 'cap2', 'cap3']
            for channel in ['2412', '2417', '2422', '2427', '2432', '2437', '2442', '2447', '2452', '2457', '2462']
        ]

        # Replace these column names with the actual column names in your dataset
        scanned_data_features = scanned_data[features]

        scanned_predictions = knn.predict(scanned_data_features)

        print(scanned_predictions)

        # Find the indices where the predicted values are 1
        indices_with_1 = np.where(scanned_predictions == 1)

        # Extract the corresponding values from the y_train column
        corresponding_values = y_train.iloc[indices_with_1]

        # Print the corresponding values
        print(corresponding_values)

        # Assuming scanned_data has 'ssid' and other relevant columns
        columns_of_interest = ['ssid']  # Include other relevant columns as needed
        scanned_data_subset = scanned_data[columns_of_interest].copy()  # Use .copy() to create a copy of the DataFrame

        # Create a new column 'timestamp' with dummy values
        scanned_data_subset['timestamp'] = pd.Timestamp.now()  # You can replace this with your desired timestamp

        # Find the indices where the predicted values are 1
        indices_with_1 = np.where(scanned_predictions == 1)[0]  # Use [0] to get the array from the tuple

        # Add the predicted 'floorid' to the scanned_data_subset
        scanned_data_subset['floorid'] = corresponding_values.idxmax(axis=1).apply(lambda x: int(x.split('_')[1]))

        # Print the final table
        print(scanned_data_subset)

        return





# Run the main function
if __name__ == "__main__":
    main()