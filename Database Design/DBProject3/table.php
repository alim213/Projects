<?php
include('server.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="container">
<?php

if ($_SESSION['username']) {
echo "<h3>".$_SESSION['username']." is logged in!<h3>"; 
// connect to the database
$connect = mysqli_connect('localhost', 'root', '', 'dbproject');

  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}
} 
?>

<!-- Add the logout button to the top -->
<div style="text-align: center; margin-top: 20px;">
  <form method="post" action="server.php">
    <button type="submit" name="logoff">Log off</button>
  </form>
</div>

<div style="text-align: center; margin-top: 20px;">
  <form method="post" action="table.php">
    <button type="submit" name="initaldb">Initialize Database</button>
  </form>
</div>

<div style="text-align: center;">
  <form method="post" action="table.php">
    <button type='submit' name='display_review'>Display Review</button>
  </form>
</div>

<div style="text-align: center;">
  <form method="post" action="table.php">
    <button type='submit' name='display_item'>Display Items</button>
  </form>
</div>


<div style="text-align: center;">
  <form action="table.php" method="post">
    <button type="submit" name="show_expensive">Expensive Items</button>
  </form>
</div>

<div style="text-align: center;">
  <form method="post" action="table.php">
    <button type='submit' name='display_date'>Display Post Since 5/1/2020</button>
  </form>
</div>

<div style="text-align: center;">
  <label>Search Users That Posted In The Same Day In Different Category:</label>
  <form class="search-form" method="post" action="table.php">
		<label>User Category X:</label>
		<input type="text" name="categoryX">
		<label>User Category Y:</label>
		<input type="text" name="categoryY">
		<button type="submit" name="search_user">Search Users</button>
  </form>
</div>

<div style="text-align: center; margin-top: 20px;">  
  <form class="search-form" method="post" action="table.php">
    <label for="user_ex">Reviewed Items With Only Excellent or Good Ratings Posted By A User:</label>
    <input type="text" name="user_ex" id="user">
    <button type="submit" name="e_users">Search User</button>
  </form>
</div>



<?php

if ($_SESSION['username'] && isset($_POST['initaldb'])) {
  // select all rows from the users table
  $sql = "SELECT * FROM users";
  $result = mysqli_query($connect, $sql);

  // check if query was successful
  if ($result) {
    // check if any row was returned
    if (mysqli_num_rows($result) > 0) {
      // print table header
      echo "<table>";
      echo "<tr><th>ID</th><th>Username</th><th>First Name</th><th>Last Name</th><th>Email</th></tr>";
      // loop through each row in the result set
      while ($row = mysqli_fetch_assoc($result)) {
        // print table row
        echo "<tr><td>".$row['id']."</td><td>".$row['username']."</td><td>".$row['firstname']."</td><td>".$row['lastname']."</td><td>".$row['email']."</td></tr>";
      }
    echo "</table>";
  } else {
    // no rows returned
    echo "No rows found in table";
  }
} else {
    // query was not successful
    echo "Error: " . mysqli_error($connect);
  }

  // close database connection
  mysqli_close($connect);
}  

?>



<div style="text-align: center; margin-top: 20px;">
  <form class="search-form" method="post" action="table.php">
    <label>Title:</label>
    <input type="text" name="item_title" required>
    <label>Description:</label>
    <input type="text" name="item_description" required>
    <label>Category:</label>
    <input type="text" name="item_category" required>
    <label>Price:</label>
    <input type="number" name="item_price" step="0.01" required>
    <button type="submit" name="submit_item">Submit Item</button>
  </form>
</div>

<div style="text-align: center; margin-top: 20px;">
  <form method="post" action="table.php">
    <label>Search By Category:</label>
    <input type="text" name="search_category" required>
    <button type="submit" name="search_items">Search</button>
  </form>
</div>




<?php


