<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tip Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #ffffff;
            color: black;
        }
        input, button {
            display: block;
            margin: 10px auto;
            padding: 10px;
            width: 250px;
        }
    </style>
</head>
<body>
    <h1>Tip Calculator</h1>

    <?php 
    // Only process the form after submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize input
        $server = filter_input(INPUT_POST, "server", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $email1 = filter_input(INPUT_POST, "email_con", FILTER_SANITIZE_EMAIL);
        $credit = filter_input(INPUT_POST, "credit", FILTER_SANITIZE_STRING); // Treat as string
        $bill = filter_input(INPUT_POST, "bill", FILTER_VALIDATE_FLOAT) ?? 0.0;
        $tip = filter_input(INPUT_POST, "percentage", FILTER_VALIDATE_FLOAT) ?? 0.0;

        // Check email mismatch
        if ($email !== $email1) {
            echo "<p style='color: red; font-weight: bold;'>Error: Emails do not match!</p>";
        } 
        // Check credit card length (should be 16 digits)
        else if (strlen($credit) !== 16 || !ctype_digit($credit)) {
            echo "<p style='color: red; font-weight: bold;'>Error: Invalid credit card number (must be 16 digits)!</p>";
        } 
        else {
            // Calculate final amount
            $final = $bill + ($bill * ($tip / 100));

            // Display results
            echo "<p>Server name: $server</p>";
            echo "<p>Total bill amount: $$bill</p>";
            echo "<p>Tip: $tip%</p>";
            echo "<p>Final amount (bill + tip): $$final</p>";
        }
    }
    ?>

    <form id="tipForm" action="tip_calculator1.php" method="post">
        <input type="text" name="server" placeholder="Enter server name" required>
        
        <!-- Email Fields -->
        <input type="email" name="email" placeholder="Enter email address" required>
        <input type="email" name="email_con" placeholder="Confirm email address" required>

        <!-- Bill and Tip Inputs -->
        <input type="number" name="bill" step="0.01" placeholder="Enter bill amount" required min="0">
        <input type="number" name="percentage" step="0.01" placeholder="Enter tip percentage" required min="0">

        <!-- Secure Credit Card Field -->
        <input type="password" name="credit" placeholder="Enter credit card number" required 
               pattern="\d{16}" title="Enter a 16-digit credit card number" minlength="16" maxlength="16">

        <button type="submit">Submit</button>
    </form>
    <script>
        document.getElementById("tipForm").addEventListener("submit", function(event) {
            var email = document.getElementById("email").value;
            var emailConfirm = document.getElementById("email_con").value;
            
            // Check if email addresses match
            if (email !== emailConfirm) {
                event.preventDefault(); // Prevent form submission
                alert("Error: Emails do not match!"); // Display error message
            }
        });
    </script>
</body>

</html>
