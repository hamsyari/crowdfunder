<?php
session_start();

if(isset($_SESSION['usr_id'])!="") {
    header("Location: index.php");
}

$dbconn = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=postgres")
or die('Could not connect: ' . pg_last_error());

//set validation error flag as false
$error = false;

//check if form is submitted
if (isset($_POST['signup'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $country = $_POST['country'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    
    //name can contain only alpha characters and space
    if (!preg_match("/^[a-zA-Z ]+$/",$fname)) {
        $error = true;
        $name_error = "Name must contain only alphabets and space";
    }

    if (!preg_match("/^[a-zA-Z ]+$/",$lname)) {
        $error = true;
        $name_error = "Name must contain only alphabets and space";
    }
    
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $email_error = "Please Enter Valid Email ID";
    }

    if(strlen($password) < 6) {
        $error = true;
        $password_error = "Password must be minimum of 6 characters";
    }

    if($password != $cpassword) {
        $error = true;
        $cpassword_error = "Password and Confirm Password doesn't match";
    }

    if (!$error) {
        $query = "INSERT INTO Member (password, firstName, lastName, email, roleId, registrationDate, countryId)
        VALUES (
        crypt('".$_POST['password']."', gen_salt('bf', 8)),
        '".$_POST['fname']."',
        '".$_POST['lname']."',
        '".$_POST['email']."',
        '2',
        '".date("Y-m-d")."',    
        '".$_POST['country']."'
        )";

        $result = pg_query($query);

        if($result) {
            $successmsg = "Successfully Registered! <a href='login.php'>Click here to Login</a>";
        } else {
            $errormsg = "Error in registering. User already exists.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Register</title>

    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
  
    <!-- Custom styles for this template -->
    <link href="main.css" rel="stylesheet">
    
    
  </head>

<body>

<div class="wrapper" style="height: auto;">
    
    <header class="main-header">

    <!-- Logo -->
    <a href="index.php" class="logo">
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>CrowdFunder</b></span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs"></span>
            </a>
          </li>
        </ul>
      </div>

    </nav>
  </header>
<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" style="height:auto;">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
        </div>
        <div class="pull-left info">
          <p>Admin</p>
        </div>
      </div>

  </aside>
    <div class="content-wrapper" style="min-height:916px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-4 col-md-offset-4 well">
                <form role="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="loginform">
                    <fieldset>
                        <legend>Register</legend>
                        <div class="form-group">
                            <label for="name">First Name</label>
                            <input type="text" name="fname" placeholder="Enter First Name" required value="<?php if($error) echo $fname; ?>" class="form-control" />
                            <span class="text-danger"><?php if (isset($name_error)) echo $name_error; ?></span>
                        </div>

                        <div class="form-group">
                            <label for="name">Last Name</label>
                            <input type="text" name="lname" placeholder="Enter Last Name" required value="<?php if($error) echo $lname; ?>" class="form-control" />
                            <span class="text-danger"><?php if (isset($name_error)) echo $name_error; ?></span>
                        </div>
                        
                        <div class="input-group">
                            <label for="name">Country</label>
                            <select name="country" class="form-control">
                                <option value="" disabled selected>Select a country</option>
                                <?php
                                    $query = 'SELECT * FROM Country c';
                                    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
                         
                                    while($row=pg_fetch_assoc($result)) {
                                            echo "<option value=".$row['id'].">".$row['name']."</option>";
                                        }
                                    
                                    pg_free_result($result);
                                ?>                              
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="name">Email</label>
                            <input type="text" name="email" placeholder="Email" required value="<?php if($error) echo $email; ?>" class="form-control" />
                            <span class="text-danger"><?php if (isset($email_error)) echo $email_error; ?></span>
                        </div>

                        <div class="form-group">
                            <label for="name">Password</label>
                            <input type="password" name="password" placeholder="Password" required class="form-control" />
                            <span class="text-danger"><?php if (isset($password_error)) echo $password_error; ?></span>
                        </div>

                        <div class="form-group">
                            <label for="name">Confirm Password</label>
                            <input type="password" name="cpassword" placeholder="Confirm Password" required class="form-control" />
                            <span class="text-danger"><?php if (isset($cpassword_error)) echo $cpassword_error; ?></span>
                        </div>

                        <div class="form-group">
                            <input type="submit" name="signup" value="Sign Up" class="btn btn-primary" />
                        </div>
                    </fieldset>
                </form>
                <span class="text-success"><?php if (isset($successmsg)) { echo $successmsg; } ?></span>
                <span class="text-danger"><?php if (isset($errormsg)) { echo $errormsg; } ?></span>
 
            </div>
        </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4 text-center">    
        Already Registered? <a href="login.php">Login Here</a>
        </div>
    </div>
    </section>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <!-- DataTables -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script src="plugins/bootbox.min.js"></script>
</body>
</html>