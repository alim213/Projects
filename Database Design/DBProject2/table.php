<?php
include('server.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    body{background: #6d44b8;}
</style>
<body>

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

<div style="text-align: center; margin-top: 20px;">
  <form method="post" action="table.php">
    <button type="submit" name="initaldb">Initialize Database</button>
  </form>
</div>

<div style="text-align: center; margin-top: 20px;">
  <label>Display Reviews</label>
  <form method="post" action="table.php">
    <button type='submit' name='display_review'>Display Review</button>
  </form>
</div>

<div style="text-align: center; margin-top: 20px;">
  <label>Display Items</label>
  <form method="post" action="table.php">
    <button type='submit' name='display_item'>Display Items</button>
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

<!-- Add the logout button after the table -->
<div style="text-align: center; margin-top: 20px;">
  <form method="post" action="server.php">
    <button type="submit" name="logoff">Log off</button>
  </form>
</div>

<div style="text-align: center; margin-top: 20px;">
  <form method="post" action="table.php">
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
    <label>Search by category:</label>
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
    echo "<tr><th>ID</th><th>Title</th><th>Description</th><th>Category</th><th>Price</th><th>Review</th></tr>";
    while ($row = mysqli_fetch_assoc($search_result)) {
      echo "<tr><td>".$row['post_id']."</td><td>".$row['title']."</td><td>".$row['description']."</td><td>".$row['category']."</td><td>".$row['price']."</td>";
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
      $stmt = $connect->prepare("INSERT INTO reviews (user_id, item_id, rating, review, date) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param('sssss', $user_id, $item_id, $review_rating, $review_description, $date);
      $stmt->execute();
      $stmt->close();

      echo "Your review has been submitted successfully.";
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
  echo "<tr><th>Review ID</th><th>User ID</th><th>Item ID</th><th>Rating</th><th>Review</th></tr>";

  // Output each row as a table row
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row["review_id"] . "</td>";
    echo "<td>" . $row["user_id"] . "</td>";
    echo "<td>" . $row["item_id"] . "</td>";
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

?>




</body>
</html>