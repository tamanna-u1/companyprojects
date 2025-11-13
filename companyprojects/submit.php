<?php
include "db.php";

// helper to sanitize
function clean($v, $conn) {
    return mysqli_real_escape_string($conn, trim($v));
}

$full_name = clean($_POST['full_name'] ?? '', $conn);
$email     = clean($_POST['email'] ?? '', $conn);
$dob       = clean($_POST['dob'] ?? '', $conn);
$gender    = clean($_POST['gender'] ?? '', $conn);
$mobile    = clean($_POST['mobile'] ?? '', $conn);
$bank_name = clean($_POST['bank_name'] ?? '', $conn);
$account_no= clean($_POST['account_no'] ?? '', $conn);
$ifsc      = strtoupper(clean($_POST['ifsc'] ?? '', $conn));
$aadhaar   = clean($_POST['aadhaar'] ?? '', $conn);

// SERVER-SIDE VALIDATIONS

$errors = [];

// full name
if (strlen($full_name) < 3) $errors[] = "Full name too short.";

// email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";

// dob & gender
if (!$dob) $errors[] = "DOB required.";
if (!$gender) $errors[] = "Gender required.";

// mobile - exactly 10 digits
if (!preg_match('/^\d{10}$/', $mobile)) $errors[] = "Mobile must be exactly 10 digits.";

// account number (if provided)
if ($account_no !== '' && !preg_match('/^\d{9,18}$/', $account_no)) $errors[] = "Account number must be 9-18 digits.";

// IFSC (if provided) - 4 letters + 0 + 6 alnum
if ($ifsc !== '' && !preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $ifsc)) $errors[] = "Invalid IFSC format.";

// Aadhaar (if provided)
if ($aadhaar !== '' && !preg_match('/^\d{12}$/', $aadhaar)) $errors[] = "Aadhaar must be 12 digits.";

// Photo upload validation
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = "Photo upload failed.";
} else {
    $fileTmp = $_FILES['photo']['tmp_name'];
    $fileName = basename($_FILES['photo']['name']);
    $fileSize = $_FILES['photo']['size'];
    $fileType = mime_content_type($fileTmp);

    $allowed = ['image/jpeg','image/png','image/jpg'];

    if (!in_array($fileType, $allowed)) $errors[] = "Photo must be JPG/PNG.";
    if ($fileSize > 2 * 1024 * 1024) $errors[] = "Photo must be less than 2 MB.";
}

// if errors, show and stop
if (!empty($errors)) {
    echo "<h3>Errors:</h3><ul>";
    foreach($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
    echo "</ul><a href='form.html'>Go back</a>";
    exit;
}

// move uploaded file safely
$ext = pathinfo($fileName, PATHINFO_EXTENSION);
$targetDir = "uploads/";
if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

// create unique filename
$newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$targetPath = $targetDir . $newName;

if (!move_uploaded_file($fileTmp, $targetPath)) {
    echo "Failed to save uploaded file."; exit;
}

// insert into DB
$stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, dob, gender, mobile, bank_name, account_no, ifsc, aadhaar, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssssssssss", $full_name, $email, $dob, $gender, $mobile, $bank_name, $account_no, $ifsc, $aadhaar, $newName);

if (mysqli_stmt_execute($stmt)) {
    echo "Form submitted successfully.<br><a href='data.php'>Go to Data page</a>";
} else {
    echo "DB error: " . mysqli_error($conn);
}
?>