// check if the form has been submitted
if (isset($_POST['display_item'])) {

  // retrieve data from your database
  $sql = "SELECT * FROM items";
  $result = mysqli_query($connect, $sql);

  // check if any data was retrieved
  if (mysqli_num_rows($result) > 0) {

    // start creating your table
    echo "<table>";
    echo "<tr>";
    echo "<th>User ID</th>";
    echo "<th>Post ID</th>";
    echo "<th>Title</th>";
    echo "<th>Description</th>";
    echo "<th>Category</th>";
    echo "<th>Price</th>";
    echo "<th>Date</th>";
    echo "</tr>";

    // loop through the data and create a row for each item
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<tr>";
      echo "<td>" . $row['user_id'] . "</td>";
      echo "<td>" . $row['post_id'] . "</td>";
      echo "<td>" . $row['title'] . "</td>";
      echo "<td>" . $row['description'] . "</td>";
      echo "<td>" . $row['category'] . "</td>";
      echo "<td>" . $row['price'] . "</td>";
      echo "<td>";
      echo "<form method='post' action='table.php'>
          <input type='hidden' name='item_id' value='".$row['post_id']."'>
          <div style='display: flex; align-items: center; gap: 10px;'>
          <label>Rating:</label>
          <select name='review_rating' required>
            <option value='excellent'>Excellent</option>
            <option value='good'>Good</option>
            <option value='fair'>Fair</option>
            <option value='poor'>Poor</option>
          </select>
          <label>Review:</label>
          <input type='text' name='review_description'>
          <button type='submit' name='submit_review'>Submit Review</button>
          <button type='submit' name='add_to_favorites'>Add to Favorites</button>
          </div>
          </form>";
      echo "</td>";
      echo "</tr>";
    }

    // end your table
    echo "</table>";

  } else {
    // if no data was retrieved, display an error message
    echo "No items found.";
  }
}


// Check if the form has been submitted
if (isset($_POST['submit_item'])) {
  // Get the user ID from the session or database, assuming it is stored there
  $user_id = $_SESSION['username'];
  $title = mysqli_real_escape_string($connect, $_POST['item_title']);
  $description = mysqli_real_escape_string($connect, $_POST['item_description']);
  $category = mysqli_real_escape_string($connect, $_POST['item_category']);
  $price = mysqli_real_escape_string($connect, $_POST['item_price']);


  // Check if the user has already posted three items today
  $date = date('Y-m-d');
  $stmt = $connect->prepare("SELECT COUNT(*) FROM items WHERE user_id = ? AND DATE = ?");
  $stmt->bind_param('ss', $user_id, $date);
  $stmt->execute();
  $count = $stmt->get_result()->fetch_row()[0];



  if ($count >= 3) {
    // Display an error message if the user has already posted three items today
    echo "You have already posted three items today. Please try again tomorrow.";
  } else {
    // Insert the new item into the database
    $stmt = $connect->prepare("INSERT INTO items (user_id, title, description, category, price, date) VALUES(?, ?, ?, ?, ?,?)");
    $stmt->bind_param("ssssss", $user_id, $title, $description, $category, $price, $date);
    $stmt->execute();
   

    // Display a success message to the user
    echo "Item added successfully!";
  }
}

?>


<?php

if (isset($_POST['search_items'])) {
  $search_category = mysqli_real_escape_string($connect, $_POST['search_category']);
  $search_sql = "SELECT * FROM items WHERE category LIKE '%$search_category%'";
  $search_result = mysqli_query($connect, $search_sql);

  if (mysqli_num_rows($search_result) > 0) {
    echo "<table>";
    echo "<tr><th>User ID</th><th>Post ID</th><th>Title</th><th>Description</th><th>Category</th><th>Price</th><th>Review</th></tr>";
    while ($row = mysqli_fetch_assoc($search_result)) {
      echo "<tr><td>".$row['user_id']."</td><td>".$row['post_id']."</td><td>".$row['title']."</td><td>".$row['description']."</td><td>".$row['category']."</td><td>".$row['price']."</td>";
      echo "<td>
      <form method='post' action='table.php'>
        <input type='hidden' name='item_id' value='".$row['post_id']."'>
        <label>Rating:</label>
        <select name='review_rating' required>
          <option value='excellent'>Excellent</option>
          <option value='good'>Good</option>
          <option value='fair'>Fair</option>
          <option value='poor'>Poor</option>
        </select>
        <label>Review:</label>
        <input type='text' name='review_description'>
        <button type='submit' name='submit_review'>Submit Review</button>
        <button type='submit' name='add_to_favorites'>Add to Favorites</button>
      </form>
    </td></tr>";
    }
    echo "</table>";
    
  } else {
    echo "No items found in the given category";
  }
}

