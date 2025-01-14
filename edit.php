<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
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

$admission_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (!empty($admission_id)) {
    $query = "SELECT * FROM admission_form WHERE admission_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $admission_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row) {
        echo "Record not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

$upload_dir = "uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $fname = htmlspecialchars($_POST['fname']);
    $mname = htmlspecialchars($_POST['mname']);
    $lname = htmlspecialchars($_POST['lname']);
    $fullname = $fname . ' ' . $mname . ' ' . $lname;
    $mother_name = htmlspecialchars($_POST['mother_name']);
    $gender = $_POST['gender'];
    $address = htmlspecialchars($_POST['address']);
    $taluka = htmlspecialchars($_POST['taluka']);
    $district = htmlspecialchars($_POST['district']);
    $pin_code = $_POST['pin_code'];
    $state = htmlspecialchars($_POST['state']);
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $aadhaar = $_POST['aadhaar'];
    $dob = $_POST['dob'];
    $age = date_diff(date_create($dob), date_create('today'))->y;
    $religion = $_POST['religion'];
    $caste_category = htmlspecialchars($_POST['caste_category']);
    $caste = htmlspecialchars($_POST['caste']);
    $physically_handicapped = $_POST['physically_handicapped'];

    $marksheet = $_FILES['marksheet']['name'] ? uniqid() . "_" . $_FILES['marksheet']['name'] : $row['marksheet'];
    $photo = $_FILES['photo']['name'] ? uniqid() . "_" . $_FILES['photo']['name'] : $row['photo'];
    $signature = $_FILES['signature']['name'] ? uniqid() . "_" . $_FILES['signature']['name'] : $row['signature'];

    $files_to_upload = [
        'marksheet' => $marksheet,
        'photo' => $photo,
        'signature' => $signature,
    ];

    foreach ($files_to_upload as $key => $file_name) {
        if ($_FILES[$key]['name']) {
            $file_path = $upload_dir . $file_name;
            $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            if ($_FILES[$key]['size'] > 1048576 || !in_array($file_extension, ['jpeg', 'jpg', 'png'])) {
                echo "<script>alert('Invalid file for $key! Only JPG, JPEG, PNG files are allowed and size should be under 1MB.');</script>";
                exit();
            }
            if (!move_uploaded_file($_FILES[$key]['tmp_name'], $file_path)) {
                echo "<script>alert('Failed to upload $key.');</script>";
                exit();
            }
        }
    }

    $updatequery = "UPDATE admission_form SET title=?, first_name=?, middle_name=?, last_name=?, full_name=?, mother_name=?, gender=?, 
        address=?, taluka=?, district=?, pin_code=?, state=?, mobile=?, email=?, aadhaar=?, dob=?, age=?, 
        religion=?, caste_category=?, caste=?, physically_handicapped=?, marksheet=?, photo=?, signature=? WHERE admission_id=?";
    $stmt = $conn->prepare($updatequery);
    $stmt->bind_param(
        "sssssssssssssssssssssssss",
        $title, $fname, $mname, $lname, $fullname, $mother_name, $gender, $address,
        $taluka, $district, $pin_code, $state, $mobile, $email, $aadhaar, $dob, $age,
        $religion, $caste_category, $caste, $physically_handicapped, $marksheet, $photo, $signature, $admission_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Form updated successfully!'); window.location.href = 'report.php';</script>";
    } else {
        echo "<script>alert('Error updating form: " . $stmt->error . "');</script>";
    }
}

$conn->close();
?>



