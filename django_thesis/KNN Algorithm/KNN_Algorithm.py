#necessary Libraries
import numpy as np
import pandas as pd
import time
import pprint

#Visualizations
import matplotlib.pyplot as plt
import seaborn as sns
from pandas.plotting import scatter_matrix
# magic word for producing visualizations in notebook
%matplotlib inline

#Preprocessing
from sklearn import preprocessing
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
from sklearn.decomposition import PCA
from scipy.sparse import lil_matrix

#Models
from sklearn.model_selection import train_test_split
from sklearn.neighbors import KNeighborsClassifier

#Scoring Metrics
from sklearn.model_selection import GridSearchCV
import sklearn.metrics as metrics
from sklearn.metrics import f1_score, fbeta_score
from sklearn.metrics import accuracy_score

# Load the dataset
file_path = r'C:\Users\Thesis2.0\django_thesis\KNN Algorithm\ap_data_2.csv'
ap_data = pd.read_csv(file_path)

# Select relevant columns
selected_columns = ['source', 'channel', 'signal_strength', 'mac_address', 'ssid', 'timestamp', 'floorid', 'latitude', 'longitude']
ap_data_selected = ap_data[selected_columns]

# Pivot the DataFrame to create separate columns for each 'cap'
ap_data_pivot = ap_data_selected.pivot_table(
    index=['mac_address', 'ssid', 'timestamp', 'floorid', 'latitude', 'longitude'],
    columns='source',
    values=['channel', 'signal_strength'],
    aggfunc='first'
).reset_index()

# Flatten the MultiIndex columns
ap_data_pivot.columns = [f'{col[0]}_{col[1]}' if col[1] else col[0] for col in ap_data_pivot.columns]

# Rename columns for clarity
ap_data_pivot.columns = [
    'mac_address', 'ssid', 'timestamp', 'floorid', 'latitude', 'longitude',
    'cap1_channel', 'cap2_channel', 'cap3_channel',
    'cap1_signal_strength', 'cap2_signal_strength', 'cap3_signal_strength'
]

# Replace missing signal_strength values with 100
ap_data_pivot['cap1_signal_strength'].fillna(100, inplace=True)
ap_data_pivot['cap2_signal_strength'].fillna(100, inplace=True)
ap_data_pivot['cap3_signal_strength'].fillna(100, inplace=True)

# Replace missing channel values with 0
ap_data_pivot['cap1_channel'].fillna(0, inplace=True)
ap_data_pivot['cap2_channel'].fillna(0, inplace=True)
ap_data_pivot['cap3_channel'].fillna(0, inplace=True)

# Save the DataFrame to a CSV file
ap_data_pivot.to_csv('ap_data_processed.csv', index=False)

# Print the resulting DataFrame
print(ap_data_pivot.head())

# Select relevant columns
selected_columns = ['source', 'channel', 'signal_strength', 'mac_address', 'ssid', 'timestamp', 'floorid', 'latitude', 'longitude']
ap_data_selected = ap_data[selected_columns]

# Pivot the DataFrame to create separate columns for each 'cap'
ap_data_pivot = ap_data_selected.pivot_table(
    index=['mac_address', 'ssid', 'timestamp', 'floorid', 'latitude', 'longitude'],
    columns='source',
    values=['channel', 'signal_strength'],
    aggfunc='first'
).reset_index()

# Flatten the MultiIndex columns
ap_data_pivot.columns = [f'{col[0]}_{col[1]}' if col[1] else col[0] for col in ap_data_pivot.columns]

# Rename columns for clarity
ap_data_pivot.columns = [
    'mac_address', 'ssid', 'timestamp', 'floorid', 'latitude', 'longitude',
    'cap1_channel', 'cap2_channel', 'cap3_channel',
    'cap1_signal_strength', 'cap2_signal_strength', 'cap3_signal_strength'
]

# PROCESSED DATA /////////////////////////////////////////////////////////////////////////////////////////////////////