if (isset($_POST['submit_review'])) {
  // Get the logged in user ID
  $user_id = $_SESSION['username'];

  // Get the item ID from the form
  $item_id = mysqli_real_escape_string($connect, $_POST['item_id']);

  // Check if the user has already given 3 reviews today
  $date = date('Y-m-d');
  $stmt = $connect->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ? AND DATE = ?");
  $stmt->bind_param('ss', $user_id, $date);
  $stmt->execute();
  $count = $stmt->get_result()->fetch_row()[0];
  $stmt->close();

  if ($count >= 3) {
    echo "You have reached the limit of giving 3 reviews a day.";
  } else {
    // Check if the user is trying to review his own item
    $stmt = $connect->prepare("SELECT user_id FROM items WHERE post_id = ?");
    $stmt->bind_param('s', $item_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $item_user_id = $result['user_id'];
    $stmt->close();

    if ($user_id == $item_user_id) {
      echo "You cannot review your own item.";
    } else {
      // Get the review rating and description from the form
      $review_rating = mysqli_real_escape_string($connect, $_POST['review_rating']);
      $review_description = mysqli_real_escape_string($connect, $_POST['review_description']);

      // Insert the review into the database
      $stmt = $connect->prepare("INSERT INTO reviews (user_id, item_id, post_id, rating, review, date) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->bind_param('ssssss', $user_id, $item_id, $item_user_id, $review_rating, $review_description, $date);
      $stmt->execute();
      $stmt->close();

      echo "Your review has been submitted successfully.";
    }
  }
}

if (isset($_POST['add_to_favorites'])) {
  // Get the logged in user ID
  $user_id = $_SESSION['username'];

  // Get the item ID from the form
  $item_id = mysqli_real_escape_string($connect, $_POST['item_id']);

  // Check if the item is already in the user's favorites
  $stmt = $connect->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ? AND item_id = ?");
  $stmt->bind_param('ss', $user_id, $item_id);
  $stmt->execute();
  $count = $stmt->get_result()->fetch_row()[0];
  $stmt->close();

  // Get the user ID of the item's owner
  $stmt = $connect->prepare("SELECT user_id FROM items WHERE post_id = ?");
  $stmt->bind_param('s', $item_id);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  $item_user_id = $result['user_id'];
  $stmt->close();

  if ($user_id == $item_user_id) {
    echo "You cannot like your own item.";
  } else {
    if ($count > 0) {
      echo "This item is already in your favorites.";
    } else {
      // Insert the item into the user's favorites
      $stmt = $connect->prepare("INSERT INTO favorites (user_id, item_id, post_user) VALUES (?, ?, ?)");
      $stmt->bind_param('sss', $user_id, $item_id, $item_user_id);
      $stmt->execute();
      $stmt->close();

      echo "This item has been added to your favorites.";
    }
  }
}


if (isset($_POST['display_review'])) {
// Retrieve the reviews from the database
$sql = "SELECT * FROM reviews";
$result = mysqli_query($connect, $sql);

// Check if any rows were returned
if (mysqli_num_rows($result) > 0) {
  // Output the table headers
  echo "<table>";
  echo "<tr><th>Review Number</th><th>Reviewer Name</th><th>Item ID</th><th>Poster Name</th><th>Rating</th><th>Review</th></tr>";

  // Output each row as a table row
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row["review_id"] . "</td>";
    echo "<td>" . $row["user_id"] . "</td>";
    echo "<td>" . $row["item_id"] . "</td>";
    echo "<td>" . $row["post_id"] . "</td>";
    echo "<td>" . $row["rating"] . "</td>";
    echo "<td>" . $row["review"] . "</td>";
    echo "</tr>";
  }

  // Close the table
  echo "</table>";
} else {
  echo "No reviews found.";
}
}

if (isset($_POST['show_expensive'])) {
  // Retrieve the most expensive item for each category
  $sql = "SELECT category, MAX(price) AS max_price FROM items GROUP BY category";
  $result = $connect->query($sql);
  
  // Create an HTML table to display the results
  echo "<table>\n";
  echo "<tr><th>User ID</th><th>Category</th><th>Price</th></tr>\n";
  while ($row = $result->fetch_assoc()) {
    $category = $row['category'];
    $max_price = $row['max_price'];
  
    // Retrieve the details of the most expensive item for this category
    $sql = "SELECT user_id, price FROM items WHERE category = '$category' AND price = $max_price";
    $item_result = $connect->query($sql);
    $item = $item_result->fetch_assoc();
  
    // Display the details in a table row
    echo "<tr><td>{$item['user_id']}</td><td>$category</td><td>{$item['price']}</td></tr>\n";
  }
  echo "</table>\n";
  
  // Close the database connection
  $connect->close();
  }
  
  
  if (isset($_POST['search_user'])) {
  
  // Get the categories from the form
  $category_x = $_POST["categoryX"];
  $category_y = $_POST["categoryY"];
  
  // Get the users who posted at least two items that are posted on the same day,
  // one has a category of X, and another has a category of Y
  $sql = "SELECT user_id FROM items WHERE category='$category_x' OR category='$category_y' GROUP BY user_id HAVING COUNT(DISTINCT DATE(date)) > 1";
  $result = $connect->query($sql);
  
  // Display the results
  if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>User ID</th></tr>";
    while ($row = $result->fetch_assoc()) {
      $user_id = $row["user_id"];
      echo "<tr><td>$user_id</td></tr>";
    }
    echo "</table>";
  } else {
    echo "<p>No users found.</p>";
  }
  
  // Close the database connection
  $connect->close();
  
  }
  
  
  if (isset($_POST['e_users'])) {
  
  // Retrieve items with good or excellent ratings by post_id
  $post_id = $_POST['user_ex'];

  $sql = "SELECT DISTINCT post_id, item_id, rating, review
  FROM reviews
  WHERE post_id = '$post_id' AND rating IN ('excellent', 'good')";
  
  $result = $connect->query($sql);
  
  // Display results in table
  if ($result->num_rows > 0) {
    echo "<table>";
    echo "<h1>All the items posted by user: '$post_id' such that all the comments are 'Excellent' or 'Good'</h1>";
    echo "<thead><tr><th>Item ID</th><th>Rating</th><th>Review</th></tr></thead>";
    echo "<tbody>";
    while($row = $result->fetch_assoc()) {
      echo "<tr><td>" . $row["item_id"] . "</td><td>" . $row["rating"] . "</td><td>" . $row["review"] . "</td></tr>";
    }
    echo "</tbody>";
    echo "</table>";
  } else {
    echo "<p>No reviews found.</p>";
  }
  
  $connect->close();
  
  }
  
  
  if (isset($_POST['display_date'])) {
  // Fetch the data
  $sql = "SELECT * FROM items WHERE date >= '2020-05-01' ORDER BY user_id";
  $result = mysqli_query($connect, $sql);
  
  // Display the data
  if (mysqli_num_rows($result) > 0) {
      echo "<table><tr><th>Post ID</th><th>User ID</th><th>Title</th><th>Description</th><th>Category</th><th>Price</th><th>Date</th></tr>";
      while($row = mysqli_fetch_assoc($result)) {
          echo "<tr><td>".$row["post_id"]."</td><td>".$row["user_id"]."</td><td>".$row["title"]."</td><td>".$row["description"]."</td><td>".$row["category"]."</td><td>".$row["price"]."</td><td>".$row["date"]."</td></tr>";
      }
      echo "</table>";
  } else {
      echo "No data found";
  }
  
  // Close the connection
  mysqli_close($connect);
  
  }



