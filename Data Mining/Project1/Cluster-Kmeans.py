
# Import libraries
# 1. Introduction to the Dataset
import pandas as pd
import numpy as np
# 2. Data Preprocessing
from sklearn.preprocessing import StandardScaler
from sklearn.preprocessing import MinMaxScaler
# 3. Exploratory Data Analysis (EDA)
import matplotlib.pyplot as plt
import seaborn as sns
# 5. Implementation of K-means Clustering
from sklearn.cluster import KMeans
# 6. Evaluation of Clusters
from sklearn.metrics import silhouette_score
# 7. Visualization
from sklearn.decomposition import PCA




# 1. Introduction to the Dataset
# This load the dataset
file_path = "College.csv"
dataframe = pd.read_csv("C:\\Users\\antho\\OneDrive\\Desktop\\Data Mining\\Project1\\College.csv", index_col=0)




# This display the first 5 rows of the dataset
print("First 5 rows of the dataset:")
dataframe.head()





# This is a summary of the dataset
print("\nSummary of the dataset:")
dataframe.info()





# This is the basic statistics of the dataset
print("\nBasic statistics of the dataset:")
dataframe.describe()





# 2. Data Preprocessing
# This is to count missing values
print("Count of missing values:")
print(dataframe.isnull().sum())





# This method is for handling missing values
#dataframe.fillna(dataset.mean(), inplace=True)





# This is show the count of missing values after handling
print("\nCount of missing values after handling:")
print(dataframe.isnull().sum())




# This is to encode categorical variables using one-hot encoding
dataframe_encoded = pd.get_dummies(dataframe, columns=['Private'], drop_first=True)




# This is to display the data types of each column
dataframe_encoded.dtypes





# This method is to add a new column 'University_Label' with a unique number for each university
dataframe_encoded['University_Label'] = range(1, len(dataframe_encoded) + 1)





# This is to display the first 5 rows of the updated dataset
print("\nFirst 5 rows of the dataset after encoding:")
dataframe_encoded.head()




# This is to display the last 5 rows of the updated dataset
print("\nLast 5 rows of the dataset after encoding:")
dataframe_encoded.tail()





# This is to normalize the data using StandardScaler
scaler = StandardScaler()
dataframe_normalized = pd.DataFrame(scaler.fit_transform(dataframe_encoded), columns=dataframe_encoded.columns)




# This is to show dataset before statistics for normalization
print("\nStatistics before normalization:")
dataframe_encoded.describe()





# This is to show dataset after statistics for normalization
print("\nStatistics after normalization:")
dataframe_normalized.describe()





# 3. Exploratory Data Analysis (EDA)
# This is to set the 'University_Label' as the index for better visualization
dataframe_encoded.set_index('University_Label', inplace=True)

# This is to get a list of features without 'Private_Yes' column
features = dataframe_encoded.drop(['Private_Yes'], axis=1).columns





# This is a plotted bar plots for each feature individually
for feature in features:
    plt.figure(figsize=(12, 6))
    plt.bar(dataframe_encoded.index, dataframe_encoded[feature], color='blue', alpha=0.7)
    plt.title(f'University Label vs. {feature}')
    plt.xlabel('University Label')
    plt.ylabel(f'{feature}')
    plt.grid(axis='y')
    plt.show()





# This is a boxplots for numerical features
plt.figure(figsize=(15, 10))
sns.boxplot(data=dataframe, orient='h')
plt.title('Boxplots of Numerical Features')
plt.show()





# This is to create a new figure with specified size
plt.figure(figsize=(10, 6))

# This is making a box plot of 'Apps' and 'Accept'
sns.boxplot(data=dataframe_encoded[['Apps', 'Accept']], palette='Set2')

# This sets the title and axis labels
plt.title('Box Plot of Apps and Accept')
plt.xlabel('Features')
plt.ylabel('Values')

# This is to display the plot
plt.show()





# This is to create a new figure with specified size
plt.figure(figsize=(8, 5))

# This is making a bar plot of 'Private_Yes'
sns.countplot(x='Private_Yes', data=dataframe_encoded, palette='Set2')

# This sets the title and axis labels
plt.title('Count Plot of Private vs Non-Private Universities')
plt.xlabel('Private')
plt.ylabel('Count')

# This is to display the plot
plt.show()





