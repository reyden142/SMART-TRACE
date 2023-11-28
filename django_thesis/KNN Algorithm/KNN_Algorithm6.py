# necessary Libraries
import numpy as np
import pandas as pd
import time
import pprint

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


# Main function
def main():
    while True:  # Run forever
        # Load the dataset
        file_path = r'C:\Users\Thesis2.0\django_thesis\KNN Algorithm\ap_data_processed.csv'
        trainingData = pd.read_csv(file_path)

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
            df.iloc[:, 9:11] = np.where(df.iloc[:, 9:11] <= 0,
                                        df.iloc[:, 9:11] + 105,
                                        df.iloc[:, 9:11] - 100)

            # remove selected columns...
            columns_removed = ['mac_address', 'ssid', 'timestamp']
            # cap1_channel	cap2_channel	cap3_channel	cap1_signal_strength	cap2_signal_strength	cap3_signal_strength

            for col in columns_removed:
                df.drop(col, axis=1, inplace=True)

            # Return the cleaned dataframe.
            return df

        # Apply Cleaning

        trainingData = clean_data(trainingData)

        def preprocess_data(df):
            """
            Separates trainingData into Features and Targets
            Will also be applied to validationData

            INPUT: Cleaned trainingData DataFrame
            OUTPUT: trainingData as Features and Targets
            """

            global X
            global y
            # split the data set into features and targets(Floor and BuildingID)
            X = df.drop(['longitude', 'latitude', 'floorid'], axis=1)
            y = df[['floorid']]

            # create Dummies for the targets to feed into the model
            y = pd.get_dummies(data=y, columns=['floorid'])

            return X, y

        # Apply preprocessing

        X, y = preprocess_data(trainingData)

        def split_data(preprocess_data):
            # TO AVOID OVERFITTING: Split the training data into training and testing sets
            global X_train
            global X_test
            global y_train
            global y_test

            X_train, X_test, y_train, y_test = train_test_split(X,
                                                                y,
                                                                test_size=0.2,
                                                                random_state=42,
                                                                shuffle=True)

            # Show the results of the split
            print("Training set has {} samples.".format(X_train.shape[0]))
            print("Testing set has {} samples.".format(X_test.shape[0]))
            return X_train, X_test, y_train, y_test

        # Apply split data

        X_train, X_test, y_train, y_test = split_data(preprocess_data)



        # SCANNED DATA /////////////////////////////////////////////////////////////////////////////////////////////////////

        # Connect to the database
        conn = connect_to_database()

        cursor = conn.cursor()

        # Check if the connection is successful
        if conn is not None:
            # Extract data to the database
            try:
                # Replace 'your_query' with the SQL query to select the data you want
                your_query = "SELECT * FROM `ap_data_position`"

                # Use pandas to read the query result into a DataFrame
                df = pd.read_sql_query(your_query, conn)

                # Replace 'output_file.csv' with the desired file name
                ap_data_position = 'ap_data_position.csv'

                # Save the DataFrame to a CSV file
                df.to_csv(ap_data_position, index=False)

                print(f"Data extracted and saved to {ap_data_position}")

                # Read the CSV file into a new DataFrame
                ap_data_pivot_2 = pd.read_csv(ap_data_position)

                print('ap_data_position', ap_data_pivot_2)

                # Check if 'cap1', 'cap2', and 'cap3' are available in the 'source' column
                if sum(cap in ap_data_pivot_2['source'].unique() for cap in ['cap1', 'cap2', 'cap3']) >= 2:
                    # Assuming 'ap_data_processed' is your training data DataFrame

                    # Select relevant columns
                    selected_columns = ['source', 'channel', 'signal_strength', 'mac_address', 'ssid', 'timestamp']
                    ap_data_selected = ap_data_pivot_2[selected_columns]

                    # Pivot the DataFrame to create separate columns for each 'cap'
                    ap_data_pivot_2 = ap_data_selected.pivot_table(
                        index=['mac_address', 'ssid', 'timestamp'],
                        columns='source',
                        values=['channel', 'signal_strength'],
                        aggfunc='first'
                    ).reset_index()

                    # Flatten the MultiIndex columns
                    ap_data_pivot_2.columns = [f'{col[0]}_{col[1]}' if col[1] else col[0] for col in
                                               ap_data_pivot_2.columns]

                    # Rename columns for clarity
                    ap_data_pivot_2.columns = [
                        'mac_address', 'ssid', 'timestamp',
                        'cap1_channel', 'cap2_channel', 'cap3_channel',
                        'cap1_signal_strength', 'cap2_signal_strength', 'cap3_signal_strength'
                    ]

                    # Replace missing signal_strength values with 100
                    ap_data_pivot_2['cap1_signal_strength'].fillna(100, inplace=True)
                    ap_data_pivot_2['cap2_signal_strength'].fillna(100, inplace=True)
                    ap_data_pivot_2['cap3_signal_strength'].fillna(100, inplace=True)

                    # Replace missing channel values with 0
                    ap_data_pivot_2['cap1_channel'].fillna(0, inplace=True)
                    ap_data_pivot_2['cap2_channel'].fillna(0, inplace=True)
                    ap_data_pivot_2['cap3_channel'].fillna(0, inplace=True)

                    print('ap_data_pivot_2', ap_data_pivot_2)

                    # Save the DataFrame to a CSV file
                    '''
                    csv_file = 'ap_data_position_processed_before.csv'
                    ap_data_pivot_2.to_csv(csv_file, index=False)
                    print('export csv file successful')
                    '''

                    # Remove rows if there are two zeroes in a row in the cap_channel
                    '''
                    ap_data_pivot_2 = ap_data_pivot_2[
                        ~((ap_data_pivot_2['cap1_channel'] == 0) & (ap_data_pivot_2['cap2_channel'] == 0)) &
                        ~((ap_data_pivot_2['cap1_channel'] == 0) & (ap_data_pivot_2['cap3_channel'] == 0)) &
                        ~((ap_data_pivot_2['cap2_channel'] == 0) & (ap_data_pivot_2['cap3_channel'] == 0))
                        ]
                    '''

                    # Save the DataFrame to a CSV file
                    ap_data_pivot_2.to_csv('ap_data_position_processed.csv', index=False)

                    # Print the resulting DataFrame
                    print(ap_data_pivot_2.head())

                    # If CAP1, CAP2, and CAP3 are available
                    csv_file = 'ap_data_position_processed.csv'
                    # csv_file_2 = 'ap_data_position.csv'

                    # Check if the CSV file exists
                    if os.path.exists(csv_file):
                        # Read the CSV file into a DataFrame
                        ap_data_pivot_2 = pd.read_csv(csv_file)

                        # Replace these column names with the actual column names in your dataset

                        features = ['cap1_signal_strength', 'cap2_signal_strength', 'cap3_signal_strength',
                                    'cap1_channel', 'cap2_channel', 'cap3_channel']
                        target = 'floorid'

                        # Clean data
                        validationData = clean_data(ap_data_pivot_2)

                        # preprocess
                        X_valid, y_valid = preprocess_data(validationData)

                        # scale
                        X_valid = scaler.transform(X_valid)

                        # pca
                        X_valid_pca = pca.transform(X_valid)

                        print("Number of PCA Components = {}.".format(pca.n_components_))

                        print("Total Variance Explained by PCA Components = {}.".format(
                            pca.explained_variance_ratio_.sum()))

                        # Convert to sparse matrix
                        X_valid_pca = lil_matrix(X_valid_pca).toarray()
                        y_valid = lil_matrix(y_valid).toarray()

                        # predict mlknn =1
                        valid_predictions = MLKNN_1_classifier.predict(X_valid_pca)

                        # accuracy
                        print("Accuracy = ", accuracy_score(y_valid, valid_predictions))

                        # Split the data into training and testing sets
                        #X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

                        # Group by 'ssid' and aggregate values
                        ap_data_pivot_2 = ap_data_pivot_2.groupby('ssid').agg({
                            'mac_address': 'first',
                            'timestamp': 'first',
                            'cap1_signal_strength': 'first',
                            'cap2_signal_strength': 'first',
                            'cap3_signal_strength': 'first',
                            'cap1_channel': 'first',
                            'cap2_channel': 'first',
                            'cap3_channel': 'first'
                        }).reset_index()

                        # Assuming 'combined_scanned_data' is your scanned data DataFrame
                        # Replace these column names with the actual column names in your dataset
                        scanned_data_features = ap_data_pivot_2[features]

                        # Make predictions on the scanned data
                        #predictions = knn.predict(scanned_data_features)

                        # Group by 'ssid' and aggregate values
                        aggregated_data = ap_data_pivot_2.groupby('ssid').agg({
                            'mac_address': 'first',
                            'timestamp': 'first',
                            'predicted_floorid': 'first'
                        }).reset_index()

                        '''
                        # Save the aggregated DataFrame to a new CSV file
                        predicted_values_file = 'predicted_values_aggregated.csv'
                        aggregated_data.to_csv(predicted_values_file, index=False)
                        '''

                        # Apply PCA while keeping 95% of the variation in the data
                        pca = PCA(.95)

                        # Fit only the training set
                        pca.fit(X_train)

                        # Apply PCA transform to both the training set and the test set.
                        X_train_pca = pca.transform(X_train)
                        X_test_pca = pca.transform(X_test)

                        print("Number of PCA Components = {}.".format(pca.n_components_))
                        # print(pca.n_components_)
                        print("Total Variance Explained by PCA Components = {}.".format(
                            pca.explained_variance_ratio_.sum()))
                        # print(pca.explained_variance_ratio_.sum())

                        # Create sparse matrices to run the scikit multilearn algorithms

                        X_train_pca = lil_matrix(X_train_pca).toarray()
                        y_train = lil_matrix(y_train).toarray()
                        X_test_pca = lil_matrix(X_test_pca).toarray()
                        y_test = lil_matrix(y_test).toarray()

                        start_time = time.time()

                        MLKNN_1_classifier = MLkNN(k=1)

                        # train
                        MLKNN_1_classifier.fit(X_train_pca, y_train)

                        # run predictions
                        # predict mlknn =1
                        predictions = MLKNN_1_classifier.predict(X_test_pca)

                        # Add predicted floorid to the scanned data
                        ap_data_pivot_2['predicted_floorid'] = predictions

                        # Convert to sparse matrix
                        X_valid_pca = lil_matrix(X_valid_pca).toarray()
                        y_valid = lil_matrix(y_valid).toarray()

                        y_pred = MLKNN_1_classifier.predict(X_valid_pca)

                        # Display the predicted floorid for the scanned data
                        print(ap_data_pivot_2[['mac_address', 'ssid', 'predicted_floorid']])

                        # accuracy
                        print("Accuracy = ", accuracy_score(y_test, y_valid))

                        print("--- Run time: %s mins ---" % np.round(((time.time() - start_time) / 60), 2))

                        # Query to fetch latitude and longitude from the "markers" table
                        query = "SELECT title, lat, lng FROM markers"
                        # Execute the query and fetch the results

                        cursor.execute(query)
                        markers_data = cursor.fetchall()

                        # Create a DataFrame from the results
                        markers_df = pd.DataFrame(markers_data, columns=['title', 'lat', 'lng'])

                        # Convert 'predicted_floorid' to object type in the aggregated_data DataFrame
                        aggregated_data['predicted_floorid'] = aggregated_data['predicted_floorid'].astype(str)



                        # Merge the aggregated_data DataFrame with markers_df based on 'predicted_floorid'
                        result_df = pd.merge(aggregated_data, markers_df, left_on='predicted_floorid', right_on='title',
                                             how='left')

                        # Drop the duplicate 'title' column
                        result_df = result_df.drop(columns=['title'])

                        # Save the final DataFrame to a new CSV file
                        # Specify the full path where you want to save the CSV file
                        csv_file_path = 'C:/Users/Thesis2.0/django_thesis/rfid_ips/css/final_predicted_values_aggregated.csv'

                        # Save the final DataFrame to the specified CSV file
                        result_df.to_csv(csv_file_path, index=False)

                        print("Latitude and longitude added successfully.")

                        # After processing, delete the CSV file
                        # os.remove(csv_file)
                        # print(f"CSV file '{csv_file}' deleted.")

                    else:
                        print("Missing 'cap1', 'cap2', or 'cap3' in the 'source' column.")

                else:
                    print(f"CSV file '{csv_file}' not found.")

                # Remove all data from the ap_data_position table
                # delete_query = "DELETE FROM `ap_data_position`"
                # with conn.cursor() as cursor:
                #    cursor.execute(delete_query)
                # conn.commit()
                # print("All data removed from 'ap_data_position' table.")

            except Exception as e:
                print(f"Error: {e}")
            finally:
                # Close the database connection
                conn.close()
        else:
            print("Failed to connect to the database")

# Run the main function
if __name__ == "__main__":
    main()