<?php
// filepath: c:\xampp\htdocs\Anveshana\info.php
$conn = new mysqli("localhost", "root", "", "anveshana");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$pname = isset($_GET['pname']) ? $conn->real_escape_string($_GET['pname']) : '';
$sql = "SELECT * FROM information WHERE pname='$pname'";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    echo "<h1>{$row['pname']}</h1>";
    echo "<p>{$row['pdescription']}</p>";
    echo "<img src='{$row['pi_main']}' alt='{$row['pname']} Main Image'><br>";
    for ($i = 1; $i <= 6; $i++) {
        $img = $row["pi$i"];
        if ($img) echo "<img src='$img' alt='{$row['pname']} Image $i' style='width:150px;'><br>";
    }
    echo "<p>Package Amount: ₹{$row['package']}</p>";
} else {
    echo "No information found.";
}
$conn->close();
?>