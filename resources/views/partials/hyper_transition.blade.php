<div id="hyper-transition" class="hyper-container">
    
    {{-- LAYER 1: DEEP SPACE CANVAS --}}
    <canvas id="deep-space"></canvas>

    {{-- LAYER 2: GRID SYSTEM --}}
    <div class="grid-floor top"></div>
    <div class="grid-floor bottom"></div>

    {{-- LAYER 3: UI HUD --}}
    <div class="hud-interface">
        <div class="hud-corner top-left">SYSTEM_OVERRIDE // <span id="fps-counter">60</span> FPS</div>
        <div class="hud-corner top-right">ENCRYPTION: <span class="blink-red">OFF</span></div>
        <div class="hud-corner bottom-left">MEM_USAGE: <span id="mem-usage">128</span> TB</div>
        <div class="hud-corner bottom-right">SECURE_CHANNEL_v9.0</div>
        
        {{-- CENTRAL CORE --}}
        <div class="central-core">
            <div class="ring-outer"></div>
            <div class="ring-middle"></div>
            <div class="ring-inner"></div>
            <div class="core-text">
                <div class="glitch-word" data-text="VOTE">VOTE</div>
                <div class="sub-text">PROTOCOL</div>
            </div>
        </div>

        {{-- PROGRESS BAR --}}
        <div class="loader-wrapper">
            <div class="loader-text">LOADING ASSETS...</div>
            <div class="loader-bar-bg">
                <div class="loader-bar-fill"></div>
            </div>
            <div class="loader-hexs">
                <span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>
    </div>

    {{-- LAYER 4: FX OVERLAYS --}}
    <div class="chromatic-aberration"></div>
    <div class="scanline-overlay"></div>
    <div class="noise-grain"></div>
    <div class="flash-bang"></div>
</div>

