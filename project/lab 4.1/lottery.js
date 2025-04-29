document.addEventListener("DOMContentLoaded", () => {
    const pickBallBtn = document.getElementById("pickBallBtn");
    const stopBtn = document.getElementById("stopBtn");
    const restartBtn = document.getElementById("restartBtn");
    const ballsContainer = document.getElementById("ballsContainer");
    const scoreDisplay = document.getElementById("scoreDisplay");
  
    // 存放所有球的数组
    let balls = [];
    // 当前分数
    let score = 0;
    // 标记游戏是否结束
    let gameEnded = false;
  
    // 初始化游戏
    function initGame() {
      score = 0;
      gameEnded = false;
      pickBallBtn.disabled = false;
      ballsContainer.innerHTML = "";
      scoreDisplay.textContent = `Score: ${score}`;
      restartBtn.style.display = "none";
  
      // 创建 100 个球（颜色随机，分值1-10）
      // 同时准备图片对应的路径
      const colors = ["red", "white"];
      balls = [];
      for (let i = 0; i < 100; i++) {
        const color = colors[Math.floor(Math.random() * colors.length)];
        const points = Math.floor(Math.random() * 10) + 1;
        balls.push({ color, points });
      }
    }
  
    // 游戏结束
    function endGame() {
      if (!gameEnded) {
        gameEnded = true;
        pickBallBtn.disabled = true;
        scoreDisplay.textContent = `Final Score: ${score}`;
        restartBtn.style.display = "inline-block";
      }
    }
  
    // 抽球事件
    pickBallBtn.addEventListener("click", () => {
      if (balls.length === 0) {
        endGame();
        return;
      }
  
      // 随机抽一个球
      const randomIndex = Math.floor(Math.random() * balls.length);
      const drawnBall = balls[randomIndex];
      balls.splice(randomIndex, 1);
  
      // 创建一个小方块 (div) 来表示抽到的球
      const ballDiv = document.createElement("div");
      ballDiv.classList.add("ball");
      // 根据球的颜色设置不同的 PNG 图片
      if (drawnBall.color === "red") {
        // 假设 red.png 与本文件处于同一目录
        ballDiv.style.backgroundImage = "url('redball.jpg')";
        // 如果还想显示分值，可以设置文字的颜色为白色
        ballDiv.style.color = "#fff";
      } else {
        // 白球对应 white.png
        ballDiv.style.backgroundImage = "url('whiteball.avif')";
        // 对白底来说，可以把文字颜色改为其他颜色
        ballDiv.style.color = "#000";
      }
  
      // 在球上显示点数（可根据需求决定是否保留）
      ballDiv.textContent = drawnBall.points;
  
      // 将球添加到页面
      ballsContainer.appendChild(ballDiv);
  
      // 分数处理
      if (drawnBall.color === "red") {
        score -= drawnBall.points;
        endGame(); // 抽到红球立即结束
      } else {
        score += drawnBall.points;
        scoreDisplay.textContent = `Score: ${score}`;
      }
    });
  
    // “停止”按钮事件
    stopBtn.addEventListener("click", () => {
      endGame();
    });
  
    // “重新开始”按钮事件
    restartBtn.addEventListener("click", () => {
      // 方式1：简单粗暴方式，直接刷新页面
      // window.location.reload();
  
      // 方式2：重置数据和UI，不刷新页面
      initGame();
    });
  
    // 页面加载后第一次初始化游戏
    initGame();
  });
  