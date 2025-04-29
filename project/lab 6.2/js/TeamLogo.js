// 获取 canvas 和上下文
const canvas = document.getElementById('bgCanvas');
const ctx = canvas.getContext('2d');

// 让 canvas 尺寸始终与屏幕匹配
function resizeCanvas() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}
window.addEventListener('resize', resizeCanvas);
resizeCanvas();

// 粒子对象数组
const particles = [];

// 生成粒子数量，可根据需求增加或减少
const PARTICLE_COUNT = 50;

// 随机数函数
function random(min, max) {
  return Math.random() * (max - min) + min;
}

// 初始化粒子
for (let i = 0; i < PARTICLE_COUNT; i++) {
  particles.push({
    x: random(0, canvas.width),
    y: random(0, canvas.height),
    radius: random(1, 3),  // 粒子大小
    alpha: random(0.2, 0.6), // 不透明度（营造朦胧感）
    speedX: random(-0.3, 0.3), // X 轴速度
    speedY: random(-0.3, 0.3)  // Y 轴速度
  });
}

// 动画循环
function animate() {
  // 清空画布
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  // 绘制每个粒子
  particles.forEach(p => {
    // 移动粒子
    p.x += p.speedX;
    p.y += p.speedY;

    // 边缘检测，让粒子能在屏幕范围内来回“弹”
    if (p.x < 0 || p.x > canvas.width) p.speedX = -p.speedX;
    if (p.y < 0 || p.y > canvas.height) p.speedY = -p.speedY;

    // 绘制粒子（发光效果）
    ctx.beginPath();
    ctx.fillStyle = `rgba(255, 255, 255, ${p.alpha})`;
    ctx.arc(p.x, p.y, p.radius, 0, 2 * Math.PI);
    ctx.fill();
  });

  requestAnimationFrame(animate);
}

// 启动动画
animate();
