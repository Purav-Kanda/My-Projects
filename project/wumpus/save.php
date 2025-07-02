<?php
// Database connection details
$host = 'sql312.infinityfree.com';
$dbname = 'if0_39187569_wumpus_game';
$username = 'if0_39187569';
$password = 'Pmpk132006';         

try {
    // Connect to the database using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", "$username", "$password");
} catch (Exception $e) {
    // If connection fails, display an error message
    die("ERROR: Couldn't connect. {$e->getMessage()}");
}
?>
<?php

// Get and sanitize user input from POST request
$result = filter_input(INPUT_POST, 'result', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$row = filter_input(INPUT_POST, 'row', FILTER_VALIDATE_INT);
$column = filter_input(INPUT_POST, 'column', FILTER_VALIDATE_INT);

// Check if all required inputs are valid
if (!$result || !$email || !$name || $row === false || $column === false) {
    die("Invalid input parameters.");
}

// Check if player already exists in the database
$stmt = $pdo->prepare("SELECT * FROM players WHERE email = ?");
$stmt->execute([$email]);
$player = $stmt->fetch();

// Get current date
$currentDate = date('Y-m-d');

if ($player) {
    // If player exists, update their stats
    $wins = $player['wins'] + ($result === 'win' ? 1 : 0);
    $losses = $player['losses'] + ($result === 'lose' ? 1 : 0);
    
    $update = $pdo->prepare("UPDATE players SET name = ?, wins = ?, losses = ?, last_played = ? WHERE email = ?");
    $update->execute([$name, $wins, $losses, $currentDate, $email]);
} else {
    // If player does not exist, insert new record
    $wins = $result === 'win' ? 1 : 0;
    $losses = $result === 'lose' ? 1 : 0;
    
    $insert = $pdo->prepare("INSERT INTO players (email, name, wins, losses, last_played) VALUES (?, ?, ?, ?, ?)");
    $insert->execute([$email, $name, $wins, $losses, $currentDate]);
}

// Fetch the top 10 players sorted by wins and last played date
$topPlayers = $pdo->query("SELECT name, wins, losses, last_played FROM players ORDER BY wins DESC, last_played DESC LIMIT 10")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Game Saved</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/wumpus.css">
</head>
<body>
    <div id="container">
        <div class="result-container">
            <h1>Game Result Saved</h1>
            <p>Thank you, <?= htmlspecialchars($name) ?>! Your <?= $result === 'win' ? 'win' : 'loss' ?> has been recorded.</p>
            
            <h2>Your Stats</h2>
            <p>Wins: <?= $wins ?></p>
            <p>Losses: <?= $losses ?></p>
            
            <h2>Top 10 Players</h2>
            <table class="leaderboard">
                <tr>
                    <th>Name</th>
                    <th>Wins</th>
                    <th>Losses</th>
                    <th>Last Played</th>
                </tr>
                <?php foreach ($topPlayers as $player): ?>
                <tr>
                    <td><?= htmlspecialchars($player['name']) ?></td>
                    <td><?= $player['wins'] ?></td>
                    <td><?= $player['losses'] ?></td>
                    <td><?= $player['last_played'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <a href="index.php" class="play-again">Play Again</a>
        </div>
    </div>
</body>
</html>
