<?php
// book.php - Booking page for a destination
// Get destination/package info from GET
$pname = $_GET['pname'] ?? '';
$id = $_GET['id'] ?? '';
$cover_image = $_GET['cover_image'] ?? '';
$highlight = $_GET['highlight'] ?? '';
$description = $_GET['description'] ?? '';
$package_id = $_GET['package_id'] ?? '';

// If package_id is present, fetch package details from DB (do this before form processing)
$package = null;
if ($package_id) {
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'anveshana_admin';
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if (!$conn->connect_error) {
        $stmt = $conn->prepare('SELECT * FROM packages WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $package_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $package = $result->fetch_assoc();
        }
        $stmt->close();
        $conn->close();
    }
}

$form_submitted = ($_SERVER['REQUEST_METHOD'] === 'POST');
$booking_success = false;
$booking_error = '';

if ($form_submitted) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $people = intval($_POST['people'] ?? 1);

    // Server-side mobile validation
    if (!preg_match('/^[6-9][0-9]{9}$/', $mobile)) {
        $booking_error = 'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.';
    } else {
        $destination_id = $id ?: null;
        $destination_name = $pname ?: null;
        $pkg_id = $package_id ?: null;
        $pkg_name = $package ? $package['package_name'] : null;
        $unit_price = $package ? $package['price'] : 0;
        $total_price = $unit_price * $people;
        // Pass all booking details to payment.php via POST
        echo '<form id="redirectToPayment" method="post" action="payment.php" style="display:none;">';
        echo '<input type="hidden" name="name" value="' . htmlspecialchars($name, ENT_QUOTES) . '">';
        echo '<input type="hidden" name="email" value="' . htmlspecialchars($email, ENT_QUOTES) . '">';
        echo '<input type="hidden" name="mobile" value="' . htmlspecialchars($mobile, ENT_QUOTES) . '">';
        echo '<input type="hidden" name="date" value="' . htmlspecialchars($date, ENT_QUOTES) . '">';
        echo '<input type="hidden" name="people" value="' . intval($people) . '">';
        echo '<input type="hidden" name="destination_id" value="' . htmlspecialchars($destination_id, ENT_QUOTES) . '">';
        echo '<input type="hidden" name="destination_name" value="' . htmlspecialchars($destination_name, ENT_QUOTES) . '">';
        echo '<input type="hidden" name="package_id" value="' . htmlspecialchars($pkg_id, ENT_QUOTES) . '">';
        echo '<input type="hidden" name="package_name" value="' . htmlspecialchars($pkg_name, ENT_QUOTES) . '">';
        echo '<input type="hidden" name="total_price" value="' . htmlspecialchars($total_price, ENT_QUOTES) . '">';
        echo '</form>';
        echo '<script>document.getElementById("redirectToPayment").submit();</script>';
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?= htmlspecialchars($pname) ?> | Anveshana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2ecc71;
            --dark: #2c3e50;
            --light: #ecf0f1;
            --accent: #e74c3c;
        }

        body {
            background: #f9f9f9;
            color: var(--dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .booking-container {
            max-width: 600px;
            margin: 60px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.13);
            padding: 2.5rem 2rem;
        }

        .booking-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 2rem;
        }

        .booking-img {
            width: 120px;
            height: 120px;
            border-radius: 16px;
            object-fit: cover;
            background: #f1f1f1;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        }

        .booking-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .booking-highlight {
            color: var(--secondary);
            font-size: 1.1rem;
            margin-bottom: 0.7rem;
        }

        .booking-desc {
            color: #555;
            font-size: 1.05rem;
            margin-bottom: 1.2rem;
        }

        .booking-form label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.3rem;
            display: block;
        }

        .booking-form input,
        .booking-form select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            margin-bottom: 1.2rem;
        }

        .booking-form button {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 0.85rem 2.2rem;
            font-size: 1.08rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .booking-form button:hover {
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            transform: translateY(-2px) scale(1.04);
        }

        .success-msg {
            background: #d1fae5;
            color: #065f46;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .error-msg {
            background: #fee2e2;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <div class="booking-header">
            <?php if ($package_id && $package): ?>
                <img src="images/packages/<?= htmlspecialchars($package['image']) ?>" alt="<?= htmlspecialchars($package['package_name']) ?>" class="booking-img">
                <div>
                    <div class="booking-title"><?= htmlspecialchars($package['package_name']) ?></div>
                    <div class="package-price" style="font-size:1.2rem;color:var(--primary);margin-top:0.5rem;">
                        <b>₹<?= number_format($package['price'],2) ?> per person</b>
                    </div>
                </div>
            <?php else: ?>
                <img src="admin/images/destination/<?= htmlspecialchars($cover_image) ?>" alt="<?= htmlspecialchars($pname) ?>" class="booking-img">
                <div>
                    <div class="booking-title"><?= htmlspecialchars($pname) ?></div>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($form_submitted): ?>
            <?php if ($booking_success): ?>
                <div class="success-msg">Booking successful! Thank you for booking with us.</div>
            <?php elseif ($booking_error): ?>
                <div class="error-msg"><?= htmlspecialchars($booking_error) ?></div>
            <?php endif; ?>
        <?php else: ?>
            <form class="booking-form" method="post" action="#" id="bookingForm">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <label for="mobile">Mobile</label>
                <input type="text" id="mobile" name="mobile" required pattern="[6-9][0-9]{9}" maxlength="10" minlength="10" title="Enter a valid 10-digit mobile number starting with 6, 7, 8, or 9">
                <label for="date">Travel Date</label>
                <input type="date" id="date" name="date" required>
                <label for="people">No. of People</label>
                <input type="number" id="people" name="people" min="1" value="1" required oninput="updateTotalPrice()">
                <div id="price-section" style="margin-bottom:1.2rem; font-weight:600; color:var(--primary);">
                    <?php if ($package_id && $package): ?>
                        Price: ₹<span id="unitPrice"><?= number_format($package['price'],2) ?></span> per person<br>
                        Total Price: ₹<span id="totalPrice"><?= number_format($package['price'],2) ?></span>
                    <?php else: ?>
                        Price: ₹0<br>
                        Total Price: ₹0
                    <?php endif; ?>
                </div>
                <button type="submit">Book Now</button>
            </form>
            <script>
            function updateTotalPrice() {
                var people = document.getElementById('people').value;
                var unitPrice = <?= ($package_id && $package) ? floatval($package['price']) : 0 ?>;
                var total = Math.max(1, parseInt(people) || 1) * unitPrice;
                document.getElementById('totalPrice').innerText = total.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
            }
            document.getElementById('people').addEventListener('input', updateTotalPrice);
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
