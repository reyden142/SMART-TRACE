import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
from sklearn.neighbors import KNeighborsClassifier
from sklearn.metrics import confusion_matrix
from sklearn.metrics import f1_score
from sklearn.metrics import accuracy_score

# Datasets

file_path = r'C:\Users\pc\Desktop\ThesisCode\Thesis2.0\django_thesis\Dataset\diabetes.csv'

dataset = pd.read_csv(file_path) #insert location of the dataset file
print(len(dataset))
print(dataset.head())

# Replace Zeroes

zero_not_accepted = ['Glucose','BloodPressure','SkinThickness','BMI','Insulin'] #insert AP1, AP2, AP3

for column in zero_not_accepted:
    dataset[column] = dataset[column].replace(0, np.NaN)
    mean = int(dataset[column].mean(skipna = True))
    dataset[column] = dataset[column].replace(np.NaN,mean)

# Split Dataset
X = dataset.iloc[:,0:8] # 0 to 2 is the number of column
y = dataset.iloc[:,8] # 3 has a column of the " actual results "
X_train, X_test, y_train, y_test = train_test_split(X,y,random_state=0,test_size=0.2) # 0.2 = 20% of the data that we can test

# Feature Scaling
sc_X = StandardScaler()
X_train = sc_X.fit_transform(X_train)
X_test = sc_X.transform(X_test)

# next step is to solve for the k value
# which is equal to sqrt(n), n = total number of dataset
import math
k = math.sqrt(len(y_test))
print(k)

# Define the Model: Init K-NN
classifier = KNeighborsClassifier(n_neighbors=11,p=2,metric='euclidean') # p is the location in x,y coordinate, not sure for the value of p

# Fit the classifier to the training data
classifier.fit(X_train, y_train)

# Predict the test set results
y_pred = classifier.predict(X_test)
print(y_pred)

# Evaluate the Model
cm = confusion_matrix(y_test, y_pred)
print (cm)
print(f1_score(y_test, y_pred))
print(accuracy_score(y_test, y_pred))