// Check if the form has been submitted
if(isset($_POST['compare'])) {
      
  // Get the selected user IDs from the form
  $userX = $_POST['userX'];
  $userY = $_POST['userY'];

  // Retrieve the list of users who are favorited by both X and Y
  $query = "SELECT post_user FROM favorites WHERE user_id='$userX' AND post_user IN (SELECT post_user FROM favorites WHERE user_id='$userY')";
  $result = mysqli_query($connect, $query);

  // Store the list of users in an array
  $users = array();
  while ($row = mysqli_fetch_assoc($result)) {
      $users[] = $row['post_user'];
  }

  // Close the database connection
  mysqli_close($connect);

  // Check if there are any users who are favorited by both X and Y
  if(count($users) > 0) {
      // Display the list of users who are favorited by both X and Y
      echo '<h1>The following users are favorited by both ' . $userX . ' and ' . $userY . ':</h1>';
      echo '<table>';
      echo '<tr><th>User ID</th></tr>';
      foreach ($users as $user) {
          echo '<tr><td>' . $user . '</td></tr>';
      }
      echo '</table>';
  } else {
      // Display a message if there are no users who are favorited by both X and Y
      echo '<p>There are no user(s) who are favorited by both ' . $userX . ' and ' . $userY . '.</p>';
  }
}