# This is a summary statistics
print("\nSummary statistics:")
dataframe.describe()





# This is selecting only numeric columns for correlation analysis
numeric_dataframe = dataframe.select_dtypes(include='number')




# This creates a correlation analysis using heatmap
correlation_matrix = numeric_dataframe.corr()





# This is plotting the heatmap
plt.figure(figsize=(15, 10))
sns.heatmap(correlation_matrix, annot=True, cmap='coolwarm', fmt=".2f")
plt.title('Correlation Matrix')
plt.show()





# THis is to determine the optimal number of clusters using the elbow method
inertia_values = []
for k in range(1, 11):
    kmeans = KMeans(n_clusters=k, random_state=42)
    kmeans.fit(numeric_dataframe)
    inertia_values.append(kmeans.inertia_)





# This is plotting the elbow method
plt.figure(figsize=(10, 6))
plt.plot(range(1, 11), inertia_values, marker='o')
plt.title('Elbow Method for Optimal Number of Clusters')
plt.xlabel('Number of Clusters')
plt.ylabel('Inertia (Within-cluster sum of squares)')
plt.show()




# This chooses the optimal number of clusters (elbow point)
optimal_clusters = 3 





# This is applying the K-means clustering with the chosen number of clusters
kmeans_final = KMeans(n_clusters=optimal_clusters, random_state=42)
dataframe['Cluster'] = kmeans_final.fit_predict(numeric_dataframe)




# This is to display the final clustering results
print("\nCluster Centers:")
pd.DataFrame(kmeans_final.cluster_centers_, columns=numeric_dataframe.columns)





# This is to display the count of data points in each cluster
print("\nCount of Data Points in Each Cluster:")
print(dataframe['Cluster'].value_counts())





# This is calculating the Silhouette Score
silhouette_avg = silhouette_score(numeric_dataframe, dataframe['Cluster'])
print(f"\nSilhouette Score: {silhouette_avg}")





# This is calculating within-cluster sum of squares
wcss = kmeans_final.inertia_
print(f"\nWithin-Cluster Sum of Squares (WCSS): {wcss}")




# 7. Visualization
# This is assuming that X contains feature without Private_Yes column
X = dataframe_encoded.drop(['Private_Yes'], axis=1)




# This is standardizing the data using standardscaler
scaler = StandardScaler()
X_scaled = scaler.fit_transform(X)





# This specify the number of components in the pca
n_components = 2
pca = PCA(n_components=n_components)

# This fit the PCA model and transform the data
X_pca = pca.fit_transform(X_scaled)




# This is creating a dataframe for PCA results
dataframe_pca = pd.DataFrame(X_pca, columns=[f'PC{i+1}' for i in range(n_components)])





# This is setting up subplots
fig, axes = plt.subplots(1, 2, figsize=(15, 6))

# This is plotting the original data
sns.scatterplot(data=dataframe_encoded, x='Apps', y='Accept', alpha=0.7, ax=axes[0], hue='Private_Yes', palette='Set1', label='Original Data')
axes[0].set_title('Original Data: Apps vs Accept')
axes[0].set_xlabel('Apps')
axes[0].set_ylabel('Accept')
axes[0].legend()

# This is plotting the PCA results with different colors for 'Apps' and 'Accept'
sns.scatterplot(data=dataframe_pca, x='PC1', y='PC2', alpha=0.7, hue=dataframe_encoded['Private_Yes'], palette='Set1', ax=axes[1], label='PCA Results')
axes[1].set_title('PCA Results: PC1 vs PC2')
axes[1].set_xlabel('PC1')
axes[1].set_ylabel('PC2')
axes[1].legend()

plt.tight_layout()
plt.show()



# 8. Interpretation and Insight
# From the dataset, we can see that there is more private university than non-private.
# Also in the dataset, there are more applicate than there are people accepted to a university.
# The average expenditure is around 10,000 per student altough there is not any information
# if there is annually or for entire live-time of a student. I suspect that this might be annually.
# This is due the to the fact that university are costly annually.
# This analyzation is only torward acceptted versus applicates.



# 9. Reflection
# There can be further refactoring of code.
# We can create code and see the the average rate of graduate student per university.
# The hard thing is to understanding what is Silhouette Score and Within-Cluster Sum of Squares
# Also, there can further implementation of PCA for other analysis 

