const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const gridSize = 20;

// Agregar estos eventos táctiles a document.addEventListener
canvas.addEventListener('touchstart', handleTouchStart);
canvas.addEventListener('touchmove', handleTouchMove);
canvas.addEventListener('touchend', handleTouchEnd);


let touchStartX = null;
let touchStartY = null;

function handleTouchStart(event) {
  touchStartX = event.touches[0].clientX;
  touchStartY = event.touches[0].clientY;
}

function handleTouchMove(event) {

  // Verifica si el evento ocurrió dentro del área del canvas
  const rect = canvas.getBoundingClientRect();
  const x = event.touches[0].clientX - rect.left;
  const y = event.touches[0].clientY - rect.top;

  if (x >= 0 && x <= canvas.width && y >= 0 && y <= canvas.height) {
    event.preventDefault(); // Agrega esta línea solo si el evento ocurrió dentro del área del canvas
  }

  if (!touchStartX || !touchStartY) {
    return;
  }

  // Solo activar en dispositivos móviles
  if (Math.min(window.innerWidth, window.innerHeight) <= 768) {
    const touchEndX = event.touches[0].clientX;
    const touchEndY = event.touches[0].clientY;

    const touchDiffX = touchStartX - touchEndX;
    const touchDiffY = touchStartY - touchEndY;

    if (Math.abs(touchDiffX) > Math.abs(touchDiffY)) {
      // Movimiento horizontal
      if (touchDiffX > 0 && dx === 0) {
        dx = -gridSize;
        dy = 0;
      } else if (touchDiffX < 0 && dx === 0) {
        dx = gridSize;
        dy = 0;
      }
    } else {
      // Movimiento vertical
      if (touchDiffY > 0 && dy === 0) {
        dy = -gridSize;
        dx = 0;
      } else if (touchDiffY < 0 && dy === 0) {
        dy = gridSize;
        dx = 0;
      }
    }
  }

  touchStartX = null;
  touchStartY = null;
}


function handleTouchEnd(event) {
  touchStartX = null;
  touchStartY = null;
}

let snake;
let apple;
let score;
let isGameOver;

function init() {
  snake = [
    { x: gridSize * 5, y: gridSize * 5 },
    { x: gridSize * 4, y: gridSize * 5 },
  ];
  apple = { x: gridSize * 10, y: gridSize * 10 };
  score = 0;
  isGameOver = false;
}

init();

let dx = gridSize;
let dy = 0;

document.addEventListener('keydown', (e) => {
  if (e.key === 'ArrowUp' && dy === 0) {
    dy = -gridSize;
    dx = 0;
  }
  if (e.key === 'ArrowDown' && dy === 0) {
    dy = gridSize;
    dx = 0;
  }
  if (e.key === 'ArrowLeft' && dx === 0) {
    dx = -gridSize;
    dy = 0;
  }
  if (e.key === 'ArrowRight' && dx === 0) {
    dx = gridSize;
    dy = 0;
  }
});

function gameLoop() {
    if (isGameOver) {
      alert(`Game Over! Tu puntuación es: ${score}`);
      init();
    } else {
      setTimeout(() => {
        requestAnimationFrame(gameLoop);
        moveSnake();
        checkCollision();
        draw();
      }, 1000 / 10);
    }
  }
  
  function moveSnake() {
    const head = { x: snake[0].x + dx, y: snake[0].y + dy };
    snake.unshift(head);
    
    if (head.x === apple.x && head.y === apple.y) {
      score += 10;
      moveApple();
    } else {
      snake.pop();
    }
  }
  
  function checkCollision() {
    if (
      snake[0].x < 0 ||
      snake[0].y < 0 ||
      snake[0].x >= canvas.width ||
      snake[0].y >= canvas.height
    ) {
      isGameOver = true;
    }
  
    for (let i = 1; i < snake.length; i++) {
      if (snake[0].x === snake[i].x && snake[0].y === snake[i].y) {
        isGameOver = true;
        break;
      }
    }
  }
  
  function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    snake.forEach((segment, index) => {
      ctx.fillStyle = index === 0 ? '#8BC34A' : '#8BC34A';
      ctx.fillRect(segment.x, segment.y, gridSize, gridSize);
    });
  
    ctx.fillStyle = 'red';
    ctx.fillRect(apple.x, apple.y, gridSize, gridSize);
  }
  
  function moveApple() {
    apple = {
      x: Math.floor(Math.random() * (canvas.width / gridSize)) * gridSize,
      y: Math.floor(Math.random() * (canvas.height / gridSize)) * gridSize,
    };
  }
  
// En juego.js, dentro de la función getTopScores()
function getTopScores() {
  fetch("./php/obtenerScore.php")
      .then((response) => response.json())
      .then((data) => {
          // No hay llamada a Swal.fire() aquí
      })
      .catch((error) => {
          console.error("Error:", error);
      });
}

  
  function updateScoreDisplay() {
    const scoreDisplay = document.getElementById("scoreDisplay");
    scoreDisplay.innerHTML = `<b>Puntaje: </b>${score}`;
  }
  
  function toggleRestartButton(show) {
    const restartBtn = document.getElementById("restartBtn");
    if (show) {
      restartBtn.style.display = "inline-block";
    } else {
      restartBtn.style.display = "none";
    }
  }
  

// En juego.js, modifica la función gameLoop()
function gameLoop() {
  if (isGameOver) {
      saveScore(score).then(() => {
          getTopScores();
          init();
      });
  } else {
      setTimeout(() => {
          requestAnimationFrame(gameLoop);
          moveSnake();
          checkCollision();
          draw();
          updateScoreDisplay();
      }, 1000 / 10);
  }
}

function getTopScores() {
  fetch("./php/obtenerScore.php")
    .then((response) => response.json())
    .then((data) => {
      displayTopScores(data);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function saveScore(score) {
  return new Promise((resolve, reject) => {
    fetch("./php/guardarScore.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ score }),
    })
      .then((response) => response.json())
      .then((result) => {
        console.log(result);
        resolve(result);
      })
      .catch((error) => {
        console.error("Error:", error);
        reject(error);
      });
  });
}
getTopScores();

  gameLoop(); // Añadir esta línea

    