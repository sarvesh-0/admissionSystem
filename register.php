<?php

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admission_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$fname = $mname = $lname = $mobile = $email = $password = $confirm_password = "";

// Registration Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $fullname = $fname . ' ' . $mname . ' ' . $lname;
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


    // Validate mobile number
    if (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
        echo "<script>alert('Invalid mobile number. Please enter a valid 10-digit number starting with 6, 7, 8, or 9.');</script>";
        $error = true;
    }

    // Validate password
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
        $error = true;
    }

    // Validate photo
    $photo = $_FILES['photo'];
    if ($photo['size'] > 1048576) {
        echo "<script>alert('File size should be under 1MB!');</script>";
        $error = true;
    } else {
        $allowed = ['jpeg', 'jpg', 'png'];
        $file_ext = pathinfo($photo['name'], PATHINFO_EXTENSION);

        if (!in_array(strtolower($file_ext), $allowed)) {
            echo "<script>alert('Invalid file format!');</script>";
            $error = true;
        }
        if (!$error) {
            $photo_name = uniqid() . '.' . $file_ext;
            move_uploaded_file($photo['tmp_name'], "uploads/$photo_name");
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);


                $sql = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, full_name, mobile_number, photo, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $sql->bind_param("ssssssss", $fname, $mname, $lname, $fullname, $mobile,$photo_name, $email, $hashed_password);
                if ($sql->execute()) {
                    echo "<script>alert('Registration successful! Redirecting to login.'); window.location.href = 'login.php';</script>";
                } else {
                    echo "<script>alert('Error occurred. Please try again.');</script>";
                }
                // $sql = "INSERT INTO users (first_name, middle_name, last_name, full_name, mobile, email, password, photo) 
                //         VALUES ('$fname', '$mname', '$lname', '$fullname', '$mobile', '$email', '$hashed_password', '$photo_name')";

                // if ($conn->query($sql) === TRUE) {
                //     echo "<script>alert('Registration successful! Redirecting to login.'); window.location.href = 'login.php';</script>";
                // } else {
                //     echo "<script>alert('Error: " . $conn->error . "');</script>";
                // }
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Portal - Registration</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ffffff, #f1f1f1, #e0e0e0);
            font-family: 'Poppins', sans-serif;
            color: #333;
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
    color: black;
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



        .register-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 500px;
            margin: 50px auto;
        }

        h2 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: #0052d4;
        }

        .form-floating label {
            font-size: 0.9rem;
            color: #555;
        }

        .form-control {
            border-radius: 10px;
        }

        .form-control:focus {
            border-color: #0052d4;
            box-shadow: 0 0 4px rgba(0, 82, 212, 0.5);
        }

        .btn-primary {
            background: linear-gradient(90deg, #4364f7, #6fb1fc);
            border: none;
            font-weight: bold;
            border-radius: 8px;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #6fb1fc, #4364f7);
            transform: scale(1.05);
        }

        .scrollable-form {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #0052d4;
            font-weight: bold;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
            color: #4364f7;
        }

        .text-danger {
            font-size: 0.875rem;
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
                <a class="nav-link" href="#">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Contact</a>
            </li>
        </ul>
    </div>
</nav>


<!-- Registration Form -->
<div class="container register-container">
    <h2>Register</h2>
    <div class="scrollable-form">
        <form method="POST" enctype="multipart/form-data" id="registrationForm">
            <div class="form-floating mb-3">
                <input type="text" name="fname" id="fname" class="form-control" placeholder="First Name" value="<?= htmlspecialchars($fname) ?>" required>
                <label for="fname">First Name</label>
                <div id="fnameError" class="text-danger"></div>
            </div>

            <div class="form-floating mb-3">
                <input type="text" name="mname" id="mname" class="form-control" placeholder="Middle Name" value="<?= htmlspecialchars($mname) ?>" required>
                <label for="mname">Middle Name</label>
                <div id="mnameError" class="text-danger"></div>
            </div>

            <div class="form-floating mb-3">
                <input type="text" name="lname" id="lname" class="form-control" placeholder="Last Name" value="<?= htmlspecialchars($lname) ?>" required>
                <label for="lname">Last Name</label>
                <div id="lnameError" class="text-danger"></div>
            </div>

            <div class="form-floating mb-3">
                <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile Number" value="<?= htmlspecialchars($mobile) ?>" maxlength="10" required>
                <label for="mobile">Mobile Number</label>
                <div id="mobileError" class="text-danger"></div>
            </div>

            <div class="mb-3">
                <label for="photo" class="form-label">Upload Photo</label>
                <input type="file" name="photo" id="photo" class="form-control" accept=".jpeg, .jpg, .png" required>
                <div id="photoError" class="text-danger"></div>
            </div>

            <div class="form-floating mb-3">
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
                <label for="email">Email</label>
                <div id="emailError" class="text-danger"></div>
            </div>

            <div class="form-floating mb-3">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                <label for="password">Password</label>
                <div id="passwordError" class="text-danger"></div>
            </div>

            <div class="form-floating mb-3">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
                <label for="confirm_password">Confirm Password</label>
                <div id="confirmPasswordError" class="text-danger"></div>
            </div>

            <button type="submit" name="register" class="btn btn-primary">Register</button>
        </form>
    </div>
    <div class="login-link">
        <p>Already have an account? <a href="login.php">Login Here</a></p>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Form Validation Script -->
<script>
    $(document).ready(function() {
        $('#registrationForm').submit(function(e) {
            let isValid = true;

            // Clear previous error messages
            $('.text-danger').text('');

            // First Name Validation
            if ($('#fname').val() === '') {
                $('#fnameError').text('First name is required.');
                isValid = false;
            }

            // Mobile Validation
            const mobile = $('#mobile').val();
            if (mobile.length !== 10 || isNaN(mobile)) {
                $('#mobileError').text('Enter a valid 10-digit mobile number.');
                isValid = false;
            }

            // Email Validation
            const email = $('#email').val();
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailPattern.test(email)) {
                $('#emailError').text('Enter a valid email address.');
                isValid = false;
            }

            // Password Validation
            const password = $('#password').val();
            const confirmPassword = $('#confirm_password').val();
            if (password !== confirmPassword) {
                $('#confirmPasswordError').text('Passwords do not match.');
                isValid = false;
            }

            // Prevent form submission if validation fails
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>

</body>
</html>



