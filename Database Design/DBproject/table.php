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

</body>
</html>