const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

const textElement = document.getElementById('text');
const textRect = textElement.getBoundingClientRect();

function draw() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  if (touchX !== null && touchY !== null) {
    const radius = 100;
    const blur = 20;

    ctx.filter = `blur(${blur}px)`;
    ctx.drawImage(canvas, touchX - radius, touchY - radius, radius * 2, radius * 2, touchX - radius, touchY - radius, radius * 2, radius * 2);
    ctx.filter = 'none';
  }
}

let touchX = null;
let touchY = null;

canvas.addEventListener('touchmove', (event) => {
  event.preventDefault();
  const touch = event.touches[0];
  touchX = touch.clientX;
  touchY = touch.clientY;
  draw();
});

canvas.addEventListener('touchend', () => {
  touchX = null;
  touchY = null;
  draw();
});

draw();