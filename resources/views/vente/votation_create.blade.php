@extends('layout')

@section('content')
<div class="stadium-wrapper">
    
    {{-- BACKGROUND: PELOUSE & PROJECTEURS --}}
    <div class="pitch-bg">
        <div class="grass-pattern"></div>
        <div class="center-circle"></div>
        <div class="half-way-line"></div>
        <div class="spotlights">
            <div class="light light-left"></div>
            <div class="light light-right"></div>
        </div>
        <div class="confetti-container" id="confetti-container"></div>
    </div>

    {{-- CONTAINER PRINCIPAL --}}
    <div class="container py-5 relative-z">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                {{-- LE FORMULAIRE (TABLEAU TACTIQUE) --}}
                <div class="tactical-board fade-in-up" id="tacticalBoard">
                    
                    <div class="board-header">
                        <div class="header-logo">
                            <i class="fas fa-futbol"></i> FIFA MANAGER
                        </div>
                        <h1 class="board-title">CRÉATION DE CAMPAGNE</h1>
                        <div class="board-status">
                            <span class="status-dot"></span> EN ATTENTE
                        </div>
                    </div>

                    <form action="{{ route('vente.votation.store') }}" method="POST" id="penaltyForm">
                        @csrf
                        
                        <div class="board-body">
                            {{-- Input 1 : TITRE --}}
                            <div class="form-group-stadium mb-4">
                                <label>TITRE DU MATCH (SUJET)</label>
                                <div class="input-field">
                                    <i class="fas fa-trophy field-icon-left"></i>
                                    <input type="text" name="nom_theme" class="stadium-input" placeholder="Ex: JOUEUR DE L'ANNÉE 2026" required autocomplete="off">
                                </div>
                            </div>

                            {{-- Input 2 : DATE --}}
                            <div class="form-group-stadium mb-5">
                                <label>COUP DE SIFFLET FINAL (DATE)</label>
                                <div class="input-field">
                                    <i class="far fa-clock field-icon-left"></i>
                                    <input type="date" name="date_fermeture" class="stadium-input date-input-fix" required>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="actions-footer">
                                <a href="{{ route('vente.votation.list') }}" class="btn-bench">
                                    <i class="fas fa-arrow-left me-2"></i> RETOUR VESTIAIRE
                                </a>
                                
                                <button type="button" class="btn-kickoff" id="btnKickoff">
                                    COUP D'ENVOI <i class="fas fa-running ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- LE JEU DE PENALTY --}}
    <div class="penalty-game-overlay" id="penaltyGame">
        
        <div class="game-instructions" id="gameInstructions">
            <h3>TIREZ POUR VALIDER !</h3>
            <p>Visez avec la souris. Cliquez pour frapper.</p>
        </div>

        <div class="scene-3d">
            {{-- LA CAGE (Zone de but définie visuellement pour CSS, logique en JS) --}}
            <div class="goal-post">
                <div class="post-top"></div>
                <div class="post-left"></div>
                <div class="post-right"></div>
                <div class="net"></div>
                
                {{-- LE GARDIEN --}}
                <div class="goalkeeper" id="goalkeeper">
                    <div class="keeper-body">
                        <div class="keeper-jersey-stripe"></div> <div class="keeper-gloves glove-left"></div>
                        <div class="keeper-gloves glove-right"></div>
                        <div class="keeper-face"></div>
                    </div>
                    <div class="keeper-shadow"></div>
                </div>
            </div>

            {{-- LE BALLON --}}
            <div class="ball-wrapper" id="ballWrapper">
                <div class="ball" id="soccerBall">
                    <div class="ball-texture"></div>
                </div>
                <div class="ball-shadow"></div>
            </div>
        </div>

        {{-- VISEUR --}}
        <div class="target-cursor" id="targetCursor"></div>

        {{-- MESSAGES DE FIN --}}
        <div class="goal-message" id="goalMessage">
            <h1>GOAAAL !</h1>
            <p>FORMULAIRE ENVOYÉ</p>
        </div>
        <div class="miss-message" id="missMessage">
            <h1 id="missText">ARRÊT DU GARDIEN !</h1>
            <button class="btn-retry" id="btnRetry">RÉESSAYER</button>
        </div>
    </div>

