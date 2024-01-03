<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $email = $phone= $pin = $cpin = $password ="";
$username_err = $email_err = $phone_err = $pin_err = $cpin_err = $password_err= "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";     
    } 
     else{
      $sql = "SELECT id FROM users WHERE email = :email";
        
      if($stmt = $pdo->prepare($sql)){
          // Bind variables to the prepared statement as parameters
          $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
          
          // Set parameters
          $param_email = trim($_POST["email"]);
          
          // Attempt to execute the prepared statement
          if($stmt->execute()){
              if($stmt->rowCount() == 1){
                  $email_err = "This email is already taken.";
              } else{
                  $email = trim($_POST["email"]);
              }
          } else{
              echo "Oops! Something went wrong. Please try again later.";
          }

          // Close statement
          unset($stmt);
         }
    }
    //validate pin
    if(empty(trim($_POST["pin"]))){
      $pin_err = "Please enter a pin.";     
  } elseif(strlen(trim($_POST["pin"])) < 6){
      $pin_err = "Pin must have  6 characters.";
  } else{
      $pin = trim($_POST["pin"]);
  }
  
  // Validate confirm password
  if(empty(trim($_POST["cpin"]))){
      $cpin_err = "Please confirm password.";     
  } else{
      $cpin = trim($_POST["cpin"]);
      if(empty($pin_err) && ($pin != $cpin)){
          $cpin_err = "Pins did not match.";
      }
  }
   /*
    if(empty(trim($_POST["phone"]))){
      $phone_err = "Please enter phone.";     
   }

   else{
   $sql = "SELECT id FROM users WHERE phone = :phone";
        
   if($stmt = $pdo->prepare($sql)){
       // Bind variables to the prepared statement as parameters
       $stmt->bindParam(":phone", $param_phone, PDO::PARAM_STR);
       
       // Set parameters
       $param_phone = trim($_POST["phone"]);
       
       // Attempt to execute the prepared statement
       if($stmt->execute()){
           if($stmt->rowCount() == 1){
               $phone_err = "This phone is already taken.";
           } else{
               $phone = trim($_POST["phone"]);
           }
       } else{
           echo "Oops! Something went wrong. Please try again later.";
       }

       // Close statement
       unset($stmt);
      }
   }
    */
        // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($email_err)  && empty($pin_err) && empty($cpin_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, email, pin) VALUES (:username, :password, :email, :pin)";
         
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":pin", $param_pin, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_email = $email;
            $param_pin = $pin;
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                echo ("<script LANGUAGE='JavaScript'>
                 window.alert('User Account Created Successfully, continue to login.');
                 window.location.href='login.php';
                </script>");

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
      <title>Sign Up</title>
      <link href="style.css" rel="stylesheet" type="text/css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
      <style>
         body{ font: 14px sans-serif; }
         .wrapper{ width: 360px; padding: 20px; }
         body {background-color: #435165;
         margin: 0;}
         th, td {
         border: 1px white;
         padding: 3px;
         }
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
                                 <h2>Sign Up</h2>
                                 <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <div class="form-group">
                                       <label>Name</label>
                                       <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                                       <span class="invalid-feedback"><?php echo $username_err; ?></span>
                                    </div>
                                    <div class="form-group">
                                       <label>Password</label>
                                       <input type="password" id="pwd" name="password" title="please click on symbols"  readonly class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                                       <span class="invalid-feedback"><?php echo $password_err; ?></span>
                                    </div>
                                    <div class="form-group">
                                       <label>Email</label>
                                       <input type="email" name="email" required id="" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"  >
                                       <span class="invalid-feedback"><?php echo $email_err; ?></span>
                                    </div>
                                  <!--  <div class="form-group">
                                       <label>Phone</label>
                                       <input type="tel" name="phone" required id="phone" maxlength="10" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" >
                                       <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                                    </div>
                                    -->
                                    <div class="form-group">
                                       <label>Pin</label>
                                       <input type="password" name="pin" required id="mypin" minlength="6" maxlength="6" class="form-control <?php echo (!empty($pin_err)) ? 'is-invalid' : ''; ?>" 
                                       <span class="invalid-feedback"><?php echo $pin_err; ?></span>
                                       <label>Confirm Pin</label>
                                       <input type="password" name="cpin" required id="mypin2" minlength="6" maxlength="6" class="form-control <?php echo (!empty($cpin_err)) ? 'is-invalid' : ''; ?>" >
                                       <span class="invalid-feedback"><?php echo $cpin_err; ?></span>
                                    </div>
                                    <div class="form-group">
                                    <input type="checkbox" onclick="pinFunction()">Show Pin
                                    </div>
                                    <div class="form-group">
                                       <input type="submit" class="btn btn-primary" value="Submit">
                                       <input type="button" class="btn btn-secondary ml-2" value="Reset" onClick="document.location.reload(true)">
                                    </div>
                                    <p>Already have an account? <a href="login.php">Login here</a>.</p>
                                    <p>Forgot Password? <a href="forgot-password.php">click here</a>.</p>
                                    <div>
                                       <input type="hidden" id="mypwd">
                                    </div>
                                 </form>
                              </div>
                           </div>
                           <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">
                              <table>
                                 <tr>
                                    <th colspan="8">Click on Symbols to set Password</th>
                                 </tr>
                                 <!--Row1-->
                                 <tr>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-clock" onclick="getClickID(this.id)">
                                       <i class="fas fa-clock fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg " id="fas fa-anchor" onclick="getClickID(this.id)">
                                       <i class="fas fa-anchor fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg " id="fas fa-ambulance" onclick="getClickID(this.id)">
                                       <i class="fas fa-ambulance fa-lg"></i>
                                       </button>
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-address-card" onclick="getClickID(this.id)">
                                       <i class="fas fa-address-card fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-bicycle" onclick="getClickID(this.id)">
                                       <i class="fas fa-bicycle fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg"  id="fas fa-bell" onclick="getClickID(this.id)">
                                       <i class="fas fa-bell fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-book" onclick="getClickID(this.id)">
                                       <i class="fas fa-book fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-building" onclick="getClickID(this.id)">
                                       <i class="fas fa-building fa-lg"></i>
                                       </button> 
                                    </td>
                                 </tr>
                                 <!--Row2-->
                                 <tr>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-apple-alt" onclick="getClickID(this.id)">
                                       <i class="fas fa-apple-alt fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-beer" onclick="getClickID(this.id)">
                                       <i class="fas fa-beer fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-cog" onclick="getClickID(this.id)">
                                       <i class="fas fa-cog fa-lg"></i>
                                       </button>
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-music" onclick="getClickID(this.id)">
                                       <i class="fas fa-music fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-envelope"  onclick="getClickID(this.id)">
                                       <i class="fas fa-envelope fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-star"  onclick="getClickID(this.id)">
                                       <i class="fas fa-star fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-tag"  onclick="getClickID(this.id)">
                                       <i class="fas fa-tag fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-home"  onclick="getClickID(this.id)">
                                       <i class="fas fa-home fa-lg"></i>
                                       </button> 
                                    </td>
                                 </tr>
                                 <!--Row3-->
                                 <tr>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-camera"  onclick="getClickID(this.id)">
                                       <i class="fas fa-camera fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-key"  onclick="getClickID(this.id)">
                                       <i class="fas fa-key fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-thumbs-up"  onclick="getClickID(this.id)">
                                       <i class="fas fa-thumbs-up fa-lg"></i>
                                       </button>
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-magnet"  onclick="getClickID(this.id)">
                                       <i class="fas fa-magnet fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-trophy"  onclick="getClickID(this.id)">
                                       <i class="fas fa-trophy fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-phone-square" onclick="getClickID(this.id)">
                                       <i class="fas fa-phone-square fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-filter" onclick="getClickID(this.id)">
                                       <i class="fas fa-filter fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-briefcase" onclick="getClickID(this.id)">
                                       <i class="fas fa-briefcase fa-lg"></i>
                                       </button> 
                                    </td>
                                 </tr>
                                 <!--Row4-->
                                 <tr>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-cloud" onclick="getClickID(this.id)">
                                       <i class="fas fa-cloud fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-rocket" onclick="getClickID(this.id)">
                                       <i class="fas fa-rocket fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-coffee" onclick="getClickID(this.id)">
                                       <i class="fas fa-coffee fa-lg"></i>
                                       </button>
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-database" onclick="getClickID(this.id)">
                                       <i class="fas fa-database fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-desktop" onclick="getClickID(this.id)">
                                       <i class="fas fa-desktop fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-dragon" onclick="getClickID(this.id)">
                                       <i class="fas fa-dragon fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-fan" onclick="getClickID(this.id)">
                                       <i class="fas fa-fan fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-feather" onclick="getClickID(this.id)">
                                       <i class="fas fa-feather fa-lg"></i>
                                       </button> 
                                    </td>
                                 </tr>
                                 <!--Row 5-->
                                 <tr>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-infinity" onclick="getClickID(this.id)">
                                       <i class="fas fa-infinity fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-peace" onclick="getClickID(this.id)">
                                       <i class="fas fa-peace fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-fish" onclick="getClickID(this.id)">
                                       <i class="fas fa-fish fa-lg"></i>
                                       </button>
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-globe" onclick="getClickID(this.id)">
                                       <i class="fas fa-globe fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-guitar" onclick="getClickID(this.id)">
                                       <i class="fas fa-guitar fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-handshake" onclick="getClickID(this.id)">
                                       <i class="fas fa-handshake fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-headphones" onclick="getClickID(this.id)">
                                       <i class="fas fa-headphones fa-lg"></i>
                                       </button> 
                                    </td>
                                    <td align ="center">
                                       <button class="btn btn-primary btn-lg" id="fas fa-hourglass" onclick="getClickID(this.id)">
                                       <i class="fas fa-hourglass fa-lg"></i>
                                       </button> 
                                    </td>
                                 </tr>
                                 <tr>
                                    <td colspan="8">
                                       <br>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td colspan="8">
                                       Selected password:
                                    </td>
                                 </tr>
                                 <tr>
                                    <td colspan="8">
                                       _________________________________________________________________________
                                    </td>
                                 </tr>
                                 <tr id="mypwd2" >
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
         function getClickID(clickID) {
                     var strg = clickID;
                    document.getElementById("pwd").value = document.getElementById("pwd").value + " "+ strg;
                    var a = [];
                    strg1="<td> <button class="+'"'+"btn btn-success btn-lg"+'">';
                    strg2="<i class=";
                    strg3='"'+strg+' fa-lg"';
                    strg4="></i>";
                    strg5="</button></td>";
                    strg6=strg1+strg2+strg3+strg4+strg5;
                    document.getElementById("mypwd").value = document.getElementById("mypwd").value + strg6;
                    var strg7 =  document.getElementById("mypwd").value;
                    document.getElementById("mypwd2").innerHTML = strg7;
         }
         function pinFunction() {
            var x = document.getElementById("mypin");
            if (x.type === "password") {
               x.type = "text";
            } else {
               x.type = "password";
            }
            var y = document.getElementById("mypin2");
            if (y.type === "password") {
               y.type = "text";
            } else {
               y.type = "password";
            }
         }
      </script>  
   </body>
</html>