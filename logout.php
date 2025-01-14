
<?php
// logout.php

// Start the session
session_start();

// Destroy all session data
session_unset(); // Removes all session variables
session_destroy(); // Destroys the session

// Redirect to the login page with a success message
echo '<script>
    alert("Logout Successful");
    window.location.href = "login.php";
</script>';
exit;

// header("Location: login.php?message=logout_success");
// exit();
?>
