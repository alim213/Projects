<?php
session_start();

// connect to the database
$connect = mysqli_connect('localhost', 'root', '', 'dbproject');

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

// register user
if (isset($_POST['reguser'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($connect, $_POST['username']);
  $firstname = mysqli_real_escape_string($connect, $_POST['firstname']);
  $lastname = mysqli_real_escape_string($connect, $_POST['lastname']);
  $email = mysqli_real_escape_string($connect, $_POST['email']);
  $password = mysqli_real_escape_string($connect, $_POST['pswd']);
  $repswd = mysqli_real_escape_string($connect, $_POST['repswd']);

  // Use prepared statements with bound parameters to prevent SQL injection
  $stmt = $connect->prepare("SELECT * FROM users WHERE username=? OR email=? LIMIT 1");
  $stmt->bind_param("ss", $username, $email);
  $stmt->execute();
  $result = $stmt->get_result();

  // check if query was successful
  if ($result) {
    // check if any row was returned
    if ($result->num_rows > 0) {
      // username or email already exists
      echo "Username or email already exists in database";
    } else {
      if ($password != $repswd) {
        echo "Passwords do not match";
      } else {
        // Finally, register user if there are no errors in the form
        $pwd = password_hash($password, PASSWORD_DEFAULT); //encrypt the password before saving in the database
        $stmt = $connect->prepare("INSERT INTO users (username, email, password, firstname, lastname) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $pwd, $firstname, $lastname);
        $stmt->execute();

        $_SESSION['username'] = $username;
        header('location: table.php');
      }
    }
  } else {
    // query was not successful
    echo "Error: " . mysqli_error($connect);
  }
}

// LOGIN USER
if (isset($_POST['userlog'])) {
  $loguser = mysqli_real_escape_string($connect, $_POST['loguser']);
  $logpassword = mysqli_real_escape_string($connect, $_POST['logpswd']);


  // Use prepared statements with bound parameters to prevent SQL injection
  $stmt = $connect->prepare("SELECT * FROM users WHERE username=?");
  $stmt->bind_param("s", $loguser);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    if (password_verify($logpassword, $row['password'])) {
      // Login successful
      $_SESSION['username'] = $row['username'];
      header('location: table.php');
      exit();
    } else {
      // Incorrect password
      echo "Incorrect password";
    }
  } else {
    // User not found
    echo "User not found";
  }
}

if (isset($_POST['logoff'])) {
  session_start();
  unset($_SESSION['username']);  
  session_destroy();
  header("Location: index.php");
  exit();
}






?>



