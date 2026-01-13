@extends('layout')

@section('content')
<div class="cinema-wrapper">
    {{-- Background Animé --}}
    <div class="cosmos-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="grid-overlay"></div>
    </div>

    <div class="container-fluid interface-layer">
        
        {{-- HEADER CINÉMATIQUE --}}
        <div class="header-glass mb-5 fade-in-down">
            <div class="d-flex justify-content-between align-items-center">
                <div class="header-text">
                    <div class="subtitle">CONFIGURATION DU VOTE</div>
                    <h1 class="title-glitch" data-text="{{ $competition->nom_theme }}">
                        {{ $competition->nom_theme }}
                    </h1>
                </div>
                <div class="header-actions">
                    <div class="search-capsule">
                        <i class="fas fa-search"></i>
                        <input type="text" id="playerSearch" placeholder="Rechercher un joueur..." autocomplete="off">
                    </div>
                    <a href="{{ route('vente.votation.list') }}" class="btn-glass-back">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
            
            {{-- Stats Bar --}}
            <div class="stats-bar mt-4">
                <div class="stat-item">
                    <span class="stat-label">TOTAL JOUEURS</span>
                    <span class="stat-value">{{ count($tousLesJoueurs) }}</span>
                </div>
                <div class="divider"></div>
                <div class="stat-item active-stat">
                    <span class="stat-label">SÉLECTIONNÉS</span>
                    <span class="stat-value" id="countDisplay">0</span>
                </div>
            </div>
        </div>

        <form action="{{ route('vente.votation.candidats.update', $competition->idtheme) }}" method="POST" id="selectionForm">
            @csrf
            
            {{-- GRILLE DES JOUEURS (HOLOGRAPHIQUE) --}}
            <div class="cards-grid" id="playersGrid">
                @foreach($tousLesJoueurs as $index => $joueur)
                    @php
                        // 1. Calcul du délai d'animation (Correction variable $delay)
                        $delay = ($index % 20) * 0.05; 

                        // 2. LOGIQUE INTELLIGENTE DE RECUPERATION D'IMAGE
                        $imageUrl = null;
                        
                        // Liste des noms de fichiers potentiels à tester
                        $nomsPossibles = [
                            $joueur->prenom_joueur . ' ' . $joueur->nom_joueur, // Vinicius Junior
                            $joueur->nom_joueur . ' ' . $joueur->prenom_joueur, // Junior Vinicius
                            $joueur->nom_joueur,                                // Vinicius
                            $joueur->prenom_joueur                              // Vinicius
                        ];

                        // Test automatique des fichiers locaux
                        foreach ($nomsPossibles as $nomTest) {
                            $slug = \Illuminate\Support\Str::slug($nomTest);
                            $path = 'img/vote/' . $slug . '.jpg';
                            if (file_exists(public_path($path))) {
                                $imageUrl = asset($path);
                                break;
                            }
                        }

                        // 3. FORCE LE DESTIN (Si automatique échoue, on force pour tes stars)
                        if (!$imageUrl) {
                            $nomComplet = strtolower($joueur->prenom_joueur . ' ' . $joueur->nom_joueur);
                            
                            if (str_contains($nomComplet, 'vinicius')) {
                                $imageUrl = asset('img/vote/vinicius-junior.jpg');
                            } elseif (str_contains($nomComplet, 'ederson')) {
                                $imageUrl = asset('img/vote/ederson-moraes.jpg');
                            } elseif (str_contains($nomComplet, 'endrick')) {
                                $imageUrl = asset('img/vote/endrick-felipe.jpg');
                            } elseif (str_contains($nomComplet, 'alisson')) {
                                $imageUrl = asset('img/vote/alisson-becker.jpg');
                            }
                        }

                        // 4. Fallback URL Base de données ou Placeholder
                        if (!$imageUrl) {
                            if (!empty($joueur->photo_url)) {
                                $imageUrl = $joueur->photo_url;
                            } else {
                                $imageUrl = asset('img/placeholder.jpg');
                            }
                        }

                        $estSelectionne = in_array($joueur->idjoueur, $candidatsIds);
                    @endphp

                    <label class="holo-card {{ $estSelectionne ? 'selected' : '' }} fade-in-up" 
                           style="animation-delay: {{ $delay }}s"
                           data-name="{{ strtolower($joueur->prenom_joueur . ' ' . $joueur->nom_joueur) }}">
                        
                        <input type="checkbox" name="joueurs[]" value="{{ $joueur->idjoueur }}" 
                               class="hidden-input" 
                               {{ $estSelectionne ? 'checked' : '' }}>
                        
                        <div class="card-inner">
                            {{-- Image Layer --}}
                            <div class="image-frame">
                                <img src="{{ $imageUrl }}" alt="{{ $joueur->nom_joueur }}" loading="lazy">
                                <div class="scanline"></div>
                            </div>

                            {{-- Overlay Gradient --}}
                            <div class="card-gradient"></div>

                            {{-- Info Layer --}}
                            <div class="info-layer">
                                <div class="player-identity">
                                    <span class="first-name">{{ $joueur->prenom_joueur }}</span>
                                    <span class="last-name">{{ $joueur->nom_joueur }}</span>
                                </div>
                                <div class="player-meta">
                                    <span class="club-badge">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        {{ optional($joueur->club)->nomclub ?? 'AGENTS LIBRES' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Selection Marker (Neon Check) --}}
                            <div class="neon-marker">
                                <div class="check-icon">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- Shine Effect Element --}}
                            <div class="shine-layer"></div>
                        </div>
                    </label>
                @endforeach
            </div>

            {{-- FOOTER LÉVITATION --}}
            <div class="levitating-dock">
                <div class="dock-glass">
                    <div class="dock-info">
                        <i class="fas fa-info-circle"></i> Modifications non enregistrées
                    </div>
                    <button type="submit" class="btn-save-atomic">
                        <span class="btn-content">
                            <i class="fas fa-save"></i> SAUVEGARDER LA SÉLECTION
                        </span>
                        <div class="btn-glow"></div>
                    </button>
                </div>
            </div>

            <div style="height: 120px;"></div>
        </form>
    </div>
