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

// Create uploads directory if it doesn't exist
$upload_dir = "uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_form'])) {
    // Sanitize inputs
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

    // Validate required fields
    if (empty($title) || empty($fname) || empty($lname) || empty($gender) || empty($address) ||
        empty($pin_code) || empty($mobile) || empty($email) || empty($aadhaar) || empty($dob)) {
        echo "<script>alert('Please fill all required fields!');</script>";
        exit;
    }

    // Validate mobile number
    if (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
        echo "<script>alert('Invalid mobile number. Please enter a valid 10-digit number starting with 6, 7, 8, or 9.');</script>";
        exit;
    }

    // Validate files
    $files_to_upload = ['marksheet', 'photo', 'signature'];
    $file_paths = [];

    foreach ($files_to_upload as $file) {
        if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
            echo "<script>alert('Error uploading $file.');</script>";
            exit;
        }

        $file_name = uniqid() . "_" . basename($_FILES[$file]['name']);
        $file_path = $upload_dir . $file_name;
        $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        if ($_FILES[$file]['size'] > 1048576 || !in_array($file_extension, ['jpeg', 'jpg', 'png'])) {
            echo "<script>alert('Invalid file for $file!');</script>";
            exit;
        }

        if (!move_uploaded_file($_FILES[$file]['tmp_name'], $file_path)) {
            echo "<script>alert('Failed to upload $file.');</script>";
            exit;
        }

        $file_paths[$file] = $file_path;
    }

    $marksheet_path = $file_paths['marksheet'];
    $photo_path = $file_paths['photo'];
    $signature_path = $file_paths['signature'];

    // Insert data into database
    $sql = "INSERT INTO admission_form (
        admission_id, title, first_name, middle_name, last_name, full_name, mother_name, gender, 
        address, taluka, district, pin_code, state, mobile, email, aadhaar, dob, age, 
        religion, caste_category, caste, physically_handicapped, marksheet, photo, signature
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $admission_id = uniqid('ADM');
        $stmt->bind_param(
            "sssssssssssssssssssssssss",
            $admission_id, $title, $fname, $mname, $lname, $fullname, $mother_name, $gender, $address,
            $taluka, $district, $pin_code, $state, $mobile, $email, $aadhaar, $dob, $age,
            $religion, $caste_category, $caste, $physically_handicapped, $marksheet_path, $photo_path, $signature_path
        );

        if ($stmt->execute()) {
            echo "<script>alert('Form submitted successfully!'); window.location.href = 'report.php';</script>";
        } else {
            echo "<script>alert('Error submitting form: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
    }
}

