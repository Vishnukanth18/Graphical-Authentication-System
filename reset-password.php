<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
            
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                echo ("<script LANGUAGE='JavaScript'>
                window.alert('Password updated successfully, continue to login.');
                window.location.href='login.php';
               </script>");
                exit();
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
      <title>Reset Password</title>
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
                              <h2>Reset Password</h2>
                              <p>Please fill out this form to reset your password.</p>
                              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                 <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" id="pwd" name="new_password"  readonly class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
                                    <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
                                 </div>
                                <div class="form-group">
                                    <label></label>
                                    <input type="hidden" id="pwd2" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                                 </div>
                                 <div class="form-group">
                                    <input type="submit" class="btn btn-primary" value="Submit">
                                    <a class="btn btn-link ml-2" href="welcome.php">Cancel</a>
                                 </div>
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
                    document.getElementById("pwd2").value = document.getElementById("pwd2").value + " "+ strg;
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
      </script>  
   </body>
</html>