</div>

<style>
    /* =========================================
       CORE UNIVERSE
       ========================================= */
    :root {
        --void-bg: #030508;
        --glass-surface: rgba(255, 255, 255, 0.02);
        --glass-border: rgba(255, 255, 255, 0.06);
        --neon-cyan: #00f3ff;
        --neon-cyan-dim: rgba(0, 243, 255, 0.1);
        --neon-purple: #bc13fe;
        --text-bright: #ffffff;
        --text-dim: #64748b;
        --font-tech: 'Montserrat', sans-serif; 
    }

    .cinema-wrapper {
        background-color: var(--void-bg);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
        font-family: var(--font-tech);
    }

    /* =========================================
       COSMOS BACKGROUND
       ========================================= */
    .cosmos-bg {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        z-index: 0;
        pointer-events: none;
    }

    .grid-overlay {
        position: absolute;
        width: 200%; height: 200%;
        background-image: 
            linear-gradient(var(--glass-border) 1px, transparent 1px),
            linear-gradient(90deg, var(--glass-border) 1px, transparent 1px);
        background-size: 50px 50px;
        transform: perspective(500px) rotateX(60deg) translateY(-100px) translateZ(-200px);
        opacity: 0.15;
        animation: gridMove 20s linear infinite;
    }

    .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(100px);
        opacity: 0.4;
    }

    .orb-1 {
        width: 600px; height: 600px;
        background: var(--neon-purple);
        top: -200px; left: -100px;
        animation: orbFloat 10s ease-in-out infinite alternate;
    }

    .orb-2 {
        width: 500px; height: 500px;
        background: var(--neon-cyan);
        bottom: -100px; right: -100px;
        animation: orbFloat 12s ease-in-out infinite alternate-reverse;
    }

    @keyframes gridMove {
        0% { transform: perspective(500px) rotateX(60deg) translateY(0) translateZ(-200px); }
        100% { transform: perspective(500px) rotateX(60deg) translateY(50px) translateZ(-200px); }
    }

    @keyframes orbFloat {
        0% { transform: translate(0, 0); }
        100% { transform: translate(50px, 30px); }
    }

    .interface-layer {
        position: relative;
        z-index: 10;
        padding: 40px;
    }

    /* =========================================
       HEADER GLASS
       ========================================= */
    .header-glass {
        background: rgba(10, 10, 15, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }

    .subtitle {
        color: var(--neon-cyan);
        letter-spacing: 4px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-shadow: 0 0 10px var(--neon-cyan-dim);
    }

    .title-glitch {
        color: white;
        font-size: 3rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: -1px;
        line-height: 1;
        position: relative;
    }

    /* Search Bar Design */
    .header-actions {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .search-capsule {
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--glass-border);
        border-radius: 50px;
        padding: 12px 25px;
        display: flex;
        align-items: center;
        width: 300px;
        transition: all 0.3s;
    }

    .search-capsule:focus-within {
        border-color: var(--neon-cyan);
        box-shadow: 0 0 20px var(--neon-cyan-dim);
        background: rgba(0,0,0,0.3);
    }

    .search-capsule i { color: var(--text-dim); margin-right: 10px; }
    
    .search-capsule input {
        background: transparent;
        border: none;
        color: white;
        width: 100%;
        font-weight: 600;
        outline: none;
    }

    .btn-glass-back {
        width: 50px; height: 50px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--glass-border);
        display: flex; align-items: center; justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-glass-back:hover {
        background: rgba(255, 42, 109, 0.2);
        border-color: #ff2a6d;
        color: #ff2a6d;
        transform: rotate(90deg);
    }

    /* Stats Bar */
    .stats-bar {
        display: flex;
        align-items: center;
        gap: 30px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--glass-border);
    }

    .stat-item { display: flex; flex-direction: column; }
    .stat-label { font-size: 0.7rem; color: var(--text-dim); letter-spacing: 2px; font-weight: 700; margin-bottom: 5px; }
    .stat-value { font-size: 1.5rem; color: white; font-weight: 800; line-height: 1; }
    .active-stat .stat-value { color: var(--neon-cyan); text-shadow: 0 0 15px var(--neon-cyan-dim); }
    .divider { width: 1px; height: 40px; background: var(--glass-border); }

    /* =========================================
       HOLOGRAPHIC CARDS GRID
       ========================================= */
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 30px;
    }

    .holo-card {
        position: relative;
        height: 320px;
        perspective: 1000px;
        cursor: pointer;
        user-select: none;
    }

    .hidden-input { display: none; }

    .card-inner {
        position: relative;
        width: 100%; height: 100%;
        background: #0f111a;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--glass-border);
        transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        transform-style: preserve-3d;
    }

    /* Hover Effects */
    .holo-card:hover .card-inner {
        transform: translateY(-10px) rotateX(5deg);
        box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        border-color: rgba(255,255,255,0.2);
    }

    /* SELECTED STATE (The Magic) */
    .holo-card.selected .card-inner {
        border-color: var(--neon-cyan);
        box-shadow: 0 0 30px rgba(0, 243, 255, 0.2), inset 0 0 20px rgba(0, 243, 255, 0.1);
    }

    /* Image */
    .image-frame {
        width: 100%; height: 100%;
        position: absolute;
    }

    .image-frame img {
        width: 100%; height: 100%;
        object-fit: cover;
        object-position: top center;
        transition: transform 0.6s;
        filter: grayscale(100%) contrast(120%); /* Noir et blanc par défaut pour style artistique */
    }

    /* En couleur si sélectionné ou hover */
    .holo-card:hover .image-frame img,
    .holo-card.selected .image-frame img {
        filter: grayscale(0%) contrast(110%);
        transform: scale(1.1);
    }

    .card-gradient {
        position: absolute;
        bottom: 0; left: 0; width: 100%; height: 70%;
        background: linear-gradient(to top, #0f111a 10%, transparent);
        z-index: 1;
    }

    /* Typography */
    .info-layer {
        position: absolute;
        bottom: 20px; left: 20px;
        z-index: 2;
        transform: translateZ(20px); /* 3D pop */
    }

    .player-identity { display: flex; flex-direction: column; margin-bottom: 5px; }
    .first-name { font-size: 0.8rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
    .last-name { font-size: 1.4rem; color: white; font-weight: 900; text-transform: uppercase; line-height: 1; }
    
    .holo-card.selected .last-name {
        color: var(--neon-cyan);
        text-shadow: 0 0 10px rgba(0, 243, 255, 0.5);
    }

    .club-badge {
        font-size: 0.7rem;
        background: rgba(255,255,255,0.1);
        padding: 4px 10px;
        border-radius: 4px;
        color: #cbd5e1;
        font-weight: 600;
        display: inline-flex; align-items: center;
    }

    /* NEON MARKER (Check) */
    .neon-marker {
        position: absolute;
        top: 15px; right: 15px;
        z-index: 5;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .check-icon {
        width: 40px; height: 40px;
        background: var(--neon-cyan);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 20px var(--neon-cyan);
    }

    .check-icon svg { width: 24px; height: 24px; fill: #000; }

    .holo-card.selected .neon-marker {
        opacity: 1;
        transform: scale(1);
    }

    /* =========================================
       LEVITATING DOCK (FOOTER)
       ========================================= */
    .levitating-dock {
        position: fixed;
        bottom: 40px;
        left: 0; width: 100%;
        display: flex; justify-content: center;
        z-index: 1000;
        pointer-events: none; /* Let clicks pass through around the dock */
    }

    .dock-glass {
        pointer-events: auto;
        background: rgba(15, 20, 30, 0.8);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 15px 30px;
        border-radius: 100px;
        display: flex;
        align-items: center;
        gap: 30px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.6);
        transform: translateY(100px);
        animation: slideUpDock 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards 0.5s;
    }

    .dock-info { color: #94a3b8; font-size: 0.9rem; font-weight: 500; }
    .dock-info i { color: var(--neon-cyan); margin-right: 8px; }

    .btn-save-atomic {
        background: linear-gradient(135deg, var(--neon-cyan), #00b8d4);
        border: none;
        border-radius: 50px;
        padding: 16px 40px;
        color: #000;
        font-weight: 800;
        font-size: 1rem;
        letter-spacing: 1px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: all 0.3s;
    }

    .btn-save-atomic:hover {
        transform: scale(1.05);
        box-shadow: 0 0 30px var(--neon-cyan);
    }

    /* =========================================
       ANIMATIONS
       ========================================= */
    .fade-in-down { animation: fadeInDown 0.8s ease forwards; opacity: 0; }
    .fade-in-up { animation: fadeInUp 0.8s ease forwards; opacity: 0; }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUpDock {
        to { transform: translateY(0); }
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .header-text h1 { font-size: 1.8rem; }
        .search-capsule { display: none; }
        .dock-glass { width: 90%; flex-direction: column; border-radius: 20px; gap: 15px; }
        .btn-save-atomic { width: 100%; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const grid = document.getElementById('playersGrid');
        const countDisplay = document.getElementById('countDisplay');
        const cards = document.querySelectorAll('.holo-card');
        const searchInput = document.getElementById('playerSearch');

        // 1. UPDATE COUNTER & CLASS LOGIC
        function updateUI() {
            const selected = document.querySelectorAll('.hidden-input:checked').length;
            countDisplay.innerText = selected;
            // Animation petit rebond sur le compteur
            countDisplay.style.transform = 'scale(1.3)';
            setTimeout(() => countDisplay.style.transform = 'scale(1)', 200);
        }

        cards.forEach(card => {
            const input = card.querySelector('.hidden-input');
            
            // Initial State
            if(input.checked) card.classList.add('selected');

            // Click Handler
            card.addEventListener('change', () => {
                if(input.checked) {
                    card.classList.add('selected');
                    playSelectionSound(); // Optionnel : bruitage soft
                } else {
                    card.classList.remove('selected');
                }
                updateUI();
            });

            // 3D TILT EFFECT (JavaScript Vanilla)
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = ((y - centerY) / centerY) * -10; // Max rotation deg
                const rotateY = ((x - centerX) / centerX) * 10;

                const inner = card.querySelector('.card-inner');
                inner.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
            });

            card.addEventListener('mouseleave', () => {
                const inner = card.querySelector('.card-inner');
                inner.style.transform = `rotateX(0) rotateY(0) scale(1)`;
            });
        });

        // 2. SEARCH FILTER
        searchInput.addEventListener('keyup', (e) => {
            const term = e.target.value.toLowerCase();
            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                if(name.includes(term)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Init
        updateUI();

        // 3. SOUND EFFECT (Simulé)
        function playSelectionSound() {
            // Ici on pourrait ajouter un petit 'pop' audio si souhaité
        }
    });
</script>
@endsection