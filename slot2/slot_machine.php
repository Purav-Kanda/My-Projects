<?php
session_start();
header('Content-Type: application/json');

// Initialize credits if new session
if (!isset($_SESSION['credits'])) {
    $_SESSION['credits'] = 10;
}

// Handle spin request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bet = isset($_POST['bet']) ? (int)$_POST['bet'] : 0;
    
    // Validate bet
    if ($bet < 1) {
        echo json_encode(['error' => 'Minimum bet is 1 credit']);
        exit;
    }
    
    if ($bet > $_SESSION['credits']) {
        echo json_encode(['error' => 'You don\'t have enough credits']);
        exit;
    }
    
    // Deduct bet from credits
    $_SESSION['credits'] -= $bet;
    
    // Generate random fruits
    $fruits = ["1.png", "2.png", "3.png", "4.png", "5.png", "6.png", "7.png"];
    $slots = [
        $fruits[array_rand($fruits)],
        $fruits[array_rand($fruits)],
        $fruits[array_rand($fruits)]
    ];
    
    // Calculate winnings
    $winnings = 0;
    $message = "Try again!";
    
    if ($slots[0] === $slots[1] && $slots[1] === $slots[2]) {
        $winnings = $bet * 10; // 10x for jackpot
        $message = "JACKPOT! You won big!";
    } elseif ($slots[0] === $slots[1] || $slots[0] === $slots[2] || $slots[1] === $slots[2]) {
        $winnings = $bet * 3; // 3x for two matches
        $message = "You won a small prize!";
    }
    
    // Add winnings to credits
    $_SESSION['credits'] += $winnings;
    
    // Check if game over
    $gameOver = ($_SESSION['credits'] < 1);
    if ($gameOver) {
        session_unset();
        session_destroy();
    }
    
    // Return response
    echo json_encode([
        'slots' => $slots,
        'message' => $message,
        'winnings' => $winnings,
        'credits' => $_SESSION['credits'],
        'gameOver' => $gameOver
    ]);
    exit;
}

// Handle credit check request
if (isset($_GET['checkCredits'])) {
    echo json_encode(['credits' => $_SESSION['credits']]);
    exit;
}

// Default response if no valid request
echo json_encode(['error' => 'Invalid request']);
?>