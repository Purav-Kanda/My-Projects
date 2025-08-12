document.addEventListener("DOMContentLoaded", function () {
  // storing all the game state variables
  const gameState = {
    playerName: "",
    playerAge: 0,
    favoriteColor: "",
    moves: 0,
    cards: [],
    firstCard: null,
    secondCard: null,
    lockBoard: false,
    matchesFound: 0
  };



  // getting all the elements as variables using DOM manipulation
  const introPage = document.getElementById("intro-page");
  const gamePage = document.getElementById("game-page");
  const introForm = document.getElementById("intro-form");
  const errorMessage = document.getElementById("error-message");
  const welcomeMessage = document.getElementById("welcome-message");
  const moveCountDisplay = document.getElementById("move-count");
  const gameGrid = document.getElementById("game-grid");
  const helpButton = document.getElementById("help-button");
  const instructions = document.getElementById("instructions");
  const finalMessage = document.getElementById("final-message");
  const endGameButton = document.getElementById("end-game");
  const quitgamebutton = document.getElementById("quit-game");




  let deck1 = ["C", "F", "B", "E", "G", "A", "H", "D", "E", "H", "A", "G", "B", "D", "C", "F"]
    ;
  function shuffleDeck(originalArray) {
    let array = [...originalArray]; // Create a copy to avoid mutating the original list
    let shuffledArray = [];

    while (array.length > 0) {
      const randomIndex = Math.floor(Math.random() * array.length);
      const randomElement = array.splice(randomIndex, 1)[0]; // Remove the element at that index
      shuffledArray.push(randomElement);
    }

    return shuffledArray;
  }

  const deck = shuffleDeck(deck1);







  function createCards() {
    gameGrid.innerHTML = "";
    gameState.cards = [];
    deck.forEach((value, index) => { //for loop to assign the deck to a single div element tile
      const card = document.createElement("div");
      card.classList.add("card");
      card.dataset.value = value;
      card.dataset.index = index;

      const cardContent = document.createElement("div");
      cardContent.classList.add("card-content");
      cardContent.textContent = "";          // Hidden at the start

      card.appendChild(cardContent);
      gameGrid.appendChild(card);
      gameState.cards.push(card);

      card.addEventListener("click", handleCardClick);
    });
  }

  function createCardsres(deck) {
    gameGrid.innerHTML = ""; // Clear the grid before creating new cards
    gameState.cards = []; // Clear the cards array

    deck.forEach((value, index) => {
      // Create a card element for each item in the deck
      const card = document.createElement("div");
      card.classList.add("card");
      card.dataset.value = value;
      card.dataset.index = index;

      // Create the card content (hidden at the start)
      const cardContent = document.createElement("div");
      cardContent.classList.add("card-content");
      cardContent.textContent = ""; // Hidden content initially

      card.appendChild(cardContent);
      gameGrid.appendChild(card); // Add card to the grid
      gameState.cards.push(card); // Keep track of all cards

      // Add event listener for card click
      card.addEventListener("click", handleCardClick);
    });
  }

  // Handle card click event
  function handleCardClick() {
    if (gameState.lockBoard || this === gameState.firstCard || this.classList.contains("matched")) {
      return;
    }
    flipCard(this);
    if (!gameState.firstCard) {
      gameState.firstCard = this;
    } else {
      gameState.secondCard = this;
      gameState.lockBoard = true;
      gameState.moves++;
      moveCountDisplay.textContent = gameState.moves;
      checkForMatch();
    }
  }

  // Flips the card to show value 
  function flipCard(card) {
    card.classList.add("flipped");
  
    const cardContent = card.querySelector(".card-content");
    cardContent.textContent = card.dataset.value;
    
    card.style.backgroundColor = gameState.favoriteColor;  
  }
  

  // unflips the card (undo's flip)
  function unflipCard(card) {
    card.classList.remove("flipped");
  
    const cardContent = card.querySelector(".card-content");
    cardContent.textContent = "";
    
    card.style.backgroundColor = deftile;
  }
  

  // Checks if cards flipped are match
  function checkForMatch() {
    const isMatch = gameState.firstCard.dataset.value === gameState.secondCard.dataset.value;
    if (isMatch) {
      gameState.firstCard.classList.add("matched");
      gameState.secondCard.classList.add("matched");
      gameState.matchesFound++;
      resetBoard();
      if (gameState.matchesFound === deck.length / 2) {
        setTimeout(endGame, 500);

      }
    } else {
      setTimeout(() => {
        unflipCard(gameState.firstCard);
        unflipCard(gameState.secondCard);
        resetBoard();
      }, 700);
    }
  }

  // Reset value of first card and second card and lockboard for next turn
  function resetBoard() {
    gameState.firstCard = null;
    gameState.secondCard = null;
    gameState.lockBoard = false;
    deftilecolor();

  }

  // gives end game message 
  function endGame() {
    finalMessage.style.display = "block";
    finalMessage.textContent = `Congratulations ${gameState.playerName}! You completed the puzzle in ${gameState.moves} moves.`;
    endGameButton.style.display = "block";
  }

  endGameButton.addEventListener("click", function () {
    gameState.moves = 0;
    gameState.matchesFound = 0;
    moveCountDisplay.textContent = gameState.moves;
    finalMessage.style.display = "none";
    endGameButton.style.display = "none";

    // Shuffle the deck and pass it to createCards
    const shuffledDeck = shuffleDeck(deck1);  // Shuffle deck
    createCardsres(shuffledDeck);  // Pass the shuffled deck to createCards
  });

  // game intro info
  introForm.addEventListener("submit", function (e) {
    e.preventDefault();//prevents page from reloading after it has been filled
    const nameInput = document.getElementById("playerName").value.trim();
    const ageInput = document.getElementById("playerAge").value.trim();
    const colorInput = document.getElementById("favoriteColor").value.trim();

    if (nameInput === "" || ageInput === "" || colorInput === "") {
      errorMessage.textContent = "Please fill in all fields.";
      return;
    }
    errorMessage.textContent = "";

    // Update game state with inputs
    gameState.playerName = nameInput;
    gameState.playerAge = parseInt(ageInput);
    gameState.favoriteColor = colorInput;
    gameState.moves = 0;
    gameState.matchesFound = 0;

    welcomeMessage.textContent = `Welcome to Round One, ${gameState.playerName}!`;
    welcomeMessage.style.color = gameState.favoriteColor;

    // Hide intro page and show game page
    introPage.style.display = "none";
    gamePage.style.display = "block";
    createCards();
  });

  // help button
  helpButton.addEventListener("click", function () {
    instructions.style.display = instructions.style.display === "none" ? "block" : "none";
  });
  function tilecolor() {
    const cards = document.getElementsByClassName("flipped");
    for (let i = 0; i < cards.length; i++) {
      cards[i].style.backgroundColor = gameState.favoriteColor;
    }
  }

  const cards = document.getElementsByClassName("card");

let deftile = "#007AFF";

function deftilecolor() {
  let hasFlipped = false;

  // Check if any card has the "flipped" class
  for (let i = 0; i < cards.length; i++) {
    if (cards[i].classList.contains("flipped")) {
      hasFlipped = true;
      break;  // No need to check further if we found one
    }
  }

  // If no cards are flipped, change the background color
  if (!hasFlipped) {
    for (let i = 0; i < cards.length; i++) {
      cards[i].style.backgroundColor = deftile;
    }
  }
}




  quitgamebutton.style.display = "block";

  quitgamebutton.addEventListener("click", function () {
    gameState.moves = 0;
    introPage.style.display = "block";
    gamePage.style.display = "none";


  });

});