# Replace missing signal_strength values with 100
ap_data_pivot['cap1_signal_strength'].fillna(100, inplace=True)
ap_data_pivot['cap2_signal_strength'].fillna(100, inplace=True)
ap_data_pivot['cap3_signal_strength'].fillna(100, inplace=True)

# Replace missing channel values with 0
ap_data_pivot['cap1_channel'].fillna(0, inplace=True)
ap_data_pivot['cap2_channel'].fillna(0, inplace=True)
ap_data_pivot['cap3_channel'].fillna(0, inplace=True)

# Save the DataFrame to a CSV file
ap_data_pivot.to_csv('ap_data_processed.csv', index=False)

# Print the resulting DataFrame
print(ap_data_pivot.head())

# Load the dataset
ap_data_processed_data = r'C:\Users\pc\Desktop\Thesis\Untitled Folder 1\ap_data_processed.csv'
ap_data_processed = pd.read_csv(ap_data_processed_data)

# SCANNED DATA /////////////////////////////////////////////////////////////////////////////////////////////////////

# Load scanned data
scanned_data_cap1 = r'C:\Users\Thesis2.0\django_thesis\KNN Algorithm\scanned_aps_cap1.csv'
scanned_data_cap2 = r'C:\Users\Thesis2.0\django_thesis\KNN Algorithm\scanned_aps_cap2.csv'
scanned_data_cap3 = r'C:\Users\Thesis2.0\django_thesis\KNN Algorithm\scanned_aps_cap3.csv'

data_cap1 = pd.read_csv(scanned_data_cap1)
data_cap2 = pd.read_csv(scanned_data_cap2)
data_cap3 = pd.read_csv(scanned_data_cap3)

# Select relevant columns for each cap
data_cap1_selected = data_cap1[['source', 'channel', 'signal_strength', 'mac_address', 'ssid']]
data_cap2_selected = data_cap2[['source', 'channel', 'signal_strength', 'mac_address', 'ssid']]
data_cap3_selected = data_cap3[['source', 'channel', 'signal_strength', 'mac_address', 'ssid']]

# Combine data from all three caps based on MAC and SSID
combined_scanned_data = pd.merge(data_cap1_selected, data_cap2_selected, on=['mac_address', 'ssid'])
combined_scanned_data = pd.merge(combined_scanned_data, data_cap3_selected, on=['mac_address', 'ssid'])

# Assign new column names based on the actual column names
combined_scanned_data.columns = ['cap1', 'cap1_channel', 'cap1_signal_strength',
                                 'mac_address', 'ssid',
                                 'cap2', 'cap2_channel', 'cap2_signal_strength',
                                 'cap3', 'cap3_channel', 'cap3_signal_strength']

# Rearrange columns to match the desired output
desired_columns = ['cap1_signal_strength', 'cap2_signal_strength', 'cap3_signal_strength',
                   'cap1_channel', 'cap2_channel', 'cap3_channel',
                   'mac_address', 'ssid']
combined_scanned_data = combined_scanned_data[desired_columns]

print(combined_scanned_data)

# Assuming 'ap_data_processed' is your training data DataFrame
# Replace these column names with the actual column names in your dataset
features = ['cap1_signal_strength', 'cap2_signal_strength', 'cap3_signal_strength',
            'cap1_channel', 'cap2_channel', 'cap3_channel']
target = 'floorid'

# Select features (X) and target variable (y)
X = ap_data_processed[features]
y = ap_data_processed[target]

# Split the data into training and testing sets
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Train a k-Nearest Neighbors classifier with Euclidean metric
k = 23 # You can adjust the value of k
knn = KNeighborsClassifier(n_neighbors=k, p=2)  # p=2 for Euclidean metric
knn.fit(X_train, y_train)

# Assuming 'combined_scanned_data' is your scanned data DataFrame
# Replace these column names with the actual column names in your dataset
scanned_data_features = combined_scanned_data[features]

# Make predictions on the scanned data
predictions = knn.predict(scanned_data_features)

# Add predicted floorid to the scanned data
combined_scanned_data['predicted_floorid'] = predictions

# Display the predicted floorid for the scanned data
print(combined_scanned_data[['mac_address', 'ssid', 'predicted_floorid']])