<?php if (!empty($row)): ?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Form</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
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

    body {
        background-color: #f8f9fa;
        font-family: 'Arial', sans-serif;
    }
    .form-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 30px;
    margin-top: 50px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
    height: 80vh; /* Set height to 80% of the viewport height */
    overflow-y: auto; /* Enable vertical scrolling if content overflows */
}

    .form-container h2 {
        text-align: center;
        color: #007bff;
        margin-bottom: 30px;
    }
    .form-group input[type="radio"] {
        margin-right: 10px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .file-input {
        padding: 5px;
        border: 2px solid #ddd;
        border-radius: 4px;
        display: block;
        width: 100%;
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
        margin-top: 20px;
        text-align: center;
        font-weight: bold;
    }
    .b{
        background-color: #007bff;
        color: white;
        font-weight: bold;
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 5px;
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

    <div class="container form-container">
    <h2>Admission Form</h2>

    <form method="POST" enctype="multipart/form-data">
        <!-- Form Fields -->
        <div class="form-group">
                    <label for="admission_id">Admission Id</label>
                    <input type="text" name="admission_id" id="admission_id" class="form-control" disabled value="<?= htmlspecialchars($row['admission_id'] ?? '') ?>" required>
        </div>
        <div class="row">
            <div class="col-md-6 col-12 mb-3">
            <div class="form-group">
    <label for="title">Title</label>
    <select name="title" id="title" class="form-control" required>
        <option value="">Select Title</option>
        <option value="Mr." <?php echo ($row['title'] == 'Mr.') ? 'selected' : ''; ?>>Mr.</option>
        <option value="Mrs." <?php echo ($row['title'] == 'Mrs.') ? 'selected' : ''; ?>>Mrs.</option>
        <option value="Mrx." <?php echo ($row['title'] == 'Mrx.') ? 'selected' : ''; ?>>Mrx.</option>
    </select>
</div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="fname">First Name</label>
                    <input type="text" name="fname" id="fname" class="form-control" placeholder="First Name" value="<?= htmlspecialchars($row['first_name'] ?? '') ?>" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="mname">Middle Name</label>
                    <input type="text" name="mname" id="mname" class="form-control" placeholder="Middle Name" value="<?= htmlspecialchars($row['middle_name'] ?? '') ?>" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="lname">Last Name</label>
                    <input type="text" name="lname" id="lname" class="form-control" placeholder="Last Name" value="<?= htmlspecialchars($row['last_name'] ?? '') ?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="mother_name">Mother's Name</label>
                    <input type="text" name="mother_name" id="mother_name" class="form-control" placeholder="Mother's Name" value="<?= htmlspecialchars($row['mother_name'] ?? '') ?>" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
            <div class="form-group">
    <label for="gender">Gender</label>
    <select name="gender" id="gender" class="form-control" required>
        <option value="">Select Gender</option>
        <option value="Male" <?php echo ($row['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
        <option value="Female" <?php echo ($row['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
        <option value="Other" <?php echo ($row['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
    </select>
</div>

            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" class="form-control" placeholder="Address" value="<?= htmlspecialchars($row['address'] ?? '') ?>" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="district">District</label>
                    <select id="district" name="district" class="form-control" required>
                    <option value="">Select District</option>
                    <?php foreach ($ $district as $dist => $talukas): ?>
                    <option value="<?= $dist ?>" <?= ($district == $dist) ? 'selected' : '' ?>><?= $dist ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="taluka">Taluka</label>
                    <select id="taluka" name="taluka" class="form-control" required>
                    <option value="">Select Taluka</option>
                    <?php foreach ($districtTalukaData as $dist => $talukas): ?>
                    <option value="<?= $dist ?>" <?= ($district == $dist) ? 'selected' : '' ?>><?= $dist ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                <label for="pin_code">Pin Code</label>
        <input 
            type="text" 
            name="pin_code" 
            id="pin_code" 
            class="form-control" 
            placeholder="Pin Code" 
            value="<?= htmlspecialchars($row['pin_code'] ?? '') ?>" 
            required 
            pattern="^\d{6}$" 
            title="Pin code must be a 6-digit number." 
            oninput="validatePinCode()" 
        >
        <small id="pinCodeError" class="text-danger"></small>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" name="state" id="state" class="form-control" placeholder="State" value="<?= htmlspecialchars($row['state'] ?? '') ?>" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="mobile">Mobile Number</label>
                    <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile Number" value="<?= htmlspecialchars($row['mobile'] ?? '') ?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="email">Email Id</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email Id" value="<?= htmlspecialchars($row['email'] ?? '') ?>" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                <label for="aadhaar">Aadhaar Number</label>
        <input 
            type="text" 
            name="aadhaar" 
            id="aadhaar" 
            class="form-control" 
            placeholder="Aadhaar Number" 
            required 
            pattern="^\d{12}$" 
            title="Aadhaar Number must be a 12-digit number." 
            oninput="validateAadhaar()"
            value="<?= htmlspecialchars($row['aadhaar'] ?? '') ?>"
        >
        <small id="aadhaarError" class="text-danger"></small>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" name="dob" id="dob" class="form-control" value="<?= htmlspecialchars($row['dob'] ?? '') ?>" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
            <div class="form-group">
    <label for="religion">Religion</label>
    <select name="religion" id="religion" class="form-control" required>
        <option value="">Select Religion</option>
        <option value="Hindu" <?= $row['religion'] == 'Hindu' ? 'selected' : '' ?>>Hindu</option>
        <option value="Muslim" <?= $row['religion'] == 'Muslim' ? 'selected' : '' ?>>Muslim</option>
        <option value="Christian" <?= $row['religion'] == 'Christian' ? 'selected' : '' ?>>Christian</option>
        <option value="Other" <?= $row['religion'] == 'Other' ? 'selected' : '' ?>>Other</option>
    </select>
</div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="caste_category">Caste Category</label>
                    <input type="text" name="caste_category" id="caste_category" class="form-control" placeholder="Caste Category" value="<?= htmlspecialchars($row['caste_category'] ?? '') ?>" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="caste">Caste</label>
                    <input type="text" name="caste" id="caste" class="form-control" placeholder="Caste" value="<?= htmlspecialchars($row['caste'] ?? '') ?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 col-12 mb-3">
            <div class="form-group">
        <label for="marksheet">Marksheet (Max: 1MB)</label>
        <input type="file" name="marksheet" id="marksheet" class="file-input" accept=".jpeg,.jpg,.png">
        
        <?php if (!empty($row['marksheet'])): ?>
            <p>Current file: <a href="<?= htmlspecialchars($row['marksheet']) ?>" target="_blank">View File</a></p>
        <?php endif; ?>
    </div>
            </div>
            <div class="col-md-4 col-12 mb-3">
            <div class="form-group">
    <label for="photo">Photo (Max: 1MB)</label>
    <input type="file" name="photo" id="photo" class="file-input" accept=".jpeg,.jpg,.png">
    
    <?php if (!empty($row['photo'])): ?>
        <p>Current photo: <a href="<?= htmlspecialchars($row['photo']) ?>" target="_blank">View Photo</a></p>
    <?php endif; ?>
</div>

            </div>
            <div class="col-md-4 col-12 mb-3">
            <div class="form-group">
    <label for="signature">Signature (Max: 1MB)</label>
    <input type="file" name="signature" id="signature" class="file-input" accept=".jpeg,.jpg,.png">
    
    <?php if (!empty($row['signature'])): ?>
        <p>Current signature: <a href="<?= htmlspecialchars($row['signature']) ?>" target="_blank">View Signature</a></p>
    <?php endif; ?>
</div>

            </div>
        </div>

        <div class="form-group mb-3">
    <label>Physically Handicapped</label><br>
    <input type="radio" name="physically_handicapped" value="Yes" <?php echo ($row['physically_handicapped'] == 'Yes') ? 'checked' : ''; ?> required> Yes
    <input type="radio" name="physically_handicapped" value="No" <?php echo ($row['physically_handicapped'] == 'No') ? 'checked' : ''; ?> required> No
</div>


        <div class="form-group">
            <button type="submit">Update</button>
        </div>

        <div class="form-group">
        <a href="report.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
<?php endif; ?>
 <!-- Success/Error Message (Example) -->
 <?php if (isset($msg)) { ?>
        <div class="alert alert-success mt-3"><?php echo $msg; ?></div>
    <?php } ?>
</div>
    <!-- Bootstrap JS & Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    
</body>
</html>

<script>
document.getElementById("gender").addEventListener("change", function () {
    const title = document.getElementById("title");
    if (this.value === "Male") {
        title.value = "Mr.";
    } else if (this.value === "Female") {
        title.value = "Mrs.";
    } else {
        title.value = "Mrx.";
    }
});

document.getElementById("title").addEventListener("change", function () {
    const title = document.getElementById("gender");
    if (this.value === "Mr.") {
        title.value = "Male";
    } else if (this.value === "Mrs.") {
        title.value = "Female";
    } else {
        title.value = "Other";
    }
}); 

function validatePinCode() {
        const pinCodeInput = document.getElementById('pin_code');
        const pinCodeError = document.getElementById('pinCodeError');
        const pinCodeValue = pinCodeInput.value;
        
        // Check if the value is exactly 6 digits
        if (!/^\d{6}$/.test(pinCodeValue)) {
            pinCodeError.textContent = "Pin code must be exactly 6 digits.";
            pinCodeInput.setCustomValidity("Invalid Pin Code");
        } else {
            pinCodeError.textContent = "";
            pinCodeInput.setCustomValidity("");
        }
    }

    function validateAadhaar() {
        const aadhaarInput = document.getElementById('aadhaar');
        const aadhaarError = document.getElementById('aadhaarError');
        const aadhaarValue = aadhaarInput.value;
        
        // Check if the value is exactly 12 digits
        if (!/^\d{12}$/.test(aadhaarValue)) {
            aadhaarError.textContent = "Aadhaar Number must be exactly 12 digits.";
            aadhaarInput.setCustomValidity("Invalid Aadhaar Number");
        } else {
            aadhaarError.textContent = "";
            aadhaarInput.setCustomValidity("");
        }
    }

    // District and Taluka data
const districtTalukaData = <?= json_encode([
       "Ahmednagar" => ["Akole", "Jamkhed", "Karjat", "Kopargaon", "Nagar", "Nevasa", "Parner", "Pathardi", "Rahata", "Rahuri", "Sangamner", "Shevgaon", "Shrigonda", "Shrirampur"],
"Akola" => ["Akola", "Akot", "Balapur", "Murtizapur", "Telhara"],
"Amravati" => ["Achalpur", "Amravati", "Anjangaon Surji", "Chandur Railway", "Chandurbazar", "Daryapur", "Dhamangaon Railway", "Morshi", "Nandgaon-Khandeshwar", "Teosa", "Warud"],
"Aurangabad" => ["Aurangabad", "Kannad", "Khuldabad", "Paithan", "Phulambri", "Sillod", "Soegaon", "Vaijapur", "Gangapur"],
"Beed" => ["Ambejogai", "Ashti", "Beed", "Georai", "Kaij", "Majalgaon", "Parli", "Patoda", "Shirur", "Wadwani"],
"Bhandara" => ["Bhandara", "Lakhandur", "Mohadi", "Pauni", "Sakoli", "Tumsar"],
"Buldhana" => ["Buldhana", "Chikhli", "Deulgaon Raja", "Jalgaon Jamod", "Khamgaon", "Lonar", "Mehkar", "Malkapur", "Motala", "Nandura", "Shegaon", "Sindkhed Raja"],
"Chandrapur" => ["Ballarpur", "Bhadravati", "Brahmapuri", "Chandrapur", "Gondpipri", "Jiwati", "Korpana", "Mul", "Nagbhid", "Pombhurna", "Rajura", "Sawali", "Sindewahi", "Warora"],
"Dhule" => ["Dhule", "Sakri", "Shindkheda", "Shirpur"],
"Gadchiroli" => ["Aheri", "Armori", "Bhamragad", "Chamorshi", "Dhanora", "Desaiganj", "Etapalli", "Gadchiroli", "Kurkheda", "Mulchera", "Sironcha"],
"Gondia" => ["Amgaon", "Arjuni Morgaon", "Deori", "Gondia", "Goregaon", "Sadak Arjuni", "Salekasa", "Tirora"],
"Hingoli" => ["Aundha", "Basmath", "Hingoli", "Kalamnuri", "Sengaon"],
"Jalgaon" => ["Amalner", "Bhadgaon", "Bhusawal", "Bodwad", "Chalisgaon", "Chopda", "Dharangaon", "Erandol", "Jalgaon", "Jamner", "Muktainagar", "Pachora", "Parola", "Raver", "Yawal"],
"Jalna" => ["Ambad", "Badnapur", "Bhokardan", "Ghansawangi", "Jafferabad", "Jalna", "Mantha", "Partur"],
"Kolhapur" => ["Ajara", "Bavda", "Chandgad", "Gadhinglaj", "Hatkanangale", "Kagal", "Karveer", "Panhala", "Radhanagari", "Shahuwadi"],
"Latur" => ["Ahmadpur", "Ausa", "Chakur", "Deoni", "Jalkot", "Latur", "Nilanga", "Renapur", "Shirur Anantpal", "Udgir"],
"Mumbai City" => ["Colaba", "Byculla", "Dadar", "Kurla", "Andheri"],
"Mumbai Suburban" => ["Bandra", "Borivali", "Dahisar", "Goregaon", "Jogeshwari", "Kandivali", "Malad", "Mulund"],
"Nagpur" => ["Hingna", "Kalameshwar", "Kamthi", "Kuhi", "Nagpur (Urban)", "Nagpur (Rural)", "Narkhed", "Parseoni", "Ramtek", "Savner", "Umred"],
"Nanded" => ["Ardhapur", "Bhokar", "Biloli", "Deglur", "Dharmabad", "Hadgaon", "Himayatnagar", "Kandhar", "Kinwat", "Loha", "Mahoor", "Mudkhed", "Mukhed", "Nanded", "Naigaon"],
"Nandurbar" => ["Akkalkuwa", "Akrani", "Nandurbar", "Navapur", "Shahada", "Taloda"],
"Nashik" => ["Baglan", "Chandvad", "Deola", "Dindori", "Igatpuri", "Kalwan", "Malegaon", "Manmad", "Nandgaon", "Nashik", "Peint", "Sinnar", "Surgana", "Trimbakeshwar", "Yeola"],
"Osmanabad" => ["Bhum", "Kalamb", "Lohara", "Osmanabad", "Paranda", "Tuljapur", "Umarga", "Washi"],
"Palghar" => ["Dahanu", "Jawhar", "Mokhada", "Palghar", "Talasari", "Vasai", "Vikramgad", "Wada"],
"Parbhani" => ["Gangakhed", "Jintur", "Manwat", "Palam", "Parbhani", "Pathri", "Purna", "Sailu", "Sonpeth"],
"Pune" => ["Ambegaon", "Baramati", "Bhor", "Daund", "Haveli", "Indapur", "Junnar", "Khed", "Mawal", "Mulshi", "Pune City", "Shirur", "Velhe"],
"Raigad" => ["Alibag", "Karjat", "Khalapur", "Mahad", "Mangaon", "Murud", "Panvel", "Pen", "Poladpur", "Roha", "Shrivardhan", "Sudhagad"],
"Ratnagiri" => ["Chiplun", "Dapoli", "Guhagar", "Khed", "Lanja", "Mandangad", "Rajapur", "Ratnagiri", "Sangameshwar"],
"Sangli" => ["Atpadi", "Jat", "Kadegaon", "Kavathe Mahankal", "Miraj", "Palus", "Shirala", "Tasgaon", "Walwa"],
"Satara" => ["Jaoli", "Khandala", "Khatav", "Koregaon", "Mahabaleshwar", "Man", "Patan", "Phaltan", "Satara", "Wai"],
"Sindhudurg" => ["Devgad", "Kankavli", "Kudal", "Malvan", "Sawantwadi", "Vengurla"],
"Solapur" => ["Akkalkot", "Barshi", "Karmala", "Madha", "Malshiras", "Mangalvedhe", "Mohol", "Pandharpur", "Sangola", "Solapur North", "Solapur South"],
"Thane" => ["Ambarnath", "Bhiwandi", "Kalyan", "Murbad", "Shahapur", "Thane", "Ulhasnagar"],
"Wardha" => ["Arvi", "Ashti", "Deoli", "Hinganghat", "Karanja", "Samudrapur", "Seloo", "Wardha"],
"Washim" => ["Karanja", "Malegaon", "Mangrulpir", "Manora", "Risod", "Washim"],
"Yavatmal" => ["Arni", "Babulgaon", "Darwha", "Digras", "Ghatanji", "Kalamb", "Mahagaon", "Maregaon", "Ner", "Pandharkawada", "Pusad", "Ralegaon", "Umarkhed", "Wani", "Yavatmal"]

    ]); ?>;

    const districtSelect = document.getElementById("district");
    const talukaSelect = document.getElementById("taluka");

    // Pre-select existing values
    const selectedDistrict = <?= json_encode($row['district']) ?>;
    const selectedTaluka = <?= json_encode($row['taluka']) ?>;

    function populateDistricts() {
        Object.keys(districtTalukaData).forEach(district => {
            const option = document.createElement("option");
            option.value = district;
            option.textContent = district;
            if (district === selectedDistrict) {
                option.selected = true; // Pre-select the district
            }
            districtSelect.appendChild(option);
        });

        // Trigger population of talukas for the selected district
        if (selectedDistrict) {
            populateTalukas(selectedDistrict);
        }
    }

    function populateTalukas(district) {
        // Reset taluka dropdown
        talukaSelect.innerHTML = '<option value="">Select Taluka</option>';

        if (district && districtTalukaData[district]) {
            talukaSelect.disabled = false; // Enable the taluka dropdown
            districtTalukaData[district].forEach(taluka => {
                const option = document.createElement("option");
                option.value = taluka;
                option.textContent = taluka;
                if (taluka === selectedTaluka) {
                    option.selected = true; // Pre-select the taluka
                }
                talukaSelect.appendChild(option);
            });
        } else {
            talukaSelect.disabled = true; // Disable if no talukas available
        }
    }

    // Populate districts on page load
    populateDistricts();

    // Update talukas when district changes
    districtSelect.addEventListener("change", function () {
        populateTalukas(this.value);
    });
</script>