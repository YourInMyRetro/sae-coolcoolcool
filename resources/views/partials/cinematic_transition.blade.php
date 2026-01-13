<div id="cinematic-overlay" class="cinematic-overlay">
    
    {{-- CANVAS POUR L'HYPERESPACE --}}
    <canvas id="warp-canvas"></canvas>

    {{-- COUCHE DE GLITCH & SCANLINES --}}
    <div class="scanlines"></div>
    <div class="vignette"></div>
    <div class="noise-overlay"></div>

    {{-- CONTENU CENTRAL --}}
    <div class="cinema-content">
        
        {{-- LOGO QUI SE FORME --}}
        <div class="central-ring-container">
            <div class="ring ring-1"></div>
            <div class="ring ring-2"></div>
            <div class="ring ring-3"></div>
            <div class="ring ring-4"></div>
            <div class="core-logo">
                <i class="fas fa-vote-yea"></i>
            </div>
        </div>

        {{-- TEXTE DE CHARGEMENT HACKER --}}
        <div class="text-sequence">
            <h1 class="glitch-text" data-text="INITIALISATION...">INITIALISATION...</h1>
            <div class="console-logs">
                <div class="log-line" id="log-1">> CONNECTING TO FIFA SERVERS...</div>
                <div class="log-line" id="log-2">> ENCRYPTING VOTE PROTOCOL...</div>
                <div class="log-line" id="log-3">> LOADING PLAYER ASSETS...</div>
                <div class="log-line" id="log-4">> ACCESS GRANTED.</div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill"></div>
            </div>
        </div>
    </div>

    {{-- FLASH FINAL --}}
    <div class="white-flash"></div>
</div>

{{-- SCRIPT D'ANIMATION GSAP + CANVAS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- CONFIGURATION ---
        const overlay = document.getElementById('cinematic-overlay');
        const canvas = document.getElementById('warp-canvas');
        const ctx = canvas.getContext('2d');
        let redirectUrl = '';

        // --- 1. CANVAS WARP SPEED EFFECT ---
        let w, h;
        const stars = [];
        const speedBase = 2; // Vitesse initiale
        let speed = speedBase;
        
        function resize() {
            w = canvas.width = window.innerWidth;
            h = canvas.height = window.innerHeight;
        }
        
        class Star {
            constructor() {
                this.x = Math.random() * w - w / 2;
                this.y = Math.random() * h - h / 2;
                this.z = Math.random() * w; // Profondeur
            }
            update() {
                this.z = this.z - speed;
                if (this.z <= 0) {
                    this.z = w;
                    this.x = Math.random() * w - w / 2;
                    this.y = Math.random() * h - h / 2;
                }
            }
            show() {
                let x = (this.x / this.z) * w;
                let y = (this.y / this.z) * h;
                let r = (w / this.z); // Taille augmente quand ça approche
                
                // Traînée (Motion Blur)
                let px = (this.x / (this.z + speed * 2)) * w;
                let py = (this.y / (this.z + speed * 2)) * h;

                ctx.beginPath();
                ctx.strokeStyle = "rgba(0, 207, 183, " + (1 - this.z/w) + ")"; // Couleur Cyan
                ctx.lineWidth = r;
                ctx.moveTo(px + w / 2, py + h / 2);
                ctx.lineTo(x + w / 2, y + h / 2);
                ctx.stroke();
            }
        }

        function initStars() {
            stars.length = 0;
            for (let i = 0; i < 800; i++) {
                stars.push(new Star());
            }
        }

        function animateCanvas() {
            ctx.fillStyle = "rgba(15, 22, 35, 0.3)"; // Trail effect noir
            ctx.fillRect(0, 0, w, h);
            stars.forEach(star => {
                star.update();
                star.show();
            });
            requestAnimationFrame(animateCanvas);
        }

        window.addEventListener('resize', resize);
        resize();
        initStars();
        animateCanvas();


        // --- 2. FONCTION DE LANCEMENT DE L'ANIMATION ---
        window.triggerCinematicTransition = function(url) {
            redirectUrl = url;
            overlay.style.display = 'flex'; // Afficher l'overlay

            const tl = gsap.timeline({
                onComplete: () => {
                    window.location.href = redirectUrl;
                }
            });

            // PHASE 1 : ENTREE BRUTALE (0s - 1s)
            tl.to(overlay, { opacity: 1, duration: 0.1 })
              .to('.scanlines', { opacity: 0.5, duration: 0.5 })
              .fromTo('.central-ring-container', 
                  { scale: 0, rotation: -180, opacity: 0 }, 
                  { scale: 1, rotation: 0, opacity: 1, duration: 1.5, ease: "elastic.out(1, 0.5)" }
              );

            // PHASE 2 : CHARGEMENT TEXTE & ACCELERATION (1s - 5s)
            tl.to('.glitch-text', { opacity: 1, duration: 0.5, onStart: () => glitchTextEffect() }, "-=1")
              .to('#log-1', { opacity: 1, x: 0, duration: 0.5 }, "-=0.5")
              .to('#log-2', { opacity: 1, x: 0, duration: 0.5 })
              .to('#log-3', { opacity: 1, x: 0, duration: 0.5 })
              .to('.progress-bar-fill', { width: '100%', duration: 4, ease: "power2.inOut" }, "-=1.5");

            // Accélération de l'espace (Warp Speed) via la variable speed du Canvas
            tl.to({ val: 2 }, {
                val: 50, // Vitesse max
                duration: 4,
                ease: "expo.in",
                onUpdate: function() {
                    speed = this.targets()[0].val;
                }
            }, "-=4");

            // Rotation des anneaux qui s'affole
            tl.to('.ring-1', { rotation: 360, duration: 2, repeat: 2, ease: "linear" }, "-=4")
              .to('.ring-2', { rotation: -360, duration: 2.5, repeat: 2, ease: "linear" }, "-=4")
              .to('.ring-3', { rotation: 720, duration: 3, repeat: 1, ease: "linear" }, "-=4");

            // PHASE 3 : LE CALME AVANT LA TEMPÊTE (5s - 6s)
            tl.to('.text-sequence', { opacity: 0, duration: 0.5, scale: 0.8 })
              .to('.central-ring-container', { scale: 0.5, duration: 0.5, ease: "back.in(2)" })
              .to('#log-4', { opacity: 1, x: 0, color: '#00cfb7', scale: 1.5, duration: 0.2 });

            // PHASE 4 : IMPLOSION & FLASH (6s - 8s)
            tl.to('.central-ring-container', { 
                scale: 50, // Le cercle grandit jusqu'à avaler l'écran
                opacity: 0, 
                duration: 1.5, 
                ease: "expo.in" 
            })
            .to('.white-flash', { opacity: 1, duration: 0.1 }, "-=0.5") // Écran blanc total
            .to('.white-flash', { backgroundColor: '#000', duration: 2 }); // Fade to black (pendant que la page charge)

        };

        // Petit effet glitch sur le texte
        function glitchTextEffect() {
            const text = document.querySelector('.glitch-text');
            const original = text.getAttribute('data-text');
            const chars = '!<>-_\\/[]{}—=+*^?#________';
            
            let iteration = 0;
            const interval = setInterval(() => {
                text.innerText = original
                .split("")
                .map((letter, index) => {
                    if(index < iteration) return original[index];
                    return chars[Math.floor(Math.random() * chars.length)]
                })
                .join("");
                
                if(iteration >= original.length) clearInterval(interval);
                iteration += 1 / 3;
            }, 30);
        }
    });
</script>

<style>
    /* RESET & OVERLAY */
    .cinematic-overlay {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background-color: #0f1623;
        z-index: 10000; /* Au dessus de tout */
        display: none; /* Caché par défaut */
        justify-content: center;
        align-items: center;
        flex-direction: column;
        overflow: hidden;
        color: white;
        font-family: 'Courier New', Courier, monospace; /* Style Terminal */
    }

    #warp-canvas {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: 1;
    }

    /* COUCHES D'EFFETS */
    .scanlines {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,0) 50%, rgba(0,0,0,0.2) 50%, rgba(0,0,0,0.2));
        background-size: 100% 4px;
        z-index: 2;
        pointer-events: none;
        opacity: 0;
    }

    .vignette {
        position: absolute;
        width: 100%; height: 100%;
        box-shadow: inset 0 0 150px rgba(0,0,0,0.9);
        z-index: 3;
    }

    /* CONTENU */
    .cinema-content {
        position: relative;
        z-index: 10;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* ANNEAUX DU LOGO */
    .central-ring-container {
        position: relative;
        width: 200px; height: 200px;
        display: flex; justify-content: center; align-items: center;
        margin-bottom: 50px;
    }

    .core-logo {
        font-size: 4rem;
        color: #00cfb7;
        z-index: 5;
        text-shadow: 0 0 20px #00cfb7;
    }

    .ring {
        position: absolute;
        border-radius: 50%;
        border: 2px solid transparent;
        border-top-color: #00cfb7;
        border-bottom-color: #00cfb7;
        box-shadow: 0 0 15px rgba(0, 207, 183, 0.3);
    }

    .ring-1 { width: 100%; height: 100%; opacity: 0.8; border-width: 4px; }
    .ring-2 { width: 80%; height: 80%; opacity: 0.6; border-left-color: #00cfb7; border-right-color: #00cfb7; }
    .ring-3 { width: 120%; height: 120%; opacity: 0.4; border: 1px dashed #ffffff; }
    .ring-4 { width: 140%; height: 140%; opacity: 0.2; border: 1px solid rgba(255,255,255,0.2); }

    /* TEXTE & LOGS */
    .text-sequence {
        width: 400px;
    }

    .glitch-text {
        font-size: 2rem;
        font-weight: 900;
        letter-spacing: 5px;
        margin-bottom: 20px;
        opacity: 0;
        text-shadow: 2px 0 #ff00c1, -2px 0 #00cfb7;
    }

    .console-logs {
        text-align: left;
        font-size: 0.9rem;
        color: #00cfb7;
        margin-bottom: 20px;
        height: 100px; /* Espace réservé */
    }

    .log-line {
        opacity: 0;
        transform: translateX(-20px);
        margin-bottom: 5px;
    }

    /* BARRE DE PROGRESSION */
    .progress-bar-container {
        width: 100%;
        height: 6px;
        background: rgba(255,255,255,0.1);
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-bar-fill {
        width: 0%;
        height: 100%;
        background: #00cfb7;
        box-shadow: 0 0 10px #00cfb7;
    }

    /* FLASH */
    .white-flash {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-color: white;
        z-index: 20000;
        opacity: 0;
        pointer-events: none;
    }
</style>