<style>
    /* --- VARIABLES CORE --- */
    :root {
        --neon-cyan: #00f3ff;
        --neon-pink: #ff0055;
        --neon-green: #00ff9d;
        --void: #050505;
        --grid-color: rgba(0, 243, 255, 0.15);
    }

    /* --- CONTAINER --- */
    .hyper-container {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background-color: var(--void);
        z-index: 99999;
        display: none; /* Active via JS */
        overflow: hidden;
        cursor: none;
        font-family: 'Courier New', Courier, monospace;
        perspective: 1000px;
    }

    canvas { display: block; width: 100%; height: 100%; position: absolute; z-index: 1; }

    /* --- 3D GRID FLOORS --- */
    .grid-floor {
        position: absolute;
        width: 200%; height: 100%;
        background-image: 
            linear-gradient(90deg, var(--grid-color) 1px, transparent 1px),
            linear-gradient(180deg, var(--grid-color) 1px, transparent 1px);
        background-size: 60px 60px;
        left: -50%;
        z-index: 2;
        opacity: 0;
        transform-style: preserve-3d;
    }

    .grid-floor.bottom {
        bottom: 0;
        transform: rotateX(60deg) translateY(0) translateZ(0);
        background: linear-gradient(to top, var(--void) 20%, transparent 100%), 
                    linear-gradient(90deg, var(--grid-color) 1px, transparent 1px),
                    linear-gradient(180deg, var(--grid-color) 1px, transparent 1px);
        background-size: 60px 60px;
    }

    .grid-floor.top {
        top: 0;
        transform: rotateX(-60deg) translateY(0) translateZ(0);
        background: linear-gradient(to bottom, var(--void) 20%, transparent 100%), 
                    linear-gradient(90deg, var(--grid-color) 1px, transparent 1px),
                    linear-gradient(180deg, var(--grid-color) 1px, transparent 1px);
        background-size: 60px 60px;
    }

    /* --- HUD INTERFACE --- */
    .hud-interface {
        position: absolute; width: 100%; height: 100%; z-index: 10;
        pointer-events: none;
    }

    .hud-corner {
        position: absolute;
        color: var(--neon-cyan);
        font-size: 12px;
        font-weight: bold;
        letter-spacing: 2px;
        padding: 20px;
        opacity: 0;
    }

    .top-left { top: 0; left: 0; border-left: 3px solid var(--neon-cyan); border-top: 3px solid var(--neon-cyan); }
    .top-right { top: 0; right: 0; border-right: 3px solid var(--neon-cyan); border-top: 3px solid var(--neon-cyan); }
    .bottom-left { bottom: 0; left: 0; border-left: 3px solid var(--neon-cyan); border-bottom: 3px solid var(--neon-cyan); }
    .bottom-right { bottom: 0; right: 0; border-right: 3px solid var(--neon-cyan); border-bottom: 3px solid var(--neon-cyan); }

    .blink-red { color: var(--neon-pink); animation: blink 0.2s infinite; }

    /* --- CENTRAL CORE RING --- */
    .central-core {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%) scale(0);
        width: 300px; height: 300px;
        display: flex; justify-content: center; align-items: center;
    }

    .ring-outer, .ring-middle, .ring-inner {
        position: absolute;
        border-radius: 50%;
        border: 2px solid transparent;
        box-shadow: 0 0 20px rgba(0, 243, 255, 0.2);
    }

    .ring-outer {
        width: 100%; height: 100%;
        border-top: 4px solid var(--neon-cyan);
        border-bottom: 4px solid var(--neon-cyan);
        animation: spin 4s linear infinite;
    }

    .ring-middle {
        width: 70%; height: 70%;
        border-left: 4px solid var(--neon-pink);
        border-right: 4px solid var(--neon-pink);
        animation: spinReverse 3s linear infinite;
    }

    .ring-inner {
        width: 40%; height: 40%;
        border: 2px dashed var(--neon-green);
        animation: spin 5s linear infinite;
    }

    .core-text {
        text-align: center;
        color: white;
        z-index: 12;
        mix-blend-mode: screen;
    }

    .glitch-word {
        font-size: 4rem;
        font-weight: 900;
        letter-spacing: 5px;
        position: relative;
    }

    .sub-text {
        font-size: 1rem;
        letter-spacing: 8px;
        color: var(--neon-cyan);
    }

    /* --- LOADING BAR --- */
    .loader-wrapper {
        position: absolute;
        bottom: 100px;
        left: 50%;
        transform: translateX(-50%);
        width: 400px;
        text-align: center;
        opacity: 0;
    }

    .loader-text {
        color: var(--neon-cyan);
        font-size: 10px;
        letter-spacing: 3px;
        margin-bottom: 10px;
        text-align: left;
    }

    .loader-bar-bg {
        width: 100%; height: 4px;
        background: rgba(255,255,255,0.1);
        position: relative;
        overflow: hidden;
    }

    .loader-bar-fill {
        width: 0%; height: 100%;
        background: var(--neon-cyan);
        box-shadow: 0 0 15px var(--neon-cyan);
    }

    .loader-hexs {
        display: flex; gap: 5px; margin-top: 10px; justify-content: center;
    }
    .loader-hexs span {
        width: 10px; height: 10px; background: #333;
        transform: skewX(-20deg);
    }
    .loader-hexs span.lit { background: var(--neon-green); box-shadow: 0 0 10px var(--neon-green); }

    /* --- FX LAYERS --- */
    .scanline-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
        background-size: 100% 2px, 3px 100%;
        z-index: 20; pointer-events: none;
    }

    .noise-grain {
        position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
        background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="noise"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="0.65" numOctaves="3" stitchTiles="stitch"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23noise)" opacity="0.05"/%3E%3C/svg%3E');
        animation: grain 0.5s steps(10) infinite;
        z-index: 19; pointer-events: none;
    }

    .vignette {
        position: absolute; width: 100%; height: 100%;
        box-shadow: inset 0 0 150px rgba(0,0,0,0.9);
        z-index: 18; pointer-events: none;
    }

    .flash-bang {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: white; opacity: 0; z-index: 100; pointer-events: none;
    }

    /* --- ANIMATIONS KEYFRAMES --- */
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    @keyframes spinReverse { 0% { transform: rotate(0deg); } 100% { transform: rotate(-360deg); } }
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
    @keyframes grain { 0%, 100% { transform: translate(0, 0); } 10% { transform: translate(-5%, -10%); } 20% { transform: translate(-15%, 5%); } 30% { transform: translate(7%, -25%); } 40% { transform: translate(-5%, 25%); } 50% { transform: translate(-15%, 10%); } 60% { transform: translate(15%, 0%); } 70% { transform: translate(0%, 15%); } 80% { transform: translate(3%, 35%); } 90% { transform: translate(-10%, 10%); } }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // SETUP
        const container = document.getElementById('hyper-transition');
        const canvas = document.getElementById('deep-space');
        const ctx = canvas.getContext('2d');
        
        let width, height;
        let particles = [];
        let speed = 0.5; // Vitesse initiale
        let animationFrame;
        let redirectUrl = null;

        // 1. ENGINE: CANVAS PARTICLE SYSTEM (STARFIELD 3D)
        // -----------------------------------------------------
        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }
        
        class Particle {
            constructor() {
                this.x = (Math.random() - 0.5) * width * 2;
                this.y = (Math.random() - 0.5) * height * 2;
                this.z = Math.random() * width;
                this.prevZ = this.z;
                this.color = Math.random() > 0.9 ? '#ff0055' : '#00f3ff'; // Neon mix
            }

            update() {
                this.z -= speed;
                if (this.z < 1) {
                    this.z = width;
                    this.x = (Math.random() - 0.5) * width * 2;
                    this.y = (Math.random() - 0.5) * height * 2;
                    this.prevZ = this.z;
                }
            }

            draw() {
                const x = (this.x / this.z) * width * 0.5 + width * 0.5;
                const y = (this.y / this.z) * height * 0.5 + height * 0.5;
                const radius = (width / this.z) * 1.5;
                
                // Trail effect (Motion Blur)
                const prevX = (this.x / (this.z + speed * 2)) * width * 0.5 + width * 0.5;
                const prevY = (this.y / (this.z + speed * 2)) * height * 0.5 + height * 0.5;

                ctx.beginPath();
                ctx.moveTo(prevX, prevY);
                ctx.lineTo(x, y);
                ctx.lineWidth = radius;
                ctx.strokeStyle = this.color;
                ctx.globalAlpha = 1 - (this.z / width);
                ctx.stroke();
                ctx.globalAlpha = 1;
                this.prevZ = this.z;
            }
        }

        function initParticles() {
            particles = [];
            for(let i=0; i<1500; i++) particles.push(new Particle()); // 1500 Particules
        }

        function loop() {
            ctx.fillStyle = 'rgba(5, 5, 5, 0.4)'; // Trail fade
            ctx.fillRect(0, 0, width, height);
            
            particles.forEach(p => {
                p.update();
                p.draw();
            });
            animationFrame = requestAnimationFrame(loop);
        }

        window.addEventListener('resize', resize);
        resize();
        initParticles();
        loop(); // Start rendering in background immediately (ready to show)


        // 2. ENGINE: TIMELINE CHOREOGRAPHY (8 SECONDS)
        // -----------------------------------------------------
        window.launchHyperJump = function(url) {
            redirectUrl = url;
            container.style.display = 'block'; // SHOW TIME

            const tl = gsap.timeline({
                onComplete: () => {
                    window.location.href = redirectUrl;
                }
            });

            // PHASE 1 : SYSTEM WAKE UP (0s - 1.5s)
            // L'écran s'allume, le HUD apparait, la grille se déploie
            tl.fromTo('.hyper-container', {opacity: 0}, {opacity: 1, duration: 0.2})
              .to('.grid-floor', {opacity: 0.6, duration: 1.5, ease: "power2.out"}, 0)
              .to('.grid-floor.bottom', {backgroundPositionY: '1000px', duration: 8, ease: "none"}, 0) // Sol qui bouge
              .to('.grid-floor.top', {backgroundPositionY: '1000px', duration: 8, ease: "none"}, 0)
              .fromTo('.hud-corner', {opacity: 0, x: -50}, {opacity: 1, x: 0, stagger: 0.1, duration: 0.5}, 0.2)
              .fromTo('.central-core', {scale: 0, rotation: -90}, {scale: 1, rotation: 0, duration: 1.5, ease: "elastic.out(1, 0.5)"}, 0.5);

            // PHASE 2 : DATA LOADING (1.5s - 3.5s)
            // Le texte glitch, la barre de chargement se remplit
            tl.to('.loader-wrapper', {opacity: 1, duration: 0.5}, 1.5)
              .to('.loader-bar-fill', {width: '100%', duration: 2, ease: "power2.inOut"}, 1.5)
              .to('.loader-hexs span', {backgroundColor: '#00ff9d', boxShadow: '0 0 10px #00ff9d', stagger: 0.4}, 1.5);

            // PHASE 3 : WARP ENGAGE (3.5s - 6s)
            // Accélération brutale des particules (via variable speed)
            tl.to({val: 0.5}, {
                val: 80, // VITESSE MAXIMALE
                duration: 3,
                ease: "expo.in",
                onUpdate: function() { speed = this.targets()[0].val; }
            }, 3);

            // Secousse de la caméra (Chromatic Aberration)
            tl.to('.chromatic-aberration', {
                duration: 3,
                onUpdate: () => {
                    const shake = Math.random() * 5;
                    container.style.textShadow = `${shake}px 0 red, -${shake}px 0 cyan`;
                    canvas.style.transform = `translate(${Math.random()*4-2}px, ${Math.random()*4-2}px)`;
                }
            }, 3);

            // Le noyau central s'emballe
            tl.to('.ring-outer', {duration: 2, borderTopColor: '#ff0055', borderBottomColor: '#ff0055', rotation: 720}, 4)
              .to('.glitch-word', {scale: 1.5, color: '#ff0055', duration: 2}, 4);

            // PHASE 4 : SINGULARITY (6s - 8s)
            // Tout est aspiré vers le centre
            tl.to('.grid-floor', {opacity: 0, duration: 0.5}, 6)
              .to('.hud-interface', {opacity: 0, scale: 2, duration: 0.5, ease: "power2.in"}, 6)
              .to('.central-core', {scale: 50, opacity: 0, duration: 1.5, ease: "expo.in"}, 6.2);

            // FLASH FINAL
            tl.to('.flash-bang', {opacity: 1, duration: 0.1}, 7.5)
              .to('.flash-bang', {backgroundColor: 'black', duration: 0.5}); // Transition vers le noir pour masquer le load
        };
    });
</script>