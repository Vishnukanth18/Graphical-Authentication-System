<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, email, username, password FROM users WHERE email = :email";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if email exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $email = $row["email"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location:todolist\index.php");
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else{
                    // Email doesn't exist, display a generic error message
                    $login_err = "Invalid email or password.";
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
      <title>Login</title>
      <link href="style.css" rel="stylesheet" type="text/css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
      <style>
         body{ font: 14px sans-serif; }
         .wrapper{ width: 360px; padding: 20px; }
         body {background-color: #435165;
         margin: 0;}
         table{
            border: 2px solid black;
         }
         th, td {
         border: 1px solid black;
         padding: 4px;
         }
      </style>
   </head>
   <body onload="generate()">
      <section class="vh-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
               <div class="col-lg-12 col-xl-11">
                  <div class="card text-black" style="border-radius: 25px;">
                     <div class="card-body p-md-5">
                        <div class="row justify-content-center">
                           <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
                              <div class="wrapper">
                                 <h2>Login</h2>
                                 <p>Please fill in your credentials to login.</p>
                                 <?php 
                                    if(!empty($login_err)){
                                        echo '<div class="alert alert-danger">' . $login_err . '</div>';
                                    }        
                                    ?>
                                 <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <div class="form-group">
                                       <label>Email</label>
                                       <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                       <span class="invalid-feedback"><?php echo $email_err; ?></span>
                                    </div>
                                    <div class="form-group">
                                       <label>Password</label>
                                       <input type="password" id="pswd" name="password" maxlength="25" title="Please enter the pin shown with your password symbol in textbox  below symbols table! " readonly class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                       <span class="invalid-feedback"><?php echo $password_err; ?></span>
                                    </div>
                                    <div class="form-group">
                                       <input type="submit" class="btn btn-primary" value="Login">
                                    </div>
                                    <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
                                    <p>Forgot Password? <a href="forgot-password.php">Reset Here</a>.</p>
                                 </form>
                              </div>
                           </div>
                           <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">
                              <table>
                                 <tr>
                                    <th colspan="8">Enter one pin at a time shown with your password symbols sequence</th>
                                 </tr>
                                 <!--Row1-->
                                 <tr>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-clock">
                                       <i class="fas fa-clock fa-lg"></i>
                                       </button> 
                                       <input type="text" id="pin1" name="fas fa-clock"  maxlength="4" size="4" readonly>
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg " id="fas fa-anchor">
                                       <i class="fas fa-anchor fa-lg"></i>
                                       </button>
                                       <input type="text" id="pin2" name="fas fa-anchor" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg " id="fas fa-ambulance"  >
                                       <i class="fas fa-ambulance fa-lg"></i>
                                       </button>
                                       <input type="text" id="pin3" name="fas fa-ambulance" maxlength="4" size="4" readonly>
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-address-card"  >
                                       <i class="fas fa-address-card fa-lg"></i>
                                       </button>
                                       <input type="text" id="pin4" name="fas fa-address-card" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-bicycle"  >
                                       <i class="fas fa-bicycle fa-lg"></i>
                                       </button>
                                       <input type="text" id="pin5" name="fas fa-bicycle" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg"  id="fas fa-bell"  >
                                       <i class="fas fa-bell fa-lg"></i>
                                       </button>
                                       <input type="text" id="pin6" name="fas fa-bell" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-book"  >
                                       <i class="fas fa-book fa-lg"></i>
                                       </button><input type="text" id="pin7" name="fas fa-book" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-building"  >
                                       <i class="fas fa-building fa-lg"></i>
                                       </button><input type="text" id="pin8" name="fas fa-building" maxlength="4" size="4" readonly> 
                                    </td>
                                 </tr>
                                 <!--Row2-->
                                 <tr>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-apple-alt"  >
                                       <i class="fas fa-apple-alt fa-lg"></i>
                                       </button><input type="text" id="pin9" name="fas fa-apple-alt" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-beer"  >
                                       <i class="fas fa-beer fa-lg"></i>
                                       </button><input type="text" id="pin10" name="fas fa-beer" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-cog"  >
                                       <i class="fas fa-cog fa-lg"></i>
                                       </button><input type="text" id="pin11" name="fas fa-cog" maxlength="4" size="4" readonly>
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-music"  >
                                       <i class="fas fa-music fa-lg"></i>
                                       </button><input type="text" id="pin12" name="fas fa-music" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-envelope"   >
                                       <i class="fas fa-envelope fa-lg"></i>
                                       </button><input type="text" id="pin13" name="fas fa-envelope" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-star"   >
                                       <i class="fas fa-star fa-lg"></i>
                                       </button><input type="text" id="pin14" name="fas fa-star" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-tag"   >
                                       <i class="fas fa-tag fa-lg"></i>
                                       </button><input type="text" id="pin15" name="fas fa-tag" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-home"   >
                                       <i class="fas fa-home fa-lg"></i>
                                       </button><input type="text" id="pin16" name="fas fa-home" maxlength="4" size="4" readonly> 
                                    </td>
                                 </tr>
                                 <!--Row3-->
                                 <tr>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-camera"   >
                                       <i class="fas fa-camera fa-lg"></i>
                                       </button><input type="text" id="pin17" name="fas fa-camera" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-key"   >
                                       <i class="fas fa-key fa-lg"></i>
                                       </button><input type="text" id="pin18" name="fas fa-key" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-thumbs-up"   >
                                       <i class="fas fa-thumbs-up fa-lg"></i>
                                       </button><input type="text" id="pin19" name="fas fa-thumbs-up" maxlength="4" size="4" readonly>
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-magnet"   >
                                       <i class="fas fa-magnet fa-lg"></i>
                                       </button><input type="text" id="pin20" name="fas fa-magnet" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-trophy"   >
                                       <i class="fas fa-trophy fa-lg"></i>
                                       </button><input type="text" id="pin21" name="fas fa-trophy" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-phone-square"  >
                                       <i class="fas fa-phone-square fa-lg"></i>
                                       </button><input type="text" id="pin22" name="fas fa-phone-square" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-filter"  >
                                       <i class="fas fa-filter fa-lg"></i>
                                       </button><input type="text" id="pin23" name="fas fa-filter" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-briefcase"  >
                                       <i class="fas fa-briefcase fa-lg"></i>
                                       </button><input type="text" id="pin24" name="fas fa-briefcase" maxlength="4" size="4" readonly> 
                                    </td>
                                 </tr>
                                 <!--Row4-->
                                 <tr>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-cloud"  >
                                       <i class="fas fa-cloud fa-lg"></i>
                                       </button><input type="text" id="pin25" name="fas fa-cloud" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-rocket"  >
                                       <i class="fas fa-rocket fa-lg"></i>
                                       </button><input type="text" id="pin26" name="fas fa-rocket" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-coffee"  >
                                       <i class="fas fa-coffee fa-lg"></i>
                                       </button><input type="text" id="pin27" name="fas fa-coffee" maxlength="4" size="4" readonly>
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-database"  >
                                       <i class="fas fa-database fa-lg"></i>
                                       </button><input type="text" id="pin28" name="fas fa-database" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-desktop"  >
                                       <i class="fas fa-desktop fa-lg"></i>
                                       </button><input type="text" id="pin29" name="fas fa-desktop" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-dragon"  >
                                       <i class="fas fa-dragon fa-lg"></i>
                                       </button><input type="text" id="pin30" name="fas fa-dragon" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-fan"  >
                                       <i class="fas fa-fan fa-lg"></i>
                                       </button><input type="text" id="pin31" name="fas fa-fan" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-feather"  >
                                       <i class="fas fa-feather fa-lg"></i>
                                       </button><input type="text" id="pin32" name="fas fa-feather" maxlength="4" size="4" readonly> 
                                    </td>
                                 </tr>
                                 <!--Row 5-->
                                 <tr>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-infinity"  >
                                       <i class="fas fa-infinity fa-lg"></i>
                                       </button><input type="text" id="pin33" name="fas fa-infinity" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-peace"  >
                                       <i class="fas fa-peace fa-lg"></i>
                                       </button><input type="text" id="pin34" name="fas fa-peace" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-fish"  >
                                       <i class="fas fa-fish fa-lg"></i>
                                       </button><input type="text" id="pin35" name="fas fa-fish" maxlength="4" size="4" readonly>
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-globe"  >
                                       <i class="fas fa-globe fa-lg"></i>
                                       </button><input type="text" id="pin36" name="fas fa-globe" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-guitar"  >
                                       <i class="fas fa-guitar fa-lg"></i>
                                       </button><input type="text" id="pin37" name="fas fa-guitar" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-handshake" onclick="getClickID(this.name)">
                                       <i class="fas fa-handshake fa-lg"></i>
                                       </button><input type="text" id="pin38" name="fas fa-handshake" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-headphones"  >
                                       <i class="fas fa-headphones fa-lg"></i>
                                       </button><input type="text" id="pin39" name="fas fa-headphones" maxlength="4" size="4" readonly> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-hourglass"  >
                                       <i class="fas fa-hourglass fa-lg"></i>
                                       </button><input type="text" id="pin40" name="fas fa-hourglass" maxlength="4" size="4" readonly> 
                                    </td>
                                 </tr>
                                 <tr>
                                    <td colspan="8" align="center">
                                       <br>
                                       <input type="text" id="pwd" maxlength="4" size="4">
                                       <button class="btn-primary" onclick="myfun()">Submit</button>
                                       <br>
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
      </section>
      <script>
      function generate() {
        let length = 4;
        const characters = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+=-';
        const charactersLength = characters.length;
        for(let x=1; x<41; x++){
         let result = ' ';
        for(let i = 0; i < length; i++) {
            result += 
            characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        document.getElementById("pin"+x).value = result;
      }
    }
    function myfun(){
     var str1= document.getElementById("pwd").value;
  
      for(let i=1;i<41;i++){
        var res = document.getElementById("pin"+i).value;
        let string1= str1.trim();
        let string2= res.trim();
        const result = string1.localeCompare(string2);
        if(result == 0) {
           let rslt=document.getElementById("pin"+i).name;
           document.getElementById("pswd").value = document.getElementById("pswd").value + " "+ rslt;
           break;
         } 
      }
      document.getElementById("pwd").value=""
      generate()
    }
    </script>
   </body>
</html>