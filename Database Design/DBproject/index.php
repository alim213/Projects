
<!DOCTYPE html>
<html>

<head>
  <title>Login/Register Page</title>
  <link rel="stylesheet" type="text/css" href="styles/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>

<body>
  <div class="main">
    <input type="checkbox" id="chk" aria-hidden="true">

    <div class="signup">
      <form method="post" action="server.php">
        <label for="chk" aria-hidden="true">Sign up</label>
        <input type="text" name="username" placeholder="User name" required="">
        <input type="firstname" name="firstname" placeholder="First name" required="">
        <input type="lastname" name="lastname" placeholder="Last name" required="">
        <input type="email" name="email" placeholder="Email" required="">
        <input type="password" name="pswd" placeholder="Password" required="">
        <input type="password" name="repswd" placeholder="Reenter Password" required="">
        <button type="submit" name="reguser">Sign up</button>
      </form>
    </div>

    <div class="login">
      <form method="post" action="server.php">
        <label for="chk" aria-hidden="true">Login</label>
        <input type="text" name="loguser" placeholder="User name" required="">
        <input type="password" name="logpswd" placeholder="Password" required="">
        <button type="submit" name="userlog">Login</button>
      </form>
    </div>

</body>
</html>