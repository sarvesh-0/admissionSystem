<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
// Start output buffering at the top
ob_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admission_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Report Section (report.php)
$sql = "SELECT * FROM admission_form";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Report</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <style>
            .navbar {
        background: linear-gradient(135deg, #0052d4, #4364f7);
        padding: 0.5rem 1rem;
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
        font-size: 1.5rem;
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
        padding: 0.3rem 0.8rem;
    }

    .nav-link {
        color: #fff;
        font-size: 1rem;
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

    /* Body Styling */
    body {
        background-color: #f8f9fa;
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
    }

    /* Table and Layout Styling */
    .table-container {
        margin: 40px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 1200px;
    }

    .export-btn {
        margin-bottom: 20px;
        background-color: #4364f7;
        color: #fff;
        padding: 10px 20px;
        font-size: 1rem;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.2s ease;
    }

    .export-btn:hover {
        background-color: #0052d4;
        transform: scale(1.05);
    }

    /* Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: white; /* Blue shade for table background */
    }

    table th, table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    table th {
        background-color: #0052d4;
        color: #fff;
    }

/*    table tr:hover {
        background-color: #bbdefb;
        cursor: pointer;
    }*/

    /* Header Styling */
    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header-container h2 {
        margin: 0;
        font-size: 1.8rem;
        color: #333;
    }

    /* Card Style for better UI */
    .card {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .card h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .card p {
        font-size: 1rem;
        color: #666;
    }

    /* Footer Styling */
    .footer {
        background-color: #0052d4;
        padding: 20px;
        color: #fff;
        text-align: center;
        margin-top: 30px;
    }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="#">Admission Portal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="admisionForm.php">Admission Form</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="report.php">Report</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a> <!-- Add Logout link -->
            </li>
        </ul>
    </div>
</nav>
<div class="container table-container">
    <div class="header-container">
        <h2>Admission Report</h2>
    </div>

<!-- Report Table -->
<?php
if ($result->num_rows > 0) {
    echo "<div class='table-responsive'>
            <table id='reportTable' class='table table-striped table-bordered align-middle'>
                 <thead style=\"background-color: #4364f7; color: #fff;\">
                   <tr>
                        <th>Admission ID</th>
                        <th>Title</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Full Name</th>
                        <th>Mother Name</th>
                        <th>Gender</th>
                        <th>Address</th>
                        <th>Taluka</th>
                        <th>District</th>
                        <th>Pin Code</th>
                        <th>State</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Aadhaar Number</th>
                        <th>Date of Birth</th>
                        <th>Age</th>
                        <th>Religion</th>
                        <th>Caste Category</th>
                        <th>Caste</th>
                        <th>Marksheet</th>
                        <th>Photo</th>
                        <th>Signature</th>
                        <th>Physically Handicapped</th>
                        <th>Actions</th>
                   </tr>
                 </thead>
                 <tbody>";

    // Loop through the records and populate the table
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['admission_id']}</td>
                <td>{$row['title']}</td>
                <td>{$row['first_name']}</td>
                <td>{$row['middle_name']}</td>
                <td>{$row['last_name']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['mother_name']}</td>
                <td>{$row['gender']}</td>
                <td>{$row['address']}</td>
                <td>{$row['taluka']}</td>
                <td>{$row['district']}</td>
                <td>{$row['pin_code']}</td>
                <td>{$row['state']}</td>
                <td>{$row['mobile']}</td>
                <td>{$row['email']}</td>
                <td>{$row['aadhaar']}</td>
                <td>{$row['dob']}</td>
                <td>{$row['age']}</td>
                <td>{$row['religion']}</td>
                <td>{$row['caste_category']}</td>
                <td>{$row['caste']}</td>
                <td><img src='{$row['marksheet']}' alt='marksheet' height='50' />
                </td>
                <td><img src='{$row['photo']}' alt='Photo' height='50' /></td>
                <td><img src='{$row['signature']}' alt='Signature' height='50' /></td>
                <td>{$row['physically_handicapped']}</td>
                <td>
                    <a href='edit.php?id={$row['admission_id']}' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='delete_entry.php?id={$row['admission_id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>
                </td>
            </tr>";
    }

    echo "</tbody></table></div>";
} else {
    echo "<p class='text-center'>No records found.</p>";
}
?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<!-- DataTables Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
<!-- JSZip (Required for Excel export) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<!-- Excel Export Plugin for DataTables -->
<script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#reportTable').DataTable({
            scrollX: true,
            pageLength: 5,
            lengthMenu: [5, 10, 15, 20],
            responsive: true,
            dom: 'Bfrtip', // Required to show buttons (B - Buttons)
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    title: 'Admission_Report', // Custom Excel file title
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: ':visible' // Export only visible columns (you can customize)
                    }
                }
            ]
        });
    });
</script>

</body>
</html>

<?php
// End output buffering and send output
ob_end_flush();
?>+