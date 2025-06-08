class GuessingGame {
    constructor(targetNumber = Math.floor(Math.random() * 100 + 1), attempts = 0) {
      this.targetNumber = targetNumber;
      this.attempts = attempts;
      this.maxAttempts = 10;
    }
  
    guess(number) {
      this.attempts++;
      this.saveGame();
      if (number === this.targetNumber) {
        return "correct";
      } else if (number > this.targetNumber) {
        return "too high";
      } else {
        return "too low";
      }
    }
  
    isGameOver() {
      return this.attempts >= this.maxAttempts;
    }
  
    saveGame() {
      localStorage.setItem("guessingGame", JSON.stringify(this));
    }
  
    static loadGame() {
      const savedGame = localStorage.getItem("guessingGame");
      if (savedGame) {
        const { targetNumber, attempts } = JSON.parse(savedGame);
        return new GuessingGame(targetNumber, attempts);
      }
      return new GuessingGame();
    }
  }
  
  let game = GuessingGame.loadGame();
  const message = document.getElementById("message");
  const form = document.getElementById("guess-form");
  const input = document.getElementById("guess-input");
  const attemptsDisplay = document.getElementById("attempts");
  
  function updateAttemptsDisplay() {
    attemptsDisplay.textContent = `Attempts: ${game.attempts}/${game.maxAttempts}`;
  }
  
  form.addEventListener("submit", function (event) {
    event.preventDefault();
    const userGuess = parseInt(input.value);
  
    if (isNaN(userGuess) || userGuess < 1 || userGuess > 100) {
      message.textContent = "Please enter a valid number between 1 and 100.";
      return;
    }
  
    const result = game.guess(userGuess);
    updateAttemptsDisplay();
  
    if (result === "correct") {
      message.textContent = `Correct! The number was ${game.targetNumber}.`;
      localStorage.removeItem("guessingGame");
      game = new GuessingGame();
    } else if (game.isGameOver()) {
      message.textContent = `Game over! The correct number was ${game.targetNumber}. `;
      localStorage.removeItem("guessingGame");
      game = new GuessingGame();
    } else {
        message.textContent = `Your guess is ${result}. Attempts left: ${game.maxAttempts - game.attempts}`;
    }
  
    game.saveGame();
    input.value = "";
  });
  
  updateAttemptsDisplay();
  