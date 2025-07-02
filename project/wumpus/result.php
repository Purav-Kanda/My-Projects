<?php
// Database connection details
$host = 'sql312.infinityfree.com';
$dbname = 'if0_39187569_wumpus_game';
$username = 'if0_39187569';
$password = 'Pmpk132006';      

try {
    // Establish connection to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", "$username", "$password");
} catch (Exception $e) {
    // If connection fails, display error message
    die("ERROR: Couldn't connect. {$e->getMessage()}");
}
?>
<?php
// Retrieve row and column values from the GET request
$row = filter_input(INPUT_GET, 'row', FILTER_VALIDATE_INT);
$column = filter_input(INPUT_GET, 'column', FILTER_VALIDATE_INT);

// Validate the row and column input values
if ($row === false || $column === false || $row < 1 || $row > 7 || $column < 1 || $column > 7) {
    die("Invalid row or column parameters.");
}

// Query the database to check if the Wumpus is at the specified location
$stmt = $pdo->prepare("SELECT * FROM wumpuses WHERE 'row' = ? AND col = ?");
$stmt->execute([$row, $column]);
$wumpus = $stmt->fetch();

// Determine if the Wumpus was found
$found = $wumpus !== false;
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $found ? 'You found the Wumpus!' : 'You didn\'t find the Wumpus' ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/wumpus.css">
</head>
<body>
    <div id="container">
        <div class="result-container">
            <h1><?= $found ? 'You found the Wumpus!' : 'You didn\'t find the Wumpus' ?></h1>
            
            <?php if ($found): ?>
                <img src="images/wumpus.png" alt="Wumpus" class="wumpus-image">
                <p>Congratulations! You've captured the Wumpus!</p>
            <?php else: ?>
                <p>Oops, you didn't capture the Wumpus.</p>
            <?php endif; ?>
            
            <!-- Form for submitting player details -->
            <form id="playerForm" action="save.php" method="post">
                <input type="hidden" name="result" value="<?= $found ? 'win' : 'lose' ?>">
                <input type="hidden" name="row" value="<?= $row ?>">
                <input type="hidden" name="column" value="<?= $column ?>">
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required 
                           pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                           title="Please enter a valid email address (e.g., user@example.com)">
                    <span id="emailError" class="error-message"></span>
                </div>
                
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required minlength="2" maxlength="100">
                </div>
                
                <button type="submit">Save My Result</button>
            </form>
        </div>
    </div>
    
    <script>
        // Client-side validation for email input
        document.getElementById('playerForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const emailError = document.getElementById('emailError');
            
            if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
                emailError.textContent = 'Please enter a valid email (e.g., user@example.com)';
                e.preventDefault(); // Prevent form submission if email is invalid
            } else {
                emailError.textContent = '';
            }
        });
    </script>
</body>
</html>
