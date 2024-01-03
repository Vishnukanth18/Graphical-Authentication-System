<?php
// Initialize the session
session_start();
 
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$email = $pin = "";
$email_err = $pin_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Check if pin is empty
    if(empty(trim($_POST["pin"]))){
        $pin_err = "Please enter your pin.";
    } else{
        $pin = trim($_POST["pin"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($pin_err)){
        // Prepare a select statement
        $sql = "SELECT id, email, pin FROM users WHERE email = :email";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            $param_pin = trim($_POST["pin"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if email exists, if yes then verify pin
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $email = $row["email"];
                        $pin = $row["pin"];
                        if($pin = $param_pin){
                            // Pin is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;                            
                            
                            // Redirect user to welcome page
                            header("location: reset-password.php");
                        } else{
                            // Pin is not valid, display a generic error message
                            $login_err = "Invalid pin.";
                        }
                    }
                } else{
                    // Email doesn't exist, display a generic error message
                    $login_err = "Invalid email.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="style.css" rel="stylesheet" type="text/css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
<section class="vh-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
    <div class="col-lg-12 col-xl-11">
    <div class="card text-black" style="border-radius: 25px;">
    <div class="card-body p-md-5">
    <div class="row justify-content-center">
    <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">

    <div class="wrapper">
        <h2>Forgot Password</h2>
        <p>Please Enter your Email and Security Pin to reset password!.</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Pin</label>
                <input type="password" name="pin" class="form-control <?php echo (!empty($pin_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $pin_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Verify">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </section>
</body>
</html>