</div>

<style>
    /* ========================================
       1. AMBIANCE STADE
       ======================================== */
    :root {
        --grass-dark: #1a4d1a;
        --grass-light: #225c22;
        --neon-stadium: #00ff88;
        --panel-bg: #1e2124;
    }

    .stadium-wrapper {
        min-height: 100vh;
        background-color: #0f1215;
        overflow: hidden;
        position: relative;
        font-family: 'Montserrat', sans-serif;
    }

    .pitch-bg {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to bottom, #0a0c10 0%, #1a4d1a 100%);
        z-index: 0;
        perspective: 1000px;
    }

    .grass-pattern {
        position: absolute; bottom: 0; width: 100%; height: 60%;
        background-image: repeating-linear-gradient(
            90deg,
            var(--grass-dark) 0px,
            var(--grass-dark) 50px,
            var(--grass-light) 50px,
            var(--grass-light) 100px
        );
        transform: rotateX(60deg) scale(2);
        opacity: 0.6;
        box-shadow: inset 0 0 200px #000;
    }

    .spotlights { position: absolute; top: 0; width: 100%; height: 100%; pointer-events: none; }
    .light {
        position: absolute; top: -100px; width: 200px; height: 1000px;
        background: linear-gradient(to bottom, rgba(255,255,255,0.4), transparent);
        transform-origin: top center;
        filter: blur(20px);
    }
    .light-left { left: 20%; transform: rotate(25deg); animation: swayLeft 10s infinite ease-in-out; }
    .light-right { right: 20%; transform: rotate(-25deg); animation: swayRight 12s infinite ease-in-out; }

    @keyframes swayLeft { 0%, 100% { transform: rotate(25deg); } 50% { transform: rotate(15deg); } }
    @keyframes swayRight { 0%, 100% { transform: rotate(-25deg); } 50% { transform: rotate(-15deg); } }

    .relative-z { position: relative; z-index: 10; }

    /* ========================================
       2. TABLEAU TACTIQUE
       ======================================== */
    .tactical-board {
        background: var(--panel-bg);
        border-radius: 15px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.6);
        border: 2px solid #333;
        overflow: hidden;
        transition: transform 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55), opacity 0.5s;
    }

    .board-header {
        background: #15171a;
        padding: 30px;
        border-bottom: 2px solid #333;
        display: flex; justify-content: space-between; align-items: center;
    }

    .header-logo { color: #888; font-weight: 700; letter-spacing: 2px; font-size: 0.8rem; }
    
    .board-title {
        color: white; font-weight: 900; margin: 0; font-size: 1.8rem;
        text-transform: uppercase;
        background: linear-gradient(to right, #fff, #bbb);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }

    .board-status { color: var(--neon-stadium); font-size: 0.8rem; font-weight: bold; }
    .status-dot { 
        display: inline-block; width: 8px; height: 8px; background: var(--neon-stadium); 
        border-radius: 50%; margin-right: 5px; animation: blink 2s infinite; 
    }

    .board-body { padding: 50px; background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png'); }

    .form-group-stadium label {
        color: #aaa; font-size: 0.8rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 10px; display: block;
    }

    .input-field { position: relative; }
    
    .stadium-input {
        width: 100%; background: #2b2e33; border: 1px solid #444; color: white;
        padding: 15px 20px 15px 50px; 
        font-size: 1.1rem; border-radius: 8px; font-weight: 600;
        transition: 0.3s;
    }
    
    .stadium-input[type="date"] { color-scheme: dark; }
    .stadium-input::-webkit-calendar-picker-indicator {
        cursor: pointer; opacity: 0.6; transition: 0.2s;
    }
    .stadium-input::-webkit-calendar-picker-indicator:hover {
        opacity: 1; background-color: rgba(255,255,255,0.1); border-radius: 4px;
    }

    .stadium-input:focus {
        border-color: var(--neon-stadium); outline: none;
        box-shadow: 0 0 15px rgba(0, 255, 136, 0.2); background: #32363b;
    }
    
    .field-icon-left {
        position: absolute; left: 20px; top: 50%; transform: translateY(-50%);
        color: #666; font-size: 1.2rem; pointer-events: none;
    }

    .actions-footer {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.05);
    }

    .btn-bench {
        color: #888; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.3s;
        display: inline-flex; align-items: center;
    }
    .btn-bench:hover { color: white; transform: translateX(-5px); }

    .btn-kickoff {
        background: linear-gradient(135deg, var(--neon-stadium), #00cc6a);
        color: #00331a; border: none; padding: 15px 40px; border-radius: 50px;
        font-weight: 800; font-size: 1.1rem; letter-spacing: 1px;
        box-shadow: 0 5px 20px rgba(0, 255, 136, 0.3);
        transition: 0.3s; cursor: pointer; position: relative; z-index: 5;
    }
    .btn-kickoff:hover {
        transform: translateY(-3px) scale(1.05); box-shadow: 0 10px 30px rgba(0, 255, 136, 0.5);
    }

    /* ========================================
       3. PENALTY GAME 
       ======================================== */
    .penalty-game-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        z-index: 100; display: none; cursor: none; perspective: 1200px; overflow: hidden;
    }

    .game-instructions {
        position: absolute; top: 15%; width: 100%; text-align: center; color: white;
        text-shadow: 0 2px 10px rgba(0,0,0,0.8); pointer-events: none; z-index: 200;
    }
    .game-instructions h3 { font-weight: 900; font-size: 3rem; margin-bottom: 0; animation: pulseText 1s infinite; }

    .scene-3d {
        position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);
        width: 1000px; height: 100%; pointer-events: none; transform-style: preserve-3d;
    }

    .goal-post {
        position: absolute; bottom: 250px; left: 50%; 
        transform: translateX(-50%) translateZ(-500px);
        width: 500px; height: 220px;
        transform-style: preserve-3d;
    }
    .post-top {
        position: absolute; top: 0; left: 0; width: 100%; height: 12px; background: white;
        border-radius: 10px; box-shadow: 0 5px 10px rgba(0,0,0,0.5);
    }
    .post-left, .post-right {
        position: absolute; top: 0; width: 12px; height: 100%; background: white; border-radius: 10px;
    }
    .post-left { left: 0; }
    .post-right { right: 0; }
    
    .net {
        position: absolute; top: 5px; left: 5px; width: 490px; height: 215px;
        background: repeating-linear-gradient(0deg, transparent, transparent 19px, rgba(255,255,255,0.2) 20px), repeating-linear-gradient(90deg, transparent, transparent 19px, rgba(255,255,255,0.2) 20px);
        opacity: 0.5; transform-origin: top; transform: rotateX(30deg) translateZ(-50px);
        border: 1px solid rgba(255,255,255,0.3);
    }

    /* GARDIEN (Design amélioré) */
    .goalkeeper {
        position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);
        width: 70px; height: 130px;
        transition: left 0.1s linear;
    }
    .keeper-body {
        width: 100%; height: 100%; background: #ffcc00; border-radius: 15px 15px 0 0;
        box-shadow: inset 0 -10px 20px rgba(0,0,0,0.3); position: relative;
    }
    .keeper-jersey-stripe {
        position: absolute; top: 30px; width: 100%; height: 20px; background: #000; opacity: 0.8;
    }
    .keeper-face {
        position: absolute; top: 10px; left: 20px; width: 30px; height: 35px; 
        background: #dcb083; border-radius: 50%;
    }
    .keeper-gloves {
        position: absolute; top: 60px; width: 25px; height: 25px; background: white; border-radius: 50%; border: 3px solid #ccc;
    }
    .glove-left { left: -10px; }
    .glove-right { right: -10px; }
    .keeper-shadow {
        position: absolute; bottom: -10px; width: 100%; height: 20px; background: rgba(0,0,0,0.5);
        border-radius: 50%; filter: blur(5px); transform: scaleY(0.5);
    }

    /* BALLON */
    .ball-wrapper {
        position: absolute; bottom: 50px; left: 50%; 
        transform: translateX(-50%) translateZ(0px);
        width: 50px; height: 50px;
        transform-style: preserve-3d; z-index: 150;
    }
    .ball {
        width: 50px; height: 50px; background: white; border-radius: 50%;
        position: relative; overflow: hidden;
        box-shadow: inset -5px -5px 15px rgba(0,0,0,0.3);
        animation: rotateBallIdle 10s linear infinite;
    }
    .ball-texture {
        width: 100%; height: 100%;
        background-image: radial-gradient(circle at 30% 30%, black 10%, transparent 12%);
        background-size: 25px 25px;
    }
    .ball-shadow {
        position: absolute; bottom: -10px; left: 5px; width: 40px; height: 10px;
        background: black; border-radius: 50%; opacity: 0.6; filter: blur(4px); z-index: -1;
    }

    /* VISEUR */
    .target-cursor {
        position: fixed; width: 40px; height: 40px;
        border: 2px solid rgba(255,255,255,0.8); border-radius: 50%;
        transform: translate(-50%, -50%); pointer-events: none; z-index: 300;
        box-shadow: 0 0 10px rgba(255,255,255,0.5); transition: transform 0.05s ease-out;
    }
    .target-cursor::after {
        content: ''; position: absolute; top: 50%; left: 50%; width: 4px; height: 4px;
        background: red; border-radius: 50%; transform: translate(-50%, -50%);
    }

    /* MESSAGES */
    .goal-message, .miss-message {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0);
        text-align: center; color: white; z-index: 300; pointer-events: none;
        transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .miss-message.show { pointer-events: auto; }
    .goal-message h1 {
        font-size: 6rem; font-weight: 900; color: var(--neon-stadium);
        text-shadow: 0 0 50px var(--neon-stadium); margin: 0;
    }
    .miss-message h1 {
        font-size: 4rem; font-weight: 900; color: #ff3333; text-shadow: 0 0 30px #ff3333;
    }
    .goal-message.show, .miss-message.show { transform: translate(-50%, -50%) scale(1); }

    .btn-retry {
        background: white; color: black; border: none; padding: 15px 40px; font-weight: bold;
        margin-top: 20px; cursor: pointer; border-radius: 50px; font-size: 1.2rem;
        box-shadow: 0 0 20px rgba(255,255,255,0.5); transition: 0.2s;
    }
    .btn-retry:hover { transform: scale(1.1); }

    /* ANIMATIONS */
    @keyframes fadeUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
    @keyframes pulseText { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
    .fade-in-up { animation: fadeUp 0.8s ease-out forwards; }
    
    .ball-shoot { animation: shootBall3D 0.7s forwards cubic-bezier(0.1, 0.5, 0.6, 1.0); }
    @keyframes shootBall3D {
        0% { transform: translate3d(0, 0, 0) rotate(0deg); }
        100% { transform: translate3d(var(--targetX), var(--targetY), -600px) rotate(720deg); }
    }
    @keyframes rotateBallIdle { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnKickoff = document.getElementById('btnKickoff');
        const tacticalBoard = document.getElementById('tacticalBoard');
        const penaltyGame = document.getElementById('penaltyGame');
        const cursor = document.getElementById('targetCursor');
        const goalkeeper = document.getElementById('goalkeeper');
        const ballWrapper = document.getElementById('ballWrapper');
        const penaltyForm = document.getElementById('penaltyForm');
        const btnRetry = document.getElementById('btnRetry');
        const missText = document.getElementById('missText');
        
        let gameActive = false;
        let canShoot = false;

        // 1. CONFIGURATION DU JEU
        const goalWidth = 500;
        const goalHeight = 220;
        
        // On calcule la position visuelle du but sur l'écran
        // Le but est centré horizontalement et positionné en bas avec CSS
        // Ces valeurs sont approximatives pour la logique de "Hitbox"
        const centerScreenX = window.innerWidth / 2;
        const goalTopScreenY = window.innerHeight - 470; // Environ le haut de la barre
        const goalBottomScreenY = window.innerHeight - 250; // Le sol
        
        // Zone du but (Hitbox)
        const goalLeft = centerScreenX - (goalWidth / 2) + 20; // +20 marge poteau
        const goalRight = centerScreenX + (goalWidth / 2) - 20;

        // 2. LANCEMENT
        btnKickoff.addEventListener('click', () => {
            const theme = document.querySelector('input[name="nom_theme"]').value;
            const date = document.querySelector('input[name="date_fermeture"]').value;
            if(!theme || !date) { alert('Remplissez la tactique avant de tirer !'); return; }

            tacticalBoard.style.transform = "scale(0.8) translateY(-1000px)";
            tacticalBoard.style.opacity = "0";
            
            setTimeout(() => {
                penaltyGame.style.display = 'block';
                gameActive = true;
                setTimeout(() => { canShoot = true; startGoalkeeperAI(); }, 300);
            }, 500);
        });

        // 3. VISEUR
        document.addEventListener('mousemove', (e) => {
            if (!gameActive) return;
            cursor.style.left = e.clientX + 'px';
            cursor.style.top = e.clientY + 'px';
        });

        // 4. IA GARDIEN
        let keeperPos = 50; 
        let keeperDirection = 1;
        let keeperInterval;

        function startGoalkeeperAI() {
            clearInterval(keeperInterval);
            keeperInterval = setInterval(() => {
                if(!canShoot) return;
                // Mouvement un peu erratique pour feinter
                if(Math.random() > 0.96) keeperDirection *= -1;
                keeperPos += keeperDirection * 1.5;
                if(keeperPos > 85) keeperDirection = -1;
                if(keeperPos < 15) keeperDirection = 1;
                goalkeeper.style.left = keeperPos + '%';
            }, 16);
        }

        // 5. TIR (CLICK)
        document.addEventListener('click', (e) => {
            if (!gameActive || !canShoot) return;
            if(e.target.closest('.btn-retry')) return;

            shoot(e.clientX, e.clientY);
        });

        function shoot(aimX, aimY) {
            canShoot = false;
            clearInterval(keeperInterval);
            
            // Calcul trajectoire animation CSS
            const animX = (aimX - window.innerWidth / 2); 
            // On triche visuellement : on remonte un peu le point d'arrivée pour l'effet de profondeur
            const animY = (aimY - window.innerHeight) + 150; 

            ballWrapper.style.setProperty('--targetX', animX + 'px');
            ballWrapper.style.setProperty('--targetY', animY + 'px');
            ballWrapper.classList.add('ball-shoot');

            // --- LOGIQUE HITBOX (COLLISION) ---
            
            // 1. Est-ce cadré ?
            const isOnTargetX = aimX >= goalLeft && aimX <= goalRight;
            const isOnTargetY = aimY >= goalTopScreenY && aimY <= goalBottomScreenY;
            
            let result = 'GOAL'; // Par défaut

            if (!isOnTargetX || !isOnTargetY) {
                result = 'MISS'; // Non cadré
                missText.innerText = "NON CADRÉ !";
                // Le gardien ne plonge pas ou plonge au hasard
                simulateKeeperDive(aimX, false); 
            } else {
                // C'est cadré, vérifions le gardien
                
                // Position actuelle du gardien en pixels (centre du gardien)
                const keeperPixelX = (keeperPos / 100) * window.innerWidth;
                
                // Distance entre le tir et le gardien
                const distance = Math.abs(aimX - keeperPixelX);
                
                // Portée du gardien (sa largeur + son plongeon)
                // Disons qu'il peut couvrir 120px de chaque côté
                const saveRange = 140; 

                if (distance < saveRange) {
                    // C'est à portée !
                    result = 'SAVE';
                    missText.innerText = "ARRÊT DU GARDIEN !";
                    // Le gardien plonge EXACTEMENT sur la balle
                    simulateKeeperDive(aimX, true); 
                } else {
                    // Trop loin pour lui
                    result = 'GOAL';
                    // Il essaie mais n'y arrive pas (plonge vers la balle mais s'arrête avant)
                    simulateKeeperDive(aimX, false);
                }
            }

            setTimeout(() => {
                if (result === 'GOAL') showGoal();
                else showMiss();
            }, 700);
        }

        function simulateKeeperDive(targetPixelX, success) {
            const screenW = window.innerWidth;
            let targetPercent = (targetPixelX / screenW) * 100;
            
            goalkeeper.style.transition = "left 0.5s ease-out, transform 0.5s";
            
            if (success) {
                // Plonge sur la balle
                goalkeeper.style.left = targetPercent + '%';
            } else {
                // Plonge vers la balle mais pas assez loin (ou ne bouge pas si c'est trop loin)
                // Ajout d'un "lag" ou d'une erreur
                const currentKeeperPos = parseFloat(goalkeeper.style.left) || 50;
                const diff = targetPercent - currentKeeperPos;
                // Il fait la moitié du chemin seulement
                goalkeeper.style.left = (currentKeeperPos + (diff * 0.6)) + '%';
            }
            
            // Rotation du corps pour simuler le plongeon
            if (targetPixelX < (window.innerWidth/2)) {
                goalkeeper.style.transform = "translateX(-50%) rotate(-45deg)";
            } else {
                goalkeeper.style.transform = "translateX(-50%) rotate(45deg)";
            }
        }

        function showGoal() {
            document.getElementById('goalMessage').classList.add('show');
            createConfetti();
            setTimeout(() => { penaltyForm.submit(); }, 2000);
        }

        function showMiss() {
            document.getElementById('missMessage').classList.add('show');
        }

        // 6. BOUTON RETRY
        btnRetry.addEventListener('click', (e) => {
            e.stopPropagation(); 
            
            document.getElementById('missMessage').classList.remove('show');
            ballWrapper.classList.remove('ball-shoot');
            ballWrapper.style.transform = 'translate3d(0,0,0)'; 
            goalkeeper.style.transition = "none"; // Reset immédiat
            goalkeeper.style.transform = "translateX(-50%) rotate(0deg)";
            
            setTimeout(() => {
                canShoot = true;
                startGoalkeeperAI();
            }, 200);
        });

        // Confetti
        function createConfetti() {
            const colors = ['#00ff88', '#ffffff', '#ffff00'];
            for(let i=0; i<100; i++) {
                const conf = document.createElement('div');
                conf.style.position = 'absolute';
                conf.style.width = '8px'; conf.style.height = '8px';
                conf.style.background = colors[Math.floor(Math.random()*colors.length)];
                conf.style.left = '50%'; conf.style.top = '50%';
                conf.style.transition = 'all 1s ease-out';
                document.getElementById('penaltyGame').appendChild(conf);
                
                setTimeout(() => {
                    conf.style.left = (Math.random()*100) + '%';
                    conf.style.top = (Math.random()*100) + '%';
                    conf.style.opacity = 0;
                }, 10);
            }
        }
    });
</script>
@endsection