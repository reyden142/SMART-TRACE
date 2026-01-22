# necessary Library import numpy as np
import pandas as pd
import numpy as np
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
        file_path = r'C:\Users\Thesis2.0\django_thesis\KNN Algorithm\ap_data_processed-2.csv'
        trainingData = pd.read_csv(file_path)

        '''
        trainingData = trainingData[
            ~((trainingData['channel_cap1'] == 0) & (trainingData['channel_cap2'] == 0)) &
            ~((trainingData['channel_cap1'] == 0) & (trainingData['channel_cap3'] == 0)) &
            ~((trainingData['channel_cap2'] == 0) & (trainingData['channel_cap3'] == 0))
            ]
        '''

        # Add a new column 'source_without_C' by removing 'C' from 'source'
        trainingData['ssid'] = trainingData['ssid'].str.replace('C', '')

        # Convert the 'source_without_C' column to numeric
        trainingData['ssid'] = pd.to_numeric(trainingData['ssid'], errors='coerce')

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
            df.iloc[:, 9:12] = np.where(df.iloc[:, 9:12] <= 0,
                                        df.iloc[:, 9:12] + 105,
                                        df.iloc[:, 9:12] - 100)

            df.iloc[:, 6:9] = np.where(df.iloc[:, 6:9] > 2000,
                                       df.iloc[:, 6:9] - 2300,
                                       df.iloc[:, 6:9] - 0)

            # remove selected columns...
            columns_removed = ['mac_address', 'timestamp']
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

                # print('0WAAAAAAAAAAAAAAAAAZZUUUUUUUUUUUUUUUUUUUUP')

                # Check if at least two out of the three sources are available in the 'source' column
                if len(ap_data_pivot_2['ssid']) > 6:

                    # print('1WAAAAAAAAAAAAAAAAAZZUUUUUUUUUUUUUUUUUUUUP')

                    # Check if at least two out of the three sources are available in the 'source' column
                    if sum(cap in ap_data_pivot_2['source'].unique() for cap in ['cap1', 'cap2', 'cap3']) > 1:
                        # Assuming 'ap_data_processed' is your training data DataFrame

                        # print('2WAAAAAAAAAAAAAAAAAZZUUUUUUUUUUUUUUUUUUUUP')

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

                        print('ap pivoted: ', ap_data_pivot_2)
                        ap_data_pivot_2.to_csv("ap_data_pivoted.csv", index=False)

                        '''
                        csv_file = 'ap_data_position_processed_before.csv'
                        ap_data_pivot_2.to_csv(csv_file, index=False)
                        print('export ap_data_position_processed_before file successful')
                        '''

                        # // CODE FOR THE MERGING OF ROWS //////////////////////////////////////////////////////////////////////////////////////////////////

                        # Define the column names to check
                        columns_to_check_1 = ['signal_strength_cap1', 'signal_strength_cap2', 'signal_strength_cap3']

                        # Check if any of the columns is missing
                        missing_columns_1 = set(columns_to_check_1).difference(ap_data_pivot_2.columns)

                        # If missing, add the columns with a default value (e.g., 100)
                        for column in missing_columns_1:
                            ap_data_pivot_2[column] = 100

                        print('FIRST IF: ', ap_data_pivot_2)

                        # Define the column names to check
                        columns_to_check_2 = ['channel_cap1', 'channel_cap2', 'channel_cap3']

                        # Check if any of the columns is missing
                        missing_columns_2 = set(columns_to_check_2).difference(ap_data_pivot_2.columns)

                        # If missing, add the columns with a default value (e.g., 0)
                        for column in missing_columns_2:
                            ap_data_pivot_2[column] = 0

                        print('SECOND IF: ', ap_data_pivot_2)

                        # Replace missing signal_strength values with 100 for all caps
                        for cap in range(1, 3):
                            signal_strength_column = f'signal_strength_cap{cap}'
                            ap_data_pivot_2[signal_strength_column].fillna(100, inplace=True)

                        # Replace missing channel values with 0 for all caps
                        for cap in range(1, 3):
                            channel_column = f'channel_cap{cap}'
                            ap_data_pivot_2[channel_column].fillna(0, inplace=True)

                            time.sleep(1)

                        csv_file = 'ap_data_position_processed_merged-pivoted.csv'
                        ap_data_pivot_2.to_csv(csv_file, index=False)
                        print('export ap_data_position_processed_merged-pivoted file successful')

                        # Group by 'ssid', 'mac_address', and 'timestamp', and aggregate the values
                        ap_data_pivot_2 = ap_data_pivot_2.groupby(['ssid']).agg({
                            'mac_address': 'first',
                            'timestamp': 'first',
                            'channel_cap1': 'first',
                            'channel_cap2': 'first',
                            'channel_cap3': 'first',
                            'signal_strength_cap1': 'first',
                            'signal_strength_cap2': 'first',
                            'signal_strength_cap3': 'first'
                        }).reset_index()
                        # cap1_channel	cap2_channel	cap3_channel	cap1_signal_strength	cap2_signal_strength	cap3_signal_strength

                        # Print the resulting DataFrame
                        print(ap_data_pivot_2)

                        # '''
                        csv_file = 'ap_data_position_processed_before-aggregate.csv'
                        ap_data_pivot_2.to_csv(csv_file, index=False)
                        print('export ap_data_position_processed_before-aggregate file successful')
                        # '''

                        print('ap_data_position_processed_before-aggregate', ap_data_pivot_2)

                        # Save the DataFrame to a CSV file
                        '''
                        csv_file = 'ap_data_position_processed_before.csv'
                        ap_data_pivot_2.to_csv(csv_file, index=False)
                        print('export csv file successful')
                        '''

                        '''
                        # Remove rows if there are two zeroes in a row in the cap_channel
                        ap_data_pivot_2 = ap_data_pivot_2[
                            ~((ap_data_pivot_2['channel_cap1'] == 0) & (ap_data_pivot_2['channel_cap2'] == 0)) &
                            ~((ap_data_pivot_2['channel_cap1'] == 0) & (ap_data_pivot_2['channel_cap3'] == 0)) &
                            ~((ap_data_pivot_2['channel_cap2'] == 0) & (ap_data_pivot_2['channel_cap3'] == 0))
                            ]
                        '''

                        # Save the DataFrame to a CSV file
                        '''
                        csv_file = 'ap_data_position_processed_before.csv'
                        ap_data_pivot_2.to_csv(csv_file, index=False)
                        print('export csv file successful')
                        '''

                        # '''
                        csv_file = 'ap_data_position_processed.csv'
                        ap_data_pivot_2.to_csv(csv_file, index=False)
                        print('export ap_data_position_processed.csv successful')
                        # '''

                        # If CAP1, CAP2, and CAP3 are available
                        # csv_file = 'ap_data_position_processed.csv'
                        # csv_file_2 = 'ap_data_position.csv'

                        print('WAAAAAAAAAAAAAAAAAZZUUUUUUUUUUUUUUUUUUUUP')

                        # Check if the CSV file exists
                        if os.path.exists(csv_file):
                            # Read the CSV file into a DataFrame
                            ap_data_pivot_2 = pd.read_csv(csv_file)

                            # Replace missing signal_strength values with 100
                            ap_data_pivot_2['signal_strength_cap1'].fillna(100, inplace=True)
                            ap_data_pivot_2['signal_strength_cap2'].fillna(100, inplace=True)
                            ap_data_pivot_2['signal_strength_cap3'].fillna(100, inplace=True)

                            # Replace missing channel values with 0
                            ap_data_pivot_2['channel_cap1'].fillna(0, inplace=True)
                            ap_data_pivot_2['channel_cap2'].fillna(0, inplace=True)
                            ap_data_pivot_2['channel_cap3'].fillna(0, inplace=True)

                            print('ap_data_pivot_2', ap_data_pivot_2)
                            # Replace these column names with the actual column names in your dataset

                            features = ['ssid', 'signal_strength_cap1', 'signal_strength_cap2', 'signal_strength_cap3',
                                        'channel_cap1', 'channel_cap2', 'channel_cap3']
                            target = 'floorid'

                            # Add a new column 'source_without_C' by removing 'C' from 'source'
                            ap_data_pivot_2['ssid'] = ap_data_pivot_2['ssid'].str.replace('C', '')

                            # Convert the 'source_without_C' column to numeric
                            ap_data_pivot_2['ssid'] = pd.to_numeric(ap_data_pivot_2['ssid'], errors='coerce')

                            '''
                            csv_file = 'ap_data_position_processed-scanned.csv'
                            ap_data_pivot_2.to_csv(csv_file, index=False)
                            print('export ap_data_position_processed-scanned.csv successful')
                            '''

                            # Select features (X) and target variable (y)
                            X = trainingData[features]
                            y = trainingData[target]

                            # Split the data into training and testing sets
                            X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

                            # Apply split data
                            X_train, X_test, y_train, y_test = split_data(preprocess_data)

                            # Scale Data with Standard Scaler

                            scaler = StandardScaler()

                            # Fit only the training set
                            # this will help us transform the validation data
                            scaler.fit(X_train)

                            # Apply transform to both the training set and the test set.
                            X_train = scaler.transform(X_train)
                            X_test = scaler.transform(X_test)

                            # Train a k-Nearest Neighbors classifier with Euclidean metric
                            k = 1  # You can adjust the value of k
                            knn = KNeighborsClassifier(n_neighbors=k, p=2,
                                                       metric='euclidean')  # p=2 for Euclidean metric
                            knn.fit(X_train, y_train)

                            # Assuming 'combined_scanned_data' is your scanned data DataFrame
                            # Replace these column names with the actual column names in your dataset
                            scanned_data_features = ap_data_pivot_2[features]

                            # Make predictions on the scanned data
                            predictions = knn.predict(scanned_data_features)

                            # Add predicted floorid to the scanned data
                            ap_data_pivot_2['predicted_floorid'] = predictions

                            print('IM HEREEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE')

                            # Assuming 'knn' is your trained KNN classifier
                            # Assuming 'X_test' and 'y_test' are your test features and labels
                            y_pred = knn.predict(X_test)

                            # Calculate accuracy
                            #accuracy = accuracy_score(y_test, y_pred)

                            #print(f'Accuracy: {accuracy * 100:.2f}%')

                            # Add 'C' back to the 'source_without_C' column
                            ap_data_pivot_2['ssid'] = 'C' + ap_data_pivot_2['ssid'].astype(str)

                            # If you want to replace the existing 'ssid' column with the one containing 'C'
                            ap_data_pivot_2['ssid'] = ap_data_pivot_2['ssid']

                            # Drop the temporary 'source_with_C' column if you don't need it anymore
                            # ap_data_pivot_2.drop('ssid', axis=1, inplace=True)

                            # Display the predicted floorid for the scanned data
                            print(ap_data_pivot_2[['mac_address', 'ssid', 'predicted_floorid']])

                            # Group by 'ssid' and aggregate values
                            aggregated_data = ap_data_pivot_2.groupby('ssid').agg({
                                'mac_address': 'first',
                                'timestamp': 'first',
                                'predicted_floorid': 'first'
                            }).reset_index()

                            # '''
                            # Save the aggregated DataFrame to a new CSV file
                            predicted_values_file = 'predicted_values_aggregated.csv'
                            aggregated_data.to_csv(predicted_values_file, index=False)
                            # '''

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
                            result_df = pd.merge(aggregated_data, markers_df, left_on='predicted_floorid',
                                                 right_on='title',
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
                        main()

                    #Remove all data from the ap_data_position table
                    delete_query = "DELETE FROM `ap_data_position`"
                    with conn.cursor() as cursor:
                        cursor.execute(delete_query)
                    conn.commit()
                    print("All data removed from 'ap_data_position' table.")

                else:
                    print("Not enough unique sources in the 'source' column.")
            except Exception as e:
                print(f"Error: {e}")
            finally:
                # Close the database connection
                conn.close()
        else:
            print("Failed to connect to the database")

        time.sleep(5)


# Run the main function
if __name__ == "__main__":
    main()