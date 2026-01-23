@extends('layout')

@section('content')
<div class="ultra-premium-wrapper">
    {{-- Background Animé (Aurora Effect) --}}
    <div class="aurora-bg">
        <div class="aurora-blob blob-1"></div>
        <div class="aurora-blob blob-2"></div>
        <div class="aurora-blob blob-3"></div>
    </div>

    {{-- NOTIFICATION SYSTEM (Dynamic Island Style) --}}
    @if(session('success'))
    <div class="notification-island" id="premiumNotification">
        <div class="island-content">
            <div class="icon-pulse">
                <i class="fas fa-check"></i>
            </div>
            <div class="text-content">
                <span class="title">Succès</span>
                <span class="message">{{ session('success') }}</span>
            </div>
        </div>
        <div class="progress-line"></div>
    </div>
    @endif

    <div class="container-fluid content-layer">
        
        {{-- HEADER SECTION --}}
        <div class="header-glass mb-5">
            <div class="d-flex justify-content-between align-items-end">
                <div class="title-block">
                    <h4 class="sub-header fade-in-up" style="animation-delay: 0.1s;">ADMINISTRATION</h4>
                    <h1 class="main-header fade-in-up" style="animation-delay: 0.2s;">
                        CENTRE DE <span class="text-gradient">VOTE</span>
                    </h1>
                    <p class="description fade-in-up" style="animation-delay: 0.3s;">
                        Pilotez les campagnes électorales avec précision.
                    </p>
                </div>
                
                <div class="actions-block fade-in-up" style="animation-delay: 0.4s;">
                    <a href="{{ route('vente.dashboard') }}" class="btn-glass-secondary me-3">
                        <span class="icon-box"><i class="fas fa-arrow-left"></i></span>
                        <span class="text">Retour</span>
                    </a>
                    <!--<a href="#" onclick="triggerCinematicTransition('{{ route('vente.votation.create') }}'); return false;" class="btn-glass-primary">
                    <span class="icon-box"><i class="fas fa-plus"></i></span>
                    <span class="text">Nouvelle Campagne</span>
                    <div class="shine"></div>
                    </a>-->

                    <a href="{{ route('vente.votation.create') }}" class="btn-kickoff">
                        LANCER UNE CAMPAGNE <i class="fas fa-plus-circle ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- LISTE DES VOTES (GLASS CARDS) --}}
        <div class="vote-grid">
            {{-- Entêtes du tableau (visuels) --}}
            <div class="grid-header fade-in-up" style="animation-delay: 0.5s;">
                <div class="col-name">NOM DE LA CAMPAGNE</div>
                <div class="col-date">DATE LIMITE</div>
                <div class="col-status">ÉTAT DU VOTE</div>
                <div class="col-actions text-end">COMMANDES</div>
            </div>

            <div class="rows-container">
                @forelse($competitions as $index => $vote)
                    @php
                        $estActif = \Carbon\Carbon::parse($vote->date_fermeture)->isFuture();
                        $delay = 0.6 + ($index * 0.1); // Délai progressif pour chaque ligne
                    @endphp
                    
                    <div class="glass-row fade-in-up" style="animation-delay: {{ $delay }}s;">
                        <div class="row-content">
                            
                            {{-- Colonne Nom --}}
                            <div class="col-name">
                                <div class="vote-icon">
                                    <i class="fas fa-poll"></i>
                                </div>
                                <div class="vote-info">
                                    <span class="vote-title">{{ $vote->nom_theme }}</span>
                                    <span class="vote-id">ID: #{{ $vote->idtheme }} • FIFA OFFICIAL</span>
                                </div>
                            </div>

                            {{-- Colonne Date --}}
                            <div class="col-date">
                                <div class="date-capsule">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($vote->date_fermeture)->format('d M Y') }}
                                </div>
                            </div>

                            {{-- Colonne Statut --}}
                            <div class="col-status">
                                @if($estActif)
                                    <div class="status-indicator active">
                                        <span class="dot-pulse"></span>
                                        <span class="status-text">EN LIGNE</span>
                                    </div>
                                @else
                                    <div class="status-indicator closed">
                                        <span class="dot-static"></span>
                                        <span class="status-text">TERMINÉ</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Colonne Actions --}}
                            
                            <div class="col-actions">
                                <div class="action-buttons">
                                    
                                    {{-- 1. ACTION STOP/PLAY --}}
                                    <form action="{{ route('vente.votation.statut', $vote->idtheme) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-icon-action {{ $estActif ? 'btn-stop' : 'btn-play' }}" 
                                                title="{{ $estActif ? 'Clôturer le vote' : 'Rouvrir le vote' }}">
                                            <i class="fas {{ $estActif ? 'fa-stop' : 'fa-play' }}"></i>
                                        </button>
                                    </form>

                                    {{-- 2. ACTION CANDIDATS (Seulement si actif ou pour consulter) --}}
                                    <a href="{{ route('vente.votation.candidats.edit', $vote->idtheme) }}" 
                                       class="btn-icon-action btn-users" 
                                       title="Gérer les candidats">
                                        <i class="fas fa-users-cog"></i>
                                    </a>

                                    {{-- 3. NOUVEAU : BOUTON DESTRUCTION (Seulement si Clôturé) --}}
                                    @if(!$estActif)
                                        <form action="{{ route('vente.votation.delete', $vote->idtheme) }}" method="POST" onsubmit="return confirm('CONFIRMATION REQUISE : Suppression irréversible du vote ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon-action btn-destroy" title="PURGER LE SYSTÈME">
                                                <i class="fas fa-skull"></i>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </div>

                        </div>
                        
                        {{-- Hover Glow Effect --}}
                        <div class="glow-border"></div>
                    </div>
                @empty
                    <div class="empty-state fade-in-up" style="animation-delay: 0.6s;">
                        <div class="empty-icon">
                            <i class="fas fa-wind"></i>
                        </div>
                        <h3>C'est bien vide ici...</h3>
                        <p>Commencez par créer votre première campagne de vote.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    /* ========================================
    CORE & VARIABLES 
    ========================================
    */
    :root {
        --dark-bg: #050505;
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --glass-highlight: rgba(255, 255, 255, 0.15);
        --neon-cyan: #00cfb7;
        --neon-purple: #7b2cbf;
        --neon-red: #ff2a6d;
        --neon-green: #05d588;
        --text-main: #ffffff;
        --text-muted: #94a3b8;
        --font-main: 'Montserrat', sans-serif;
    }

    .ultra-premium-wrapper {
        min-height: 100vh;
        background-color: var(--dark-bg);
        position: relative;
        overflow-x: hidden;
        font-family: var(--font-main);
        padding-bottom: 100px;
    }

    /* ========================================
    AURORA BACKGROUND ANIMATION 
    ========================================
    */
    .aurora-bg {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        z-index: 0;
        overflow: hidden;
        pointer-events: none;
    }

    .aurora-blob {
        position: absolute;
        filter: blur(80px);
        opacity: 0.4;
        animation: floatAurora 10s infinite alternate cubic-bezier(0.4, 0, 0.2, 1);
    }

    .blob-1 {
        top: -10%; left: -10%; width: 50vw; height: 50vw;
        background: radial-gradient(circle, var(--neon-purple), transparent);
        animation-duration: 15s;
    }

    .blob-2 {
        bottom: -10%; right: -10%; width: 60vw; height: 60vw;
        background: radial-gradient(circle, var(--neon-cyan), transparent);
        animation-duration: 12s;
        animation-delay: -5s;
    }

    .blob-3 {
        top: 40%; left: 40%; width: 40vw; height: 40vw;
        background: radial-gradient(circle, rgba(0, 50, 255, 0.3), transparent);
        animation-duration: 18s;
        animation-delay: -8s;
    }

    @keyframes floatAurora {
        0% { transform: translate(0, 0) scale(1); }
        100% { transform: translate(50px, 30px) scale(1.1); }
    }

    .content-layer {
        position: relative;
        z-index: 2;
        padding: 40px 60px;
    }

    /* ========================================
    DYNAMIC ISLAND NOTIFICATION 
    ========================================
    */
    .notification-island {
        position: fixed;
        top: 30px;
        left: 50%;
        transform: translateX(-50%) translateY(-100px);
        background: rgba(15, 20, 30, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 0;
        border-radius: 50px;
        display: flex;
        flex-direction: column;
        z-index: 9999;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        min-width: 400px;
        overflow: hidden;
        animation: slideDownIsland 0.8s cubic-bezier(0.19, 1, 0.22, 1) forwards;
    }

    .island-content {
        display: flex;
        align-items: center;
        padding: 15px 25px;
    }

    .icon-pulse {
        width: 35px; height: 35px;
        background: var(--neon-green);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #000;
        margin-right: 15px;
        animation: pulseGreen 2s infinite;
    }

    .text-content {
        display: flex; flex-direction: column;
    }

    .text-content .title {
        color: var(--neon-green);
        font-weight: 800;
        font-size: 0.75rem;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .text-content .message {
        color: white;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .progress-line {
        height: 3px;
        background: linear-gradient(90deg, var(--neon-green), transparent);
        width: 100%;
        animation: progressShrink 4s linear forwards;
    }

    @keyframes slideDownIsland {
        0% { transform: translateX(-50%) translateY(-150%) scale(0.8); opacity: 0; }
        100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 1; }
    }

    @keyframes progressShrink {
        from { width: 100%; }
        to { width: 0%; }
    }

    @keyframes pulseGreen {
        0% { box-shadow: 0 0 0 0 rgba(5, 213, 136, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(5, 213, 136, 0); }
        100% { box-shadow: 0 0 0 0 rgba(5, 213, 136, 0); }
    }

    /* ========================================
    HEADER TYPOGRAPHY 
    ========================================
    */
    .sub-header {
        color: var(--neon-cyan);
        font-size: 0.9rem;
        letter-spacing: 4px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .main-header {
        color: white;
        font-size: 3.5rem;
        font-weight: 900;
        letter-spacing: -2px;
        line-height: 1;
        margin-bottom: 10px;
    }

    .text-gradient {
        background: linear-gradient(135deg, #fff 0%, var(--neon-cyan) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .description {
        color: var(--text-muted);
        font-size: 1.1rem;
        max-width: 500px;
    }

    /* ========================================
    GLASS BUTTONS 
    ========================================
    */
    .btn-glass-primary, .btn-glass-secondary {
        position: relative;
        display: inline-flex;
        align-items: center;
        padding: 12px 30px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        backdrop-filter: blur(10px);
    }

    .btn-glass-primary {
        background: rgba(0, 207, 183, 0.1);
        border: 1px solid rgba(0, 207, 183, 0.3);
        color: var(--neon-cyan);
    }

    .btn-glass-secondary {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
    }

    .icon-box { margin-right: 10px; font-size: 1.1rem; }

    .btn-glass-primary:hover {
        background: var(--neon-cyan);
        color: #000;
        box-shadow: 0 0 30px rgba(0, 207, 183, 0.4);
        transform: translateY(-3px);
    }

    .btn-glass-secondary:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-3px);
        border-color: white;
    }

    .shine {
        position: absolute;
        top: 0; left: -100%;
        width: 100%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: 0.5s;
    }

    .btn-glass-primary:hover .shine {
        left: 100%;
        transition: 0.5s;
    }

    /* ========================================
    TABLE LAYOUT & GLASS ROWS 
    ========================================
    */
    .grid-header {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        padding: 0 30px;
        margin-bottom: 20px;
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 2px;
        opacity: 0.7;
    }

    .glass-row {
        position: relative;
        background: linear-gradient(145deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        margin-bottom: 15px;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        backdrop-filter: blur(10px);
        transform-style: preserve-3d;
    }

    .row-content {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        align-items: center;
        padding: 25px 30px;
        position: relative;
        z-index: 2;
    }

    .glass-row:hover {
        transform: translateY(-5px) scale(1.01);
        background: linear-gradient(145deg, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.02) 100%);
        border-color: var(--glass-highlight);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }

    /* Colonne 1: Nom */
    .col-name { display: flex; align-items: center; gap: 20px; }
    
    .vote-icon {
        width: 50px; height: 50px;
        background: linear-gradient(135deg, #1e293b, #0f1623);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        color: var(--neon-cyan);
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .vote-info { display: flex; flex-direction: column; }
    .vote-title { font-weight: 700; font-size: 1.1rem; color: white; margin-bottom: 4px; }
    .vote-id { font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px; font-weight: 600; }

    /* Colonne 2: Date */
    .date-capsule {
        background: rgba(255, 255, 255, 0.05);
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.9rem;
        color: #e2e8f0;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid transparent;
        transition: 0.3s;
    }
    
    .glass-row:hover .date-capsule {
        border-color: rgba(255,255,255,0.2);
        background: rgba(255,255,255,0.1);
    }

    /* Colonne 3: Statut */
    .status-indicator {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.8rem;
        letter-spacing: 1px;
        width: fit-content;
    }

    .status-indicator.active {
        background: rgba(5, 213, 136, 0.1);
        color: var(--neon-green);
        border: 1px solid rgba(5, 213, 136, 0.2);
    }

    .status-indicator.closed {
        background: rgba(255, 42, 109, 0.1);
        color: var(--neon-red);
        border: 1px solid rgba(255, 42, 109, 0.2);
    }

    .dot-pulse, .dot-static {
        width: 8px; height: 8px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .dot-pulse {
        background: var(--neon-green);
        box-shadow: 0 0 10px var(--neon-green);
        animation: blink 2s infinite;
    }

    /* STYLE DU BOUTON DESTRUCTION */
    .btn-destroy {
        background: rgba(255, 0, 0, 0.1);
        color: #ff003c;
        border: 1px solid #ff003c;
        box-shadow: 0 0 5px rgba(255, 0, 60, 0.2);
        position: relative;
        overflow: hidden;
        animation: breath-red 3s infinite;
    }

    /* Effet Glitch/Tremblement au survol */
    .btn-destroy:hover {
        background: #ff003c;
        color: black;
        box-shadow: 0 0 25px #ff003c, inset 0 0 10px black;
        animation: glitch-skew 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) both infinite;
    }

    .btn-destroy:hover i {
        animation: shake-icon 0.1s infinite;
    }

    @keyframes breath-red {
        0%, 100% { box-shadow: 0 0 5px rgba(255, 0, 60, 0.2); }
        50% { box-shadow: 0 0 15px rgba(255, 0, 60, 0.6); }
    }

    @keyframes glitch-skew {
        0% { transform: skew(0deg); }
        20% { transform: skew(-10deg); }
        40% { transform: skew(10deg); }
        60% { transform: skew(-5deg); }
        80% { transform: skew(5deg); }
        100% { transform: skew(0deg); }
    }

    @keyframes shake-icon {
        0% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(2px, 2px) rotate(5deg); }
        50% { transform: translate(-2px, -2px) rotate(-5deg); }
        75% { transform: translate(-2px, 2px) rotate(5deg); }
        100% { transform: translate(2px, -2px) rotate(-5deg); }
    }


    .dot-static { background: var(--neon-red); }

    @keyframes blink { 50% { opacity: 0.4; transform: scale(0.8); } }

    /* Colonne 4: Actions */
    .action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-icon-action {
        width: 45px; height: 45px;
        border-radius: 50%;
        border: none;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .btn-stop {
        background: rgba(255, 42, 109, 0.1);
        color: var(--neon-red);
        border: 1px solid rgba(255, 42, 109, 0.2);
    }

    .btn-stop:hover {
        background: var(--neon-red);
        color: white;
        transform: rotate(90deg) scale(1.1);
    }

    .btn-play {
        background: rgba(5, 213, 136, 0.1);
        color: var(--neon-green);
        border: 1px solid rgba(5, 213, 136, 0.2);
    }

    .btn-play:hover {
        background: var(--neon-green);
        color: #000;
        transform: scale(1.1);
    }

    .btn-users {
        background: rgba(255, 255, 255, 0.05);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-users:hover {
        background: white;
        color: black;
        transform: translateY(-5px);
    }

    /* Glow Border Effect */
    .glow-border {
        position: absolute;
        bottom: 0; left: 0;
        width: 100%; height: 1px;
        background: linear-gradient(90deg, transparent, var(--neon-cyan), transparent);
        opacity: 0;
        transition: 0.5s;
    }

    .glass-row:hover .glow-border { opacity: 1; }

    /* ========================================
    ANIMATIONS & UTILS
    ========================================
    */
    .fade-in-up {
        opacity: 0;
        transform: translateY(30px);
        animation: fadeInUpAnim 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }

    @keyframes fadeInUpAnim {
        to { opacity: 1; transform: translateY(0); }
    }

    .empty-state {
        text-align: center;
        padding: 80px;
        color: var(--text-muted);
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.3;
        animation: floatAurora 3s infinite ease-in-out;
    }

    /* RESPONSIVE */
    @media (max-width: 992px) {
        .content-layer { padding: 20px; }
        .grid-header { display: none; }
        .row-content { grid-template-columns: 1fr; gap: 20px; text-align: center; }
        .col-name, .col-actions, .action-buttons { justify-content: center; }
        .col-name { flex-direction: column; }
        .vote-icon { margin-bottom: 10px; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Suppression automatique de la notification après 4s (durée de l'animation CSS)
        const notification = document.getElementById('premiumNotification');
        if (notification) {
            setTimeout(() => {
                notification.style.animation = 'slideUpOut 0.8s forwards';
                setTimeout(() => notification.remove(), 800);
            }, 4000);
        }

        // Effet Tilt 3D sur les lignes au survol
        const rows = document.querySelectorAll('.glass-row');
        
        rows.forEach(row => {
            row.addEventListener('mousemove', (e) => {
                const rect = row.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                // Calcul du centre
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                // Calcul de la rotation (très subtile)
                const rotateX = ((y - centerY) / centerY) * -2; // Max -2deg
                const rotateY = ((x - centerX) / centerX) * 2;  // Max 2deg

                row.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
            });

            row.addEventListener('mouseleave', () => {
                row.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
            });
        });
    });

    // Ajout de l'animation de sortie via CSS injecté dynamiquement pour JS
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes slideUpOut {
            from { transform: translateX(-50%) translateY(0) scale(1); opacity: 1; }
            to { transform: translateX(-50%) translateY(-150%) scale(0.8); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection