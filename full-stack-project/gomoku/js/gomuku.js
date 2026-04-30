window.addEventListener('load', function () {
  // Get DOM elements
  const intro = document.getElementById('intro');
  const gameContainer = document.getElementById('gameContainer');
  const playerForm = document.getElementById('playerForm');
  const gameBoardDiv = document.getElementById('gameBoard');
  const messageDiv = document.getElementById('message');
  const restartBtn = document.getElementById('restartBtn');
  const helpBtn = document.getElementById('helpBtn');
  const exitBtn = document.getElementById('exitBtn');
  const player1ScoreSpan = document.getElementById('player1Score');
  const player2ScoreSpan = document.getElementById('player2Score');

  // Add sound effect objects; ensure corresponding files exist in the audio folder
  const stoneSound = new Audio('audio/stone.mp3');
  const winSound = new Audio('audio/win.mp3');

  // Game state variables
  let board = [];
  const boardSize = 15;         // Number of intersections on the board: 15 x 15
  const boardPixelSize = 450;   // Board container size (px)
  const cellSize = boardPixelSize / boardSize; // Spacing per cell
  
  let currentPlayer = 1;
  let gameActive = true;
  
  // Add match counting variables
  let currentHand = 1;          // Current hand number in the round (1-3 hands)
  let currentRound = 1;         // Current match round (round), using best-of-3 hands rule
  let player1RoundWins = 0;     // Number of hands won by Player 1 in the current round
  let player2RoundWins = 0;     // Number of hands won by Player 2 in the current round
  let player1MatchWins = 0;     // Number of rounds won by Player 1 in the match
  let player2MatchWins = 0;     // Number of rounds won by Player 2 in the match

  let player1 = {};
  let player2 = {};

  // Update scoreboard display
  function updateScoreboard() {
    player1ScoreSpan.textContent = `${player1.name}: ${player1MatchWins}`;
    player2ScoreSpan.textContent = `${player2.name}: ${player2MatchWins}`;
  }

  // Initialize board data model (clear board at the start of each hand)
  function initBoard() {
    board = [];
    for (let i = 0; i < boardSize; i++) {
      board[i] = [];
      for (let j = 0; j < boardSize; j++) {
        board[i][j] = 0; // 0 represents empty, 1 represents Player 1, 2 represents Player 2
      }
    }
  }

  // Draw board lines
  function drawBoardLines() {
    // Clear board container
    gameBoardDiv.innerHTML = '';
    // Draw horizontal and vertical lines
    for (let i = 0; i < boardSize; i++) {
      // Horizontal line: 100% width, 1px height, positioned based on each intersection's y-coordinate
      const hLine = document.createElement('div');
      hLine.classList.add('board-line');
      hLine.style.width = '100%';
      hLine.style.height = '1px';
      hLine.style.top = `${i * cellSize + cellSize / 2}px`;
      hLine.style.left = '0';
      gameBoardDiv.appendChild(hLine);

      // Vertical line: 100% height, 1px width, positioned based on each intersection's x-coordinate
      const vLine = document.createElement('div');
      vLine.classList.add('board-line');
      vLine.style.width = '1px';
      vLine.style.height = '100%';
      vLine.style.left = `${i * cellSize + cellSize / 2}px`;
      vLine.style.top = '0';
      gameBoardDiv.appendChild(vLine);
    }
  }

  // Render board and the state of placed stones
  function renderBoard() {
    drawBoardLines();
    for (let i = 0; i < boardSize; i++) {
      for (let j = 0; j < boardSize; j++) {
        if (board[i][j] !== 0) {
          const stone = document.createElement('div');
          stone.classList.add('stone');
          // Display stone using player's color
          stone.style.backgroundColor = board[i][j] === 1 ? player1.color : player2.color;
          // Stone diameter 20px, centered on the intersection
          const offset = 10;
          stone.style.left = `${j * cellSize + cellSize / 2 - offset}px`;
          stone.style.top = `${i * cellSize + cellSize / 2 - offset}px`;
          gameBoardDiv.appendChild(stone);
        }
      }
    }
  }

  // Check win condition: five consecutive stones in any direction
  function checkWin(row, col) {
    const directions = [
      { dr: 0, dc: 1 },   // Horizontal
      { dr: 1, dc: 0 },   // Vertical
      { dr: 1, dc: 1 },   // Diagonal (top left to bottom right)
      { dr: 1, dc: -1 }   // Diagonal (top right to bottom left)
    ];
    for (const dir of directions) {
      let count = 1;
      count += countInDirection(row, col, dir.dr, dir.dc);
      count += countInDirection(row, col, -dir.dr, -dir.dc);
      if (count >= 5) return true;
    }
    return false;
  }

  function countInDirection(row, col, dr, dc) {
    let r = row + dr;
    let c = col + dc;
    let count = 0;
    while (r >= 0 && r < boardSize && c >= 0 && c < boardSize && board[r][c] === currentPlayer) {
      count++;
      r += dr;
      c += dc;
    }
    return count;
  }

  // Board click event: calculate the nearest intersection based on click coordinates for stone placement
  gameBoardDiv.addEventListener('click', function (e) {
    if (!gameActive) return;
    const rect = gameBoardDiv.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    const col = Math.round((x - cellSize / 2) / cellSize);
    const row = Math.round((y - cellSize / 2) / cellSize);
    if (row < 0 || row >= boardSize || col < 0 || col >= boardSize) return;
    if (board[row][col] !== 0) return; // This intersection already has a stone

    board[row][col] = currentPlayer;
    // Play stone drop sound effect
    stoneSound.currentTime = 0;
    stoneSound.play();
    renderBoard();

    if (checkWin(row, col)) {
      // Play win sound effect
      winSound.currentTime = 0;
      winSound.play();

      // End this hand
      gameActive = false;
      // Record the winner of this hand
      if (currentPlayer === 1) {
        player1RoundWins++;
      } else {
        player2RoundWins++;
      }
      messageDiv.textContent = `${currentPlayer === 1 ? player1.name : player2.name} wins Hand ${currentHand} of Round ${currentRound}!`;
      
      // Determine if the round is over (maximum three hands or one player wins two hands)
      if (player1RoundWins === 2 || player2RoundWins === 2 || currentHand === 3) {
        let roundWinner;
        if (player1RoundWins > player2RoundWins) {
          roundWinner = 1;
        } else if (player2RoundWins > player1RoundWins) {
          roundWinner = 2;
        } else {
          roundWinner = null; // Tie
        }
        if (roundWinner !== null) {
          // There is a winner: update match round wins
          if (roundWinner === 1) {
            player1MatchWins++;
          } else {
            player2MatchWins++;
          }
          updateScoreboard();
          messageDiv.textContent += ` Round ${currentRound} won by ${roundWinner === 1 ? player1.name : player2.name}.`;
        } else {
          // Tie handling: display tie message, do not increment round count, replay the round
          messageDiv.textContent += ` Round ${currentRound} is tied. Replay the round.`;
        }
        // Check if match is over: first to win 2 rounds wins
        if (player1MatchWins === 2 || player2MatchWins === 2) {
          messageDiv.textContent += ` Match over! ${player1MatchWins === 2 ? player1.name : player2.name} wins the match.`;
          restartBtn.style.display = 'block';
          restartBtn.textContent = "Play Again";
          return;
        } else {
          // Prepare for the next round
          // If the round is tied, do not increment the round number
          if (roundWinner !== null) {
            currentRound++;
          }
          currentHand = 1;
          player1RoundWins = 0;
          player2RoundWins = 0;
          restartBtn.style.display = 'block';
          restartBtn.textContent = roundWinner !== null ? "Next Round" : "Replay Round";
        }
      } else {
        // If the hand hasn't produced a winner, continue to the next hand
        currentHand++;
        restartBtn.style.display = 'block';
        restartBtn.textContent = "Next Hand";
      }
    } else {
      // If this hand hasn't produced a winner, switch player's turn
      currentPlayer = currentPlayer === 1 ? 2 : 1;
      messageDiv.textContent = `Hand ${currentHand} of Round ${currentRound}: ${currentPlayer === 1 ? player1.name : player2.name}'s turn.`;
    }
  });

  // When "Next Hand", "Next Round", "Replay Round" or "Play Again" button is clicked, start a new hand or restart the match
  restartBtn.addEventListener('click', function () {
    // If the match is over, reset all counters
    if (!gameActive && (player1MatchWins === 2 || player2MatchWins === 2)) {
      currentHand = 1;
      currentRound = 1;
      player1RoundWins = 0;
      player2RoundWins = 0;
      player1MatchWins = 0;
      player2MatchWins = 0;
      updateScoreboard();
    }
    initBoard();
    renderBoard();
    gameActive = true;
    // New hand always starts with Player 1 (can be adjusted as needed)
    currentPlayer = 1;
    restartBtn.style.display = 'none';
    messageDiv.textContent = `Hand ${currentHand} of Round ${currentRound}: ${player1.name}'s turn.`;
  });

  // Help button event: display game instructions
  helpBtn.addEventListener('click', function () {
    messageDiv.textContent = "Instructions: Click on the board to place your stone at the nearest intersection. Each round is best of 3 hands (first to win 2 hands wins the round). The match is best-of-3 rounds (first to 2 rounds wins). In case of a tie round, replay the round.";
  });

  // Exit button: exit the game, return to the intro page, and reset game state
  exitBtn.addEventListener('click', function () {
    // Directly switch back to the intro page and reset state
    intro.style.display = 'block';
    gameContainer.style.display = 'none';
    // Reset match state (you can also clear form content here)
    currentHand = 1;
    currentRound = 1;
    player1RoundWins = 0;
    player2RoundWins = 0;
    player1MatchWins = 0;
    player2MatchWins = 0;
    updateScoreboard();
    messageDiv.textContent = '';
  });

  // Handle intro page form submission: retrieve player information and start the match
  playerForm.addEventListener('submit', function (e) {
    e.preventDefault();
    // Get player information (all inputs are required)
    player1 = {
      name: document.getElementById('player1Name').value,
      age: document.getElementById('player1Age').value,
      color: document.getElementById('player1Color').value
    };
    player2 = {
      name: document.getElementById('player2Name').value,
      age: document.getElementById('player2Age').value,
      color: document.getElementById('player2Color').value
    };

    // Hide intro page and display game container
    intro.style.display = 'none';
    gameContainer.style.display = 'block';

    // Initialize all states: match, round, and hand numbers reset to zero
    currentHand = 1;
    currentRound = 1;
    player1RoundWins = 0;
    player2RoundWins = 0;
    player1MatchWins = 0;
    player2MatchWins = 0;
    updateScoreboard();
    initBoard();
    renderBoard();
    gameActive = true;
    currentPlayer = 1;
    messageDiv.textContent = `Hand ${currentHand} of Round ${currentRound}: ${player1.name}'s turn.`;
  });
});