$conn->close();
?>
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
        <div class="row">
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="title">Title</label>
                    <select name="title" id="title" class="form-control" required>
                        <option value="">Select Title</option>
                        <option value="Mr.">Mr.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Mrx.">Mrx.</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="fname">First Name</label>
                    <input type="text" name="fname" id="fname" class="form-control" placeholder="First Name" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="mname">Middle Name</label>
                    <input type="text" name="mname" id="mname" class="form-control" placeholder="Middle Name" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="lname">Last Name</label>
                    <input type="text" name="lname" id="lname" class="form-control" placeholder="Last Name" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="mother_name">Mother's Name</label>
                    <input type="text" name="mother_name" id="mother_name" class="form-control" placeholder="Mother's Name" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select name="gender" id="gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" class="form-control" placeholder="Address" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="district">District</label>
                    <select id="district" name="district" class="form-control" required>
                    <option value="">Select District</option>
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
                    <input type="text" name="state" id="state" class="form-control" placeholder="State" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="mobile">Mobile Number</label>
                    <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile Number" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="email">Email Id</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email Id" required>
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
        >
        <small id="aadhaarError" class="text-danger"></small>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" name="dob" id="dob" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="religion">Religion</label>
                    <select name="religion" id="religion" class="form-control" required>
                        <option value="">Select Religion</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Muslim">Muslim</option>
                        <option value="Christian">Christian</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="caste_category">Caste Category</label>
                    <input type="text" name="caste_category" id="caste_category" class="form-control" placeholder="Caste Category" required>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="caste">Caste</label>
                    <input type="text" name="caste" id="caste" class="form-control" placeholder="Caste" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 col-12 mb-3">
                <div class="form-group">
                    <label for="marksheet">Marksheet (Max: 1MB)</label>
                    <input type="file" name="marksheet" id="marksheet" class="file-input" accept=".jpeg,.jpg,.png" required>
                </div>
            </div>
            <div class="col-md-4 col-12 mb-3">
                <div class="form-group">
                    <label for="photo">Photo (Max: 1MB)</label>
                    <input type="file" name="photo" id="photo" class="file-input" accept=".jpeg,.jpg,.png" required>
                </div>
            </div>
            <div class="col-md-4 col-12 mb-3">
                <div class="form-group">
                    <label for="signature">Signature (Max: 1MB)</label>
                    <input type="file" name="signature" id="signature" class="file-input" accept=".jpeg,.jpg,.png" required>
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label>Physically Handicapped</label><br>
            <input type="radio" name="physically_handicapped" value="Yes" required> Yes
            <input type="radio" name="physically_handicapped" value="No" required> No
        </div>

        <div class="form-group">
            <button type="submit" name="submit_form">Submit</button>
        </div>
    </form>

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

    const districtTalukaData = {
    "Ahmednagar": ["Akole", "Jamkhed", "Karjat", "Kopargaon", "Nagar", "Nevasa", "Parner", "Pathardi", "Rahata", "Rahuri", "Sangamner", "Shevgaon", "Shrigonda", "Shrirampur"],
    "Akola": ["Akola", "Akot", "Balapur", "Murtizapur", "Telhara"],
    "Amravati": ["Achalpur", "Amravati", "Anjangaon Surji", "Chandur Railway", "Chandurbazar", "Daryapur", "Dhamangaon Railway", "Morshi", "Nandgaon-Khandeshwar", "Teosa", "Warud"],
    "Aurangabad": ["Aurangabad", "Kannad", "Khuldabad", "Paithan", "Phulambri", "Sillod", "Soegaon", "Vaijapur", "Gangapur"],
    "Beed": ["Ambejogai", "Ashti", "Beed", "Georai", "Kaij", "Majalgaon", "Parli", "Patoda", "Shirur", "Wadwani"],
    "Bhandara": ["Bhandara", "Lakhandur", "Mohadi", "Pauni", "Sakoli", "Tumsar"],
    "Buldhana": ["Buldhana", "Chikhli", "Deulgaon Raja", "Jalgaon Jamod", "Khamgaon", "Lonar", "Mehkar", "Malkapur", "Motala", "Nandura", "Shegaon", "Sindkhed Raja"],
    "Chandrapur": ["Ballarpur", "Bhadravati", "Brahmapuri", "Chandrapur", "Gondpipri", "Jiwati", "Korpana", "Mul", "Nagbhid", "Pombhurna", "Rajura", "Sawali", "Sindewahi", "Warora"],
    "Dhule": ["Dhule", "Sakri", "Shindkheda", "Shirpur"],
    "Gadchiroli": ["Aheri", "Armori", "Bhamragad", "Chamorshi", "Dhanora", "Desaiganj", "Etapalli", "Gadchiroli", "Kurkheda", "Mulchera", "Sironcha"],
    "Gondia": ["Amgaon", "Arjuni Morgaon", "Deori", "Gondia", "Goregaon", "Sadak Arjuni", "Salekasa", "Tirora"],
    "Hingoli": ["Aundha", "Basmath", "Hingoli", "Kalamnuri", "Sengaon"],
    "Jalgaon": ["Amalner", "Bhadgaon", "Bhusawal", "Bodwad", "Chalisgaon", "Chopda", "Dharangaon", "Erandol", "Jalgaon", "Jamner", "Muktainagar", "Pachora", "Parola", "Raver", "Yawal"],
    "Jalna": ["Ambad", "Badnapur", "Bhokardan", "Ghansawangi", "Jafferabad", "Jalna", "Mantha", "Partur"],
    "Kolhapur": ["Ajara", "Bavda", "Chandgad", "Gadhinglaj", "Hatkanangale", "Kagal", "Karveer", "Panhala", "Radhanagari", "Shahuwadi"],
    "Latur": ["Ahmadpur", "Ausa", "Chakur", "Deoni", "Jalkot", "Latur", "Nilanga", "Renapur", "Shirur Anantpal", "Udgir"],
    "Mumbai City": ["Colaba", "Byculla", "Dadar", "Kurla", "Andheri"],
    "Mumbai Suburban": ["Bandra", "Borivali", "Dahisar", "Goregaon", "Jogeshwari", "Kandivali", "Malad", "Mulund"],
    "Nagpur": ["Hingna", "Kalameshwar", "Kamthi", "Kuhi", "Nagpur (Urban)", "Nagpur (Rural)", "Narkhed", "Parseoni", "Ramtek", "Savner", "Umred"],
    "Nanded": ["Ardhapur", "Bhokar", "Biloli", "Deglur", "Dharmabad", "Hadgaon", "Himayatnagar", "Kandhar", "Kinwat", "Loha", "Mahoor", "Mudkhed", "Mukhed", "Nanded", "Naigaon"],
    "Nandurbar": ["Akkalkuwa", "Akrani", "Nandurbar", "Navapur", "Shahada", "Taloda"],
    "Nashik": ["Baglan", "Chandvad", "Deola", "Dindori", "Igatpuri", "Kalwan", "Malegaon", "Manmad", "Nandgaon", "Nashik", "Peint", "Sinnar", "Surgana", "Trimbakeshwar", "Yeola"],
    "Osmanabad": ["Bhum", "Kalamb", "Lohara", "Osmanabad", "Paranda", "Tuljapur", "Umarga", "Washi"],
    "Palghar": ["Dahanu", "Jawhar", "Mokhada", "Palghar", "Talasari", "Vasai", "Vikramgad", "Wada"],
    "Parbhani": ["Gangakhed", "Jintur", "Manwat", "Palam", "Parbhani", "Pathri", "Purna", "Sailu", "Sonpeth"],
    "Pune": ["Ambegaon", "Baramati", "Bhor", "Daund", "Haveli", "Indapur", "Junnar", "Khed", "Mawal", "Mulshi", "Pune City", "Shirur", "Velhe"],
    "Raigad": ["Alibag", "Karjat", "Khalapur", "Mahad", "Mangaon", "Murud", "Panvel", "Pen", "Poladpur", "Roha", "Shrivardhan", "Sudhagad"],
    "Ratnagiri": ["Chiplun", "Dapoli", "Guhagar", "Khed", "Lanja", "Mandangad", "Rajapur", "Ratnagiri", "Sangameshwar"],
    "Sangli": ["Atpadi", "Jat", "Kadegaon", "Kavathe Mahankal", "Miraj", "Palus", "Shirala", "Tasgaon", "Walwa"],
    "Satara": ["Jaoli", "Khandala", "Khatav", "Koregaon", "Mahabaleshwar", "Man", "Patan", "Phaltan", "Satara", "Wai"],
    "Sindhudurg": ["Devgad", "Kankavli", "Kudal", "Malvan", "Sawantwadi", "Vengurla"],
    "Solapur": ["Akkalkot", "Barshi", "Karmala", "Madha", "Malshiras", "Mangalvedhe", "Mohol", "Pandharpur", "Sangola", "Solapur North", "Solapur South"],
    "Thane": ["Ambarnath", "Bhiwandi", "Kalyan", "Murbad", "Shahapur", "Thane", "Ulhasnagar"],
    "Wardha": ["Arvi", "Ashti", "Deoli", "Hinganghat", "Karanja", "Samudrapur", "Seloo", "Wardha"],
    "Washim": ["Karanja", "Malegaon", "Mangrulpir", "Manora", "Risod", "Washim"],
    "Yavatmal": ["Arni", "Babulgaon", "Darwha", "Digras", "Ghatanji", "Kalamb", "Mahagaon", "Maregaon", "Ner", "Pandharkawada", "Pusad", "Ralegaon", "Umarkhed", "Wani", "Yavatmal"]
    };

    const districtSelect = document.getElementById("district");
    const talukaSelect = document.getElementById("taluka");

    // Populate the district dropdown
    function populateDistricts() {
      Object.keys(districtTalukaData).forEach(district => {
        const option = document.createElement("option");
        option.value = district;
        option.textContent = district;
        districtSelect.appendChild(option);
      });
    }

    // Add event listener for district change
    districtSelect.addEventListener("change", function () {
      const district = this.value;

      // Reset and disable taluka dropdown initially
      talukaSelect.innerHTML = '<option value="">Select Taluka</option>'; 
      talukaSelect.disabled = true;

      // Check if a district is selected and if talukas are available for the selected district
      if (district && districtTalukaData[district]) {
        talukaSelect.disabled = false; // Enable the taluka dropdown
        const talukas = districtTalukaData[district];

        // Populate taluka dropdown with the corresponding talukas
        talukas.forEach(taluka => {
          const option = document.createElement("option");
          option.value = taluka;
          option.textContent = taluka;
          talukaSelect.appendChild(option);
        });
      }
    });

    // Populate the district dropdown when the page loads
    populateDistricts();
</script>

