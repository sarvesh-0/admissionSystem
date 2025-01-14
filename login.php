<?php
session_start(); // Start the session

if (isset($_SESSION['email'])) {
    header("Location: admisionForm.php");
    exit();
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admission_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $captcha = trim($_POST['captcha']);
// Login Page (login.php)
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
//     $email = $_POST['email'];
//     $password = $_POST['password'];
//     $captcha = $_POST['captcha'];

    // $_SESSION['email'] = $email;

    // Check if the captcha is correct
//     if ($_SESSION['captcha'] !== $captcha) {
//         echo "<script>alert('Invalid captcha!');</script>";
//     } else {
//         $sql = "SELECT * FROM users WHERE email = '$email'";
//         $result = $conn->query($sql);

//         if ($result->num_rows > 0) {
//             $user = $result->fetch_assoc();
//             if (password_verify($password, $user['password'])) {
//                 echo "<script>alert('Login successful! Redirecting to form.'); window.location.href = 'admisionForm.php';</script>";
//             } else {
//                 echo "<script>alert('Invalid credentials!');</script>";
//             }
//         } else {
//             echo "<script>alert('User does not exist!');</script>";
//         }
//     }
// }

    // Check if CAPTCHA matches
    if (!isset($_SESSION['captcha']) || $_SESSION['captcha'] !== $captcha) {
        $msg = "Invalid CAPTCHA! Please try again.";
        $alertClass = "alert-danger";
    } else {
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['email'] = $email;
                header("Location: admisionForm.php");
                exit();
            } else {
                $msg = "Invalid credentials! Please try again.";
                $alertClass = "alert-danger";
            }
        } else {
            $msg = "User does not exist!";
            $alertClass = "alert-danger";
        }
        $stmt->close();
    }
$conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f7;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background: linear-gradient(135deg, #0052d4, #4364f7);
            padding: 0.5rem 1rem; /* Reduced padding for a smaller navbar */
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: background 0.3s ease;
        }

        .navbar:hover {
            background: linear-gradient(135deg, #003366, #66ccff);
        }

        .navbar-brand {
            color: #fff;
            font-size: 1.5rem; /* Slightly smaller font size */
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .navbar-brand:hover {
            color: #6fb1fc;
        }

        .navbar-toggler {
            border: none;
            outline: none;
        }

        .navbar-toggler-icon {
            background-color: #fff;
        }

        .navbar-nav {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-grow: 1;
        }

        .nav-item {
            padding: 0.3rem 0.8rem; /* Reduced padding for smaller items */
        }

        .nav-link {
            color: #fff;
            font-size: 1rem; /* Smaller font size */
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.8px;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #6fb1fc;
            text-decoration: none;
        }

        @media (max-width: 991px) {
            .navbar-nav {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
                padding: 0.8rem;
            }

            .nav-item {
                width: 100%;
                text-align: left;
            }

            .nav-link {
                padding: 0.6rem 1.2rem;
                font-size: 1.1rem;
            }
        }

        .login-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 50px auto;
        }
        .login-container h2 {
            color: #007bff;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-control {
            border-radius: 8px;
            padding: 10px;
        }
        .captcha-img {
            margin-bottom: 20px;
        }
        button[type="submit"] {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        .alert {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#">Admission Portal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.html">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.html">Contact</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container login-container">
        <h2>Login</h2>

        <form method="POST" id="loginForm">
            <div class="form-group mb-3">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="form-group mb-3">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
            </div>

            <div class="form-group mb-3 captcha-img">
                <label for="captcha">Captcha</label><br>
                <img src="captcha.php" alt="Captcha Image" class="img-fluid" style="max-width: 100px;">
            </div>

            <div class="form-group mb-3">
                <input type="text" name="captcha" id="captcha" class="form-control" placeholder="Enter Captcha" required>
            </div>

            <button type="submit" name="login">Login</button>
        </form>

        <!-- Success/Error Message (Example) -->
        <?php if (isset($msg)) { ?>
            <div class="alert alert-success mt-3"><?php echo $msg; ?></div>
        <?php } ?>

        <!-- Register Link -->
        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register Here</a></p>
        </div>
    </div>

    <!-- Bootstrap JS & Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- jQuery for form validation -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function(e) {
                var email = $('#email').val();
                var password = $('#password').val();
                var captcha = $('#captcha').val();

                // Basic validation
                if (email === "" || password === "" || captcha === "") {
                    alert("Please fill all the fields!");
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>