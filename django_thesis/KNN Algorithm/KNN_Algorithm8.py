# necessary Libraries
import numpy as np
import pandas as pd
import time
from datetime import datetime
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
    '''
    df.iloc[:, 5:8] = np.where(df.iloc[:, 5:8] <= 0,
                               df.iloc[:, 5:8] + 105,
                               df.iloc[:, 5:8] - 100)
    '''

    '''
    df.iloc[:, 6:9] = np.where(df.iloc[:, 6:9] > 2000, 
                df.iloc[:, 6:9] - 2300, 
                df.iloc[:, 6:9] - 0)
    '''

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
    '''

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
    X = df.drop(['floorid', 'roomid'], axis=1)
    y = df[['floorid', 'roomid']]

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
    y = pd.get_dummies(data=y, columns=['floorid', 'roomid'])

    return X, y

def add_unique_channels(df, X_train):

    X_processed = df

    # Assuming X_processed is your existing DataFrame and X_train_cols is the list of columns in X_train
    X_train_cols = X_train.columns  # Get the columns of X_train

    # Iterate through each column in X_train_cols
    for col in X_train_cols:
        # Check if the column already exists in X_processed
        if col not in X_processed.columns:
            # If not, add the column and fill with 0
            X_processed[col] = 0

    # Rearrange the columns of X_train to match the order in X_train_cols
    X_processed = X_processed[X_train_cols]

    return X_processed

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

    # TRAINED DATA /////////////////////////////////////////////////////////////////////////////////////////////////////

        # Load the dataset
        file_path = r'C:\Users\Thesis2.0\django_thesis\KNN Algorithm\combined_rssi_final.csv'
        trainingData = pd.read_csv(file_path)

        # Apply Cleaning
        trainingData = clean_data_dataset(trainingData)

        # Apply preprocessing
        X_train, y_train = preprocess_data_dataset(trainingData)

        print(X_train)
        print(y_train)

        X_train.to_csv('X_train.csv', index=False)
        y_train.to_csv('y_train.csv', index=False)


    # PREPROCESSED DATA /////////////////////////////////////////////////////////////////////////////////////////////////////

        # Apply preprocessing
        X_test = add_unique_channels(X_processed, X_train)
        print('X_test', X_test)

        # Replace 'output_file.csv' with the desired file name
        output_file = 'scan5_add_unique_channels_data.csv'

        # Save the DataFrame to a CSV file
        X_test.to_csv(output_file, index=False)

    # PREDICTED DATA /////////////////////////////////////////////////////////////////////////////////////////////////////

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
        y_test = knn.predict(X_test)

    # See the predictions and translate them

        print(y_test)

        # Assuming 'y_train' is your true labels array
        column_names = y_train.columns

        for i, y_pred in enumerate(y_test):
            print(f"\nPrediction {i + 1}:")
            print(f"Column names with 'True' predictions:")

            # Find the indices where the value is True for the current prediction
            true_indices = np.where(y_pred)[0]

            # Remove the 'floorid_' prefix when printing the column names
            true_column_names = [column.replace('floorid_', '').replace('roomid_', '') for column in column_names[true_indices]]
            print("Column names with 'True' predictions:", true_column_names)

            # Add predicted floorid to the scanned data
            cleaned_data.loc[cleaned_data.index[i], 'predicted_floorid'] = true_column_names[0]
            cleaned_data.loc[cleaned_data.index[i], 'predicted_roomid'] = true_column_names[1]
            current_timestamp = datetime.now()  # Capture the current timestamp

        # Extract columns [ssid, predicted_floorid, predicted_roomid]
        selected_columns = cleaned_data[['ssid', 'predicted_floorid', 'predicted_roomid']]

        # Create a new DataFrame named 'final_predictions'
        final_predictions = pd.DataFrame(selected_columns)

        print('Final Predictions: ')
        print(final_predictions)

        # Count occurrences of each room ID
        roomid_counts = final_predictions['predicted_roomid'].value_counts()

        # Count occurrences of specific rooms and floors
        be111_count = roomid_counts.get('111', 0)
        be213_count = roomid_counts.get('213', 0)
        be214_count = roomid_counts.get('214', 0)
        first_floor_count = be111_count
        second_floor_count = be213_count + be214_count
        total_persons = roomid_counts.sum()

        # Create a DataFrame for the counts
        counts_df = pd.DataFrame({
            'RoomID': ['BE111', 'BE213', 'BE214', 'First Floor', 'Second Floor', 'Total'],
            'Count': [be111_count, be213_count, be214_count, first_floor_count, second_floor_count, total_persons]
        })

        # Save the DataFrame to a CSV file
        csv_file_path = 'C:/Users/Thesis2.0/django_thesis/rfid_ips/css/room_counts.csv'
        counts_df.to_csv(csv_file_path, index=False)

        print(f'Room Count CSV file saved at: {csv_file_path}')

        '''
        # Group by 'ssid' and aggregate values
        aggregated_data = cleaned_data.groupby('ssid').agg({
            'timestamp': 'first',
            'predicted_floorid': 'first'
        }).reset_index()

        print('aggregated_data: ', aggregated_data)
        '''
    # CSV FILE /////////////////////////////////////////////////////////////////////////////////////////////////////

        connection = connect_to_database()

        cursor = connection.cursor()

        # Query to fetch latitude and longitude from the "markers" table
        query = "SELECT title, lat, lng FROM markers_combined"
        # Execute the query and fetch the results

        cursor.execute(query)
        markers_data = cursor.fetchall()

        # Create a DataFrame from the results
        markers_df = pd.DataFrame(markers_data, columns=['title', 'lat', 'lng'])

        # Convert 'predicted_floorid' to object type in the aggregated_data DataFrame
        final_predictions['predicted_floorid'] = final_predictions['predicted_floorid'].astype(str)

        # Merge the aggregated_data DataFrame with markers_df based on 'predicted_floorid'
        result_df = pd.merge(final_predictions, markers_df, left_on='predicted_floorid', right_on='title',
                             how='left')

        # Drop the duplicate 'title' column
        result_df = result_df.drop(columns=['title'])

        # Create empty DataFrames to accumulate rows based on the condition
        df_map1 = pd.DataFrame()
        df_map2 = pd.DataFrame()

        for i, row in result_df.iterrows():
            # Convert 'predicted_roomid' column to integers
            row['predicted_roomid'] = int(row['predicted_roomid'])

            # Check if the value in the 'predicted_roomid' column is equal to 111
            if row['predicted_roomid'] == 111:
                # Append the current row to df_map2
                df_map2 = pd.concat([df_map2, row.to_frame().T], ignore_index=True)
            else:
                # Append the current row to df_map1
                df_map1 = pd.concat([df_map1, row.to_frame().T], ignore_index=True)

        # Specify the full paths for the CSV files
        csv_file_path_map1 = 'C:/Users/Thesis2.0/django_thesis/rfid_ips/css/final_predicted_values_aggregated_map1.csv'
        csv_file_path_map2 = 'C:/Users/Thesis2.0/django_thesis/rfid_ips/css/final_predicted_values_aggregated_map2.csv'

        # Save the accumulated DataFrames to the specified CSV files
        df_map1.to_csv(csv_file_path_map1, index=False)
        df_map2.to_csv(csv_file_path_map2, index=False)

        print("Latitude and longitude added successfully.")

        time.sleep(1)


# Run the main function
if __name__ == "__main__":
    main()