if(isset($_POST['never_ex'])) {
  // Query to retrieve users who have never posted any "excellent" items
  $sql = "SELECT DISTINCT post_id FROM reviews WHERE post_id NOT IN (
    SELECT DISTINCT post_id FROM reviews WHERE rating = 'excellent' GROUP BY post_id HAVING COUNT(DISTINCT user_id, item_id, rating) >= 3
)";

  // Execute the query and check for errors
  $result = $connect->query($sql);

  // Display the results as a table
  echo "<h1>Users who never posted any \"excellent\" items (an item is excellent if at least 
  three reviews are excellent):</h1>";
  echo "<table>";
  echo "<thead><tr><th>User</th></tr></thead>";
  echo "<tbody>";
  while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row['post_id'] . "</td></tr>";
  }
  echo "</tbody>";
  echo "</table>";

  // Close the database connection
  $connect->close();
}

if(isset($_POST['never_poor'])) {
// Query to retrieve all users who have posted a "poor" review
$sql = "SELECT DISTINCT user_id FROM reviews WHERE rating = 'poor'";
$result = $connect->query($sql);

// Create an array to store the user IDs of those who have posted a "poor" review
$poor_users = array();
while ($row = $result->fetch_assoc()) {
  $poor_users[] = $row['user_id'];
}

// Query to retrieve all users who have never posted a "poor" review
$sql = "SELECT DISTINCT user_id FROM reviews WHERE user_id NOT IN 
        (SELECT DISTINCT user_id FROM reviews WHERE rating = 'poor')";
$result = $connect->query($sql);

// Display the results as a table
echo "<h1>Users who never posted a \"poor\" review:</h1>";
echo "<table>";
echo "<thead><tr><th>User ID</th></tr></thead>";
echo "<tbody>";
while ($row = $result->fetch_assoc()) {
  if (!in_array($row['user_id'], $poor_users)) {
    echo "<tr><td>" . $row['user_id'] . "</td></tr>";
  }
}
echo "</tbody>";
echo "</table>";

// Close the database connection
$connect->close();
}

if(isset($_POST['never_item'])) {
// Retrieve users with items that have never received a "poor" rating
$sql = "SELECT DISTINCT post_id
        FROM reviews
        WHERE post_id NOT IN (
          SELECT post_id
          FROM reviews
          WHERE rating = 'poor'
        )
        AND post_id IN (
          SELECT post_id
          FROM reviews
        )";
        
$result = $connect->query($sql);

// Display the results as a table
echo "<h1>Users who have posted items that never received any \"poor\" reviews:</h1>";
echo "<table>";
echo "<thead><tr><th>User ID</th></tr></thead>";
echo "<tbody>";
while ($row = $result->fetch_assoc()) {
  echo "<tr><td>" . $row['post_id'] . "</td></tr>";
}
echo "</tbody>";
echo "</table>";

// Close the database connection
$connect->close();
}

if(isset($_POST['each_item'])) {
// Query to retrieve all users who have posted reviews, but each of them is "poor"
$sql = "SELECT DISTINCT user_id FROM reviews WHERE user_id NOT IN 
        (SELECT DISTINCT user_id FROM reviews WHERE rating != 'poor')
        AND user_id IN (SELECT DISTINCT user_id FROM reviews)";
$result = $connect->query($sql);

// Display the results as a table
echo "<h1>Users who have posted reviews, but each of them is \"poor\":</h1>";
if ($result->num_rows > 0) {
echo "<table>";
echo "<thead><tr><th>User ID</th></tr></thead>";
echo "<tbody>";
while ($row = $result->fetch_assoc()) {
  echo "<tr><td>" . $row['user_id'] . "</td></tr>";
}
echo "</tbody>";
echo "</table>";
} else {
echo "<h3>No users have posted reviews with a rating of \"poor\".</h3>";
}

// Close the database connection
$connect->close();
}

