<?php
    require "config/config.php";
    session_start();

	// Is this user already logged in?
	if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
		// User is already logged in. Redirect to homepage.
		//header('Location: home.php');
        session_destroy();
	} else {

        // Login POST
        if (isset($_POST['state']))
        {

            // Searches DB based on credentials
            $email = $_POST['email'];
            $password = $_POST['password'];
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ( $mysqli->connect_errno ) {
                echo $mysqli->connect_error;
                exit();
            }

            $mysqli->set_charset('utf8');

            // Handles Login Functionality
            if ($_POST['state'] == 'login' && isset($email) && isset($password)) {

                $login_query = "SELECT fname, lname, email, password
                    FROM users
                    WHERE email = '$email' AND password = '$password'";
                    


                $login_results = $mysqli->query($login_query);

                if ( !$login_results ) {
                    echo $mysqli->error;
                    $mysqli->close();
                    exit();
                }
                else if($login_results->num_rows != 1)
                {
                    $error = "Invalid Login Credentials.";
                }
                else 
                {
                    $row = $login_results->fetch_assoc();
                    $_SESSION['logged_in'] = true;
                    $_SESSION['email'] = $_POST['email'];
                    $_SESSION['fname'] = $row['fname'];

                    header('Location: home.php');
                }
            }
            // Handles Signup Functionality
            else if ($_POST['state'] == 'signup' && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['fname']) && isset($_POST['lname']))
            {
                $signup_check = "SELECT fname, lname, email, password
                    FROM users
                    WHERE email = '$email'";
                    


                $signup_check_results = $mysqli->query($signup_check);

                if (!$signup_check_results) {
                    echo $mysqli->error;
                    $mysqli->close();
                    exit();
                }
                else if ($signup_check_results->num_rows != 0)
                {
                    $error = "Email is already in use. If this is you, please log in instead. Otherwise, use a different email.";
                }
                else
                {
                    $fname = $_POST['fname'];
                    $lname = $_POST['lname'];
                    $signup_query = "INSERT INTO users (fname, lname, email, password)
                        VALUES ('$fname', '$lname', '$email', '$password');";	


                    $signup_result = $mysqli->query($signup_query);

                    if (!$signup_result) {
                        echo $mysqli->error;
                        $mysqli->close();
                        exit();
                    }
                    else {
                        $_SESSION['logged_in'] = true;
                        $_SESSION['email'] = $_POST['email'];
                        $_SESSION['fname'] = $fname;
                        header('Location: home.php');
                    }
                }
            }

            if ($mysqli->ping()) {
                // Connection is open, so close it
                $mysqli->close();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Restaurant Finder | Login</title>
    <?php include "include-require/head.html" ?>
</head>

<body>
    <div id="page">
        <?php include "include-require/nav.php" ?>
        <div class="container-fluid" id="login-form">
            <div class="row text-center mt-4">
                <h1>Login / Signup</h1>
            </div>
            <div class="row px-5 justify-content-center">
                <div class="col-lg-6 col-12 container col-offset-8">
                    <form method="POST">
                        <div class="mt-5 mb-3 text-center">
                            <p class="d-inline me-2">Select one:</p>
                            <input type="radio" class="btn-check" name="state" value="login" id="login">
                            <label class="btn btn-outline-primary" for="login">Login</label>
                            <input type="radio" class="btn-check" name="state" value="signup" id="signup">
                            <label class="btn btn-outline-success" for="signup">Signup</label>
                            <?php if (isset($error) && trim($error) != '' ) : ?>
                                <div class="text-danger font-italic my-3" id="login-error">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="mb-3 hidden-toggle hidden col-6" id="fname-div">
                                <label for="fname" class="form-label">First Name</label>
                                <input type="test" name="fname" class="form-control" id="fname" placeholder="First Name">
                            </div>
                            <div class="mb-3 hidden-toggle hidden col-6" id="lname-div">
                                <label for="lname" class="form-label">Last Name</label>
                                <input type="text" name="lname" class="form-control" id="lname" placeholder="Last Name">
                            </div>
                        </div>
                        <div class="mb-3 hidden-toggle hidden">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="" required>
                        </div>
                        <div class="mb-3 hidden-toggle hidden">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                aria-describedby="passwordHelp" required>
                            <div class="hidden-toggle hidden form-text" id="passwordHelp">
                            </div>
                        </div>
                        <div class="hidden-toggle hidden">
                            <button type="submit" id="submit-button" class="btn btn-primary mb-3 btn-lg"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            let loginRadio = document.getElementById('login');
            let signupRadio = document.getElementById('signup');

            loginRadio.addEventListener('change', function () {
                if (this.checked) {
                    login();
                    document.getElementById("login-error").classList.add("hidden");
                }
            });

            signupRadio.addEventListener('change', function () {
                if (this.checked) {
                    signUp();
                    document.getElementById("login-error").classList.add("hidden");
                }
            });
        });

        function login() {
            let hiddenElements = document.querySelectorAll('.hidden-toggle');
            for (let i = 0; i < hiddenElements.length; i++) {
                hiddenElements[i].classList.remove('hidden');
            }
            let fnameLabel = document.getElementById('fname-div');
            fnameLabel.classList.add("hidden");
            let lnameLabel = document.getElementById('lname-div');
            lnameLabel.classList.add("hidden");
            let fname = document.getElementById('fname');
            fname.required = false;
            let lname = document.getElementById('lname');
            lname.required = false;
            let emailLabel = document.getElementById('email');
            emailLabel.placeholder = "";
            let submitButton = document.getElementById('submit-button');
            submitButton.innerHTML = "Login";
            let passwordHelp = document.getElementById('passwordHelp');
            passwordHelp.innerHTML = "";
        }

        function signUp() {
            let hiddenElements = document.querySelectorAll('.hidden-toggle');
            for (let i = 0; i < hiddenElements.length; i++) {
                hiddenElements[i].classList.remove('hidden');
            }
            let fnameLabel = document.getElementById('fname-div');
            fnameLabel.classList.remove("hidden");
            let fname = document.getElementById('fname');
            fname.required = true;
            let lname = document.getElementById('lname');
            lname.required = true;
            let lnameLabel = document.getElementById('lname-div');
            lnameLabel.classList.remove("hidden");
            let emailLabel = document.getElementById('email');
            emailLabel.placeholder = "name@example.com";
            let submitButton = document.getElementById('submit-button');
            submitButton.innerHTML = "Sign Up";
            let passwordHelp = document.getElementById('passwordHelp');
            passwordHelp.innerHTML = "Your password must be 8 - 20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.";
        }
    </script>
    <?php include "include-require/bootstrap.html" ?>
</body>

</html>