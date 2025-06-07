<?php
$targetDir = "images/destination/";
$fields = ['pi_main', 'pi1', 'pi2', 'pi3', 'pi4', 'pi5', 'pi6'];
$imagePaths = [];

foreach ($fields as $field) {
    if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
        $targetFile = $targetDir . basename($_FILES[$field]["name"]);
        move_uploaded_file($_FILES[$field]["tmp_name"], $targetFile);
        $imagePaths[$field] = $targetFile;
    } else {
        $imagePaths[$field] = null;
    }
}

// Get other form data
$pname = $_POST['pname'];
$pdescription = $_POST['pdescription'];
$package_amt = $_POST['package_amt'];

// Database connection
$conn = new mysqli("localhost", "username", "password", "database");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Insert query
$sql = "INSERT INTO information (pname, pdescription, pi_main, pi1, pi2, pi3, pi4, pi5, pi6, package_amt)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssssssssd",
    $pname,
    $pdescription,
    $imagePaths['pi_main'],
    $imagePaths['pi1'],
    $imagePaths['pi2'],
    $imagePaths['pi3'],
    $imagePaths['pi4'],
    $imagePaths['pi5'],
    $imagePaths['pi6'],
    $package_amt
);
$stmt->execute();
$stmt->close();
$conn->close();

echo "Data inserted successfully!";
?>