if(isset($_POST['pair'])) {
  // Query to retrieve all distinct user pairs who always gave each other "excellent" reviews for every single item they posted
  $sql = "SELECT r1.user_id as user_a, r1.post_id as post_a, r2.user_id as user_b, r2.post_id as post_b
  FROM reviews r1
  JOIN reviews r2 ON r1.post_id = r2.user_id AND r1.user_id = r2.post_id
  WHERE r1.rating = 'excellent' AND r2.rating = 'excellent'
  GROUP BY r1.user_id, r1.post_id, r2.user_id, r2.post_id
  HAVING COUNT(DISTINCT r1.item_id) = COUNT(DISTINCT r2.item_id)";

  // Execute the query and retrieve the results
  $result = $connect->query($sql);

  // Display the results as a table
  echo "<h1>User pairs who always gave each other \"excellent\" reviews for every single item:</h1>";
  if ($result->num_rows > 0) { 
      echo "<table>";
      echo "<thead><tr><th>User A</th><th>User B</th></tr></thead>";
      echo "<tbody>";
      while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['user_a'] . "</td><td>" . $row['user_b'] . "</td></tr>";
      }
      echo "</tbody>";
      echo "</table>";
  } else {
      echo "<h3>No pairs are found in the database.</h3>";
  }

  // Close the database connection
  $connect->close();
}

?>

<div style="text-align: center; margin-top: 20px;">  
    <form action="table.php" method="POST">
        <label>The Following User Are Favorited By Both:</label>
        <label for="userX">Select user X:</label>
        <select name="userX" id="userX">
            <option value="">--Select user--</option>
            <?php
                // Establish a database connection
                $connect = mysqli_connect('localhost', 'root', '', 'dbproject');

                // Fetch the list of users from the database
                $sql = "SELECT DISTINCT username FROM users";
                $result = mysqli_query($connect, $sql);

                // Loop through the results and create an option for each user
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . $row['username'] . '">' . $row['username'] . '</option>';
                }

                // Close the database connection
                mysqli_close($connect);
            ?>
        </select>
        <br><br>
        <label for="userY">Select user Y:</label>
        <select name="userY" id="userY">
            <option value="">--Select user--</option>
            <?php
                // Establish a new database connection
         $connect = mysqli_connect('localhost', 'root', '', 'dbproject');

                // Fetch the list of users from the database
                $sql = "SELECT DISTINCT username FROM users";
                $result = mysqli_query($connect, $sql);

                // Loop through the results and create an option for each user
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . $row['username'] . '">' . $row['username'] . '</option>';
                }

                // Close the database connection
                mysqli_close($connect);
            ?>
        </select>
        <br><br>
        <button type="submit" name="compare" value="Compare favorites">Compare favorites</button>
    </form>
</div>

<div style="text-align: center; margin-top: 20px;">
  <label>Users Who Never Posted Any "Excellent" Items (An Item Is Excellent If At Least 
Three Reviews Are Excellent):</label>
  <form method="post" action="table.php">
    <button type='submit' name='never_ex'>Display Never Excellent Reviewers</button>
  </form>
</div>

<div style="text-align: center; margin-top: 20px;">
  <label>Users Who Never Posted A "Poor" Review:</label>
  <form method="post" action="table.php">
    <button type='submit' name='never_poor'>Display Never Poor Reviewers</button>
  </form>
</div>

<div style="text-align: center; margin-top: 20px;">
  <label>Users Who Have Posted Items That Never Received Any "Poor" Reviews:</label>
  <form method="post" action="table.php">
    <button type='submit' name='never_item'>Display Never Poor Review Users</button>
  </form>
</div>

<div style="text-align: center; margin-top: 20px;">
  <label>Users Who Have Posted Reviews, But Each Of Them Is "Poor":</label>
  <form method="post" action="table.php">
    <button type='submit' name='each_item'>Display Each Reviewer</button>
  </form>
</div>

<div style="text-align: center; margin-top: 20px;">
  <label>User Pairs Who Always Gave Each Other "Excellent" Reviews For Every Single Item:</label>
  <form method="post" action="table.php">
    <button type='submit' name='pair'>Display Each Pair</button>
  </form>
</div>

</div>
</body>
</html>