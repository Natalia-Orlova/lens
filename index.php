<!-- <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <style>
      body {
        overflow: hidden;
        touch-action: none; /* Предотвращает скролл при касании */
      }
      #stretchableText {
        width: 90%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        line-height: 32px;
        text-transform: uppercase;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div id="stretchableText">
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Perspiciatis,
        porro, sequi placeat quidem sint voluptate, eaque quaerat qui
        dignissimos commodi ex excepturi. Itaque explicabo aliquam laudantium
        voluptatibus quos cumque sed culpa consequuntur iusto repudiandae iure
        animi, odio vitae quasi blanditiis beatae. Quasi iure praesentium quae
        distinctio itaque facilis ipsam eligendi. Lorem ipsum dolor sit amet
        consectetur adipisicing elit. Totam cumque minus ea nam laudantium
        quibusdam. Eveniet earum, aperiam natus beatae, aliquam voluptatibus
        incidunt repudiandae, reiciendis dolorem officia minus fuga obcaecati
        vitae eum. Nemo optio impedit modi facilis, et excepturi expedita
        voluptates voluptatum nisi veritatis reiciendis ratione iste consequatur
        libero tempora!
      </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.0/p5.js"></script>
    <script>
      let textContainer;
      let textParts = [];

      function setup() {
        createCanvas(windowWidth, windowHeight);
        textContainer = document.getElementById("stretchableText");
        let text = textContainer.textContent;
        textContainer.innerHTML = "";

        console.log("Text parts length:", text.length);

        for (let i = 0; i < text.length; i++) {
          let span = document.createElement("span");
          span.textContent = text[i];
          span.style.display = "inline-block";
          span.style.transformOrigin = "center";
          span.style.transform = "scale(1)"; // Устанавливаем изначальный масштаб
          textContainer.appendChild(span);
          textParts.push(span);
        }
      }

      function draw() {
        background(255);

        for (let i = 0; i < textParts.length; i++) {
          let part = textParts[i];
          let partRect = part.getBoundingClientRect();
          let partCenterX = partRect.left + partRect.width / 2;
          let partCenterY = partRect.top + partRect.height / 2;

          let d = dist(mouseX, mouseY, partCenterX, partCenterY);
          let stretchFactor = map(d, 0, 100, 2, 1, true);

          if (d < 100) {
            part.style.transform = `scale(${stretchFactor})`;
            part.style.filter = "blur(0.3px)";
          } else {
            part.style.transform = "scale(1)";
            part.style.filter = "none";
          }
        }
      }

      document.addEventListener(
        "touchstart",
        function (event) {
          console.log("touchstart event triggered");
          event.preventDefault();
        },
        { passive: false }
      );

      document.addEventListener("touchend", function () {
        console.log("touchend event triggered");
        resetTextStyles();
      });

      function resetTextStyles() {
        console.log("Resetting text styles");
        for (let part of textParts) {
          console.log("Resetting style for:", part);
          part.style.transform = "scale(1)";
          part.style.filter = "none";
        }
      }
    </script>
  </body>
</html> -->


<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Displacement with Motion Blur</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        canvas {
            border: 1px solid black;
            touch-action: none; /* Отключаем стандартное поведение касания */
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <canvas id="canvas" width="600" height="900"></canvas>
    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        let img = new Image();
        img.src = 'test.png'; // Замените на имя вашего изображения

        img.onload = () => {
            console.log('Image loaded');
            resizeCanvas();
        };

        img.onerror = () => {
            console.error('Failed to load image');
        };

        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;
        let touchX = 0;
        let touchY = 0;
        let originalImageData = null;
        let lastUpdateTime = 0;
        const updateInterval = 16; // Интервал обновления в миллисекундах (примерно 60 FPS)

        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault(); // Предотвращаем стандартное поведение
            isDrawing = true;
            const touch = e.touches[0];
            [lastX, lastY] = [touch.clientX, touch.clientY];
            originalImageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        });

        canvas.addEventListener('touchmove', (e) => {
            if (!isDrawing) return;
            e.preventDefault(); // Предотвращаем стандартное поведение
            const touch = e.touches[0];
            [touchX, touchY] = [touch.clientX, touch.clientY];
            const currentTime = Date.now();
            if (currentTime - lastUpdateTime >= updateInterval) {
                requestAnimationFrame(() => displacePixels(touchX, touchY));
                lastUpdateTime = currentTime;
            }
        });

        canvas.addEventListener('touchend', () => {
            isDrawing = false;
            resetImage();
        });

        canvas.addEventListener('touchcancel', () => {
            isDrawing = false;
            resetImage();
        });

        function displacePixels(x, y) {
            const radius = 50;
            const displacementFactor = 10;
            const blurSamples = 5;
            const smoothFactor = 0.5;

            if (originalImageData) {
                ctx.putImageData(originalImageData, 0, 0);
            }

            const dx = (x - lastX) * smoothFactor;
            const dy = (y - lastY) * smoothFactor;
            const length = Math.sqrt(dx * dx + dy * dy);
            const directionX = length > 0 ? dx / length : 0;
            const directionY = length > 0 ? dy / length : 0;

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const pixels = imageData.data;

            for (let i = 0; i < pixels.length; i += 4) {
                const pixelX = (i / 4) % canvas.width;
                const pixelY = Math.floor((i / 4) / canvas.width);
                const distance = Math.sqrt((pixelX - x) ** 2 + (pixelY - y) ** 2);

                if (distance < radius) {
                    const blurFactor = 1 - distance / radius;
                    const displacement = displacementFactor * blurFactor;
                    let r = 0, g = 0, b = 0;
                    for (let j = 0; j < blurSamples; j++) {
                        const offsetX = displacement * directionX * (j / blurSamples);
                        const offsetY = displacement * directionY * (j / blurSamples);

                        const sourceX = pixelX - offsetX;
                        const sourceY = pixelY - offsetY;

                        if (sourceX >= 0 && sourceX < canvas.width && sourceY >= 0 && sourceY < canvas.height) {
                            const sourceIndex = (Math.floor(sourceY) * canvas.width + Math.floor(sourceX)) * 4;
                            r += imageData.data[sourceIndex];
                            g += imageData.data[sourceIndex + 1];
                            b += imageData.data[sourceIndex + 2];
                        }
                    }

                    pixels[i] = r / blurSamples;
                    pixels[i + 1] = g / blurSamples;
                    pixels[i + 2] = b / blurSamples;
                }
            }

            ctx.putImageData(imageData, 0, 0);
            [lastX, lastY] = [x, y];
        }

        function resetImage() {
            if (originalImageData) {
                ctx.putImageData(originalImageData, 0, 0);
            }
        }

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            originalImageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
    </script>
</body>
</html>