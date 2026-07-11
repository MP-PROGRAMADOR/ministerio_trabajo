        <!-- ===== FIN DEL CONTENIDO ===== -->

        <!-- ===== FOOTER INSTITUCIONAL ===== -->
        <footer class="bg-white text-center py-4 text-muted small mt-auto border-top">
            <div class="container">
                <p class="m-0 fw-medium">&copy; 2026 Ministerio de Trabajo, Fomento del Empleo y Seguridad Social.</p>
                <p class="m-0 text-uppercase tracking-wider mt-1" style="font-size: 0.65rem; color: var(--gov-green); font-weight: 700;">Unidad • Paz • Justicia</p>
            </div>
        </footer>

    </div> <!-- .main-wrapper -->

    <!-- ===== SCRIPTS ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // CANVAS ANIMADO (TEXTURA SUAVE)
        const canvas = document.getElementById('canvas-background');
        const ctx = canvas.getContext('2d');
        let points = [];
        const maxPoints = 40;
        const maxDistance = 150;

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        class Point {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.vx = (Math.random() - 0.5) * 0.25;
                this.vy = (Math.random() - 0.5) * 0.25;
                this.radius = Math.random() * 2 + 1;
            }
            update() {
                this.x += this.vx;
                this.y += this.vy;
                if (this.x < 0 || this.x > canvas.width) this.vx *= -1;
                if (this.y < 0 || this.y > canvas.height) this.vy *= -1;
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(11, 58, 96, 0.15)';
                ctx.fill();
            }
        }

        for (let i = 0; i < maxPoints; i++) {
            points.push(new Point());
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (let i = 0; i < points.length; i++) {
                points[i].update();
                points[i].draw();

                for (let j = i + 1; j < points.length; j++) {
                    const dx = points[i].x - points[j].x;
                    const dy = points[i].y - points[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < maxDistance) {
                        ctx.beginPath();
                        ctx.moveTo(points[i].x, points[i].y);
                        ctx.lineTo(points[j].x, points[j].y);
                        ctx.strokeStyle = `rgba(11, 58, 96, ${0.1 * (1 - dist / maxDistance)})`;
                        ctx.lineWidth = 0.6;
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }
        animate();
    </script>

</body>
</html>