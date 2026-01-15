@extends('layout')

@section('content')

{{-- ======================================================================= --}}
{{-- STYLE PREMIUM "YACHINE TROPHY" --}}
{{-- ======================================================================= --}}
<style>
    :root {
        --bg-dark: #0a0a0a;
        --card-bg: #1c1c1c;
        --gold-primary: #FFD700;
        --gold-gradient: linear-gradient(135deg, #FFD700 0%, #B8860B 100%);
        --text-light: #ffffff;
        --text-dim: #aaaaaa;
        --success: #2ecc71;
    }

    body {
        background-color: var(--bg-dark);
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(184, 134, 11, 0.1) 0%, transparent 40%),
            radial-gradient(circle at 90% 80%, rgba(50, 98, 149, 0.1) 0%, transparent 40%);
        color: var(--text-light);
        font-family: 'Open Sans', sans-serif;
    }

    /* --- 1. BARRE DE SÉLECTION (PODIUM STICKY) --- */
    .podium-sticky {
        position: sticky;
        top: 80px; /* Ajuster selon la hauteur de ton header global */
        z-index: 999;
        background: rgba(10, 10, 10, 0.9);
        backdrop-filter: blur(15px);
        border-bottom: 1px solid rgba(255, 215, 0, 0.2);
        padding: 15px 0;
        box-shadow: 0 5px 20px rgba(0,0,0,0.5);
        transition: all 0.3s ease;
    }

    .podium-wrapper {
        display: flex;
        justify-content: center;
        gap: 30px;
    }

    .podium-slot {
        position: relative;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        border: 2px dashed #444;
        background: #1a1a1a;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .podium-slot:hover {
        border-color: var(--gold-primary);
        transform: translateY(-2px);
    }

    .podium-rank {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--gold-gradient);
        color: #000;
        font-weight: 800;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        box-shadow: 0 2px 5px rgba(0,0,0,0.5);
    }

    /* Image dans le slot */
    .slot-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .podium-slot.filled {
        border: 2px solid var(--gold-primary);
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
    }
    .podium-slot.filled .slot-img {
        opacity: 1;
        transform: scale(1);
    }

    /* Bouton Valider (Sticky Bottom optionnel ou dans la barre) */
    .btn-gold-action {
        background: var(--gold-gradient);
        color: #000;
        font-weight: 800;
        text-transform: uppercase;
        border: none;
        padding: 12px 30px;
        border-radius: 50px;
        letter-spacing: 1px;
        opacity: 0.5;
        pointer-events: none;
        transition: all 0.3s;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    .btn-gold-action.active {
        opacity: 1;
        pointer-events: all;
        cursor: pointer;
        animation: pulse-gold 2s infinite;
    }

    @keyframes pulse-gold {
        0% { box-shadow: 0 0 0 0 rgba(255, 215, 0, 0.7); }
        70% { box-shadow: 0 0 0 15px rgba(255, 215, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 215, 0, 0); }
    }

    /* --- 2. HEADER & TITRE --- */
    .page-header {
        text-align: center;
        padding: 60px 0 40px;
    }
    .main-title {
        font-size: 3.5rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: -1px;
        background: var(--gold-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
        text-shadow: 0 10px 30px rgba(0,0,0,0.5); /* Fallback */
    }

    /* --- 3. GRILLE DE CARTES (CSS GRID) --- */
    .cards-grid {
        display: grid;
        /* Magie du responsive : autant de colonnes que possible, min 260px */
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 30px;
        padding-bottom: 80px;
    }

    /* --- 4. DESIGN CARTE JOUEUR --- */
    .player-card {
        background: var(--card-bg);
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 1px solid #333;
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        aspect-ratio: 3/4; /* Format portrait constant */
    }

    .player-card:hover {
        transform: translateY(-10px);
        border-color: var(--gold-primary);
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    }
    
    .player-card.is-selected {
        filter: grayscale(100%);
        opacity: 0.4;
        border: 1px solid #555;
        cursor: not-allowed;
    }

    /* Image du joueur */
    .card-image-container {
        width: 100%;
        height: 100%;
        position: relative;
    }
    
    .card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top center;
        transition: transform 0.5s ease;
    }
    .player-card:hover .card-img {
        transform: scale(1.05);
    }

    /* Overlay dégradé pour le texte */
    .card-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 60%;
        background: linear-gradient(to top, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 40%, transparent 100%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 20px;
        z-index: 2;
    }

    /* Textes */
    .player-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: white;
        text-transform: uppercase;
        line-height: 1.1;
        margin-bottom: 5px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.8);
    }

    .player-club {
        font-size: 0.9rem;
        color: var(--gold-primary);
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .player-stats {
        display: flex;
        gap: 10px;
        font-size: 0.75rem;
        color: var(--text-dim);
    }

    .stat-pill {
        background: rgba(255,255,255,0.1);
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid rgba(255,255,255,0.1);
    }

    /* Indicateur de sélection (Coche verte) */
    .selected-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10;
        backdrop-filter: blur(2px);
    }
    .player-card.is-selected .selected-overlay {
        display: flex;
    }
    .check-icon {
        font-size: 3rem;
        color: var(--success);
        background: white;
        border-radius: 50%;
        padding: 10px;
        box-shadow: 0 0 20px rgba(46, 204, 113, 0.5);
    }

    /* Responsive mobile */
    @media (max-width: 768px) {
        .main-title { font-size: 2rem; }
        .podium-slot { width: 50px; height: 50px; }
        .btn-gold-action span { display: none; } /* Cache le texte, garde l'icône */
        .btn-gold-action { padding: 10px; border-radius: 50%; width: 50px; height: 50px; display:flex; align-items:center; justify-content:center;}
        .cards-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .player-name { font-size: 1rem; }
    }
</style>

{{-- ======================================================================= --}}
{{-- CONTENU --}}
{{-- ======================================================================= --}}

{{-- Header Global --}}
<div class="page-header">
    <div class="container">
        <h5 class="text-uppercase text-white-50 mb-2 ls-2">FIFA FOOTBALL AWARDS</h5>
        <h1 class="main-title">{{ $vote->nom_theme }}</h1>
        <p class="lead text-white-50" style="max-width: 600px; margin: 0 auto;">
            Élisez le meilleur gardien de l'année. Sélectionnez votre 
            <span style="color: var(--gold-primary); font-weight: bold;">TOP 3</span>.
        </p>
    </div>
</div>

<form action="{{ route('vote.store', $vote->idtheme) }}" method="POST" id="voteForm">
    @csrf
    
    {{-- Inputs cachés pour le formulaire --}}
    <div id="hidden-inputs"></div>

    {{-- BARRE DE SÉLECTION STICKY --}}
    <div class="podium-sticky">
        <div class="container d-flex justify-content-between align-items-center">
            
            {{-- Le Podium --}}
            <div class="podium-wrapper">
                {{-- Or --}}
                <div class="podium-slot" id="slot-0" onclick="removeSelection(0)" title="Cliquez pour retirer">
                    <div class="podium-rank">1</div>
                    <img src="" class="slot-img" id="img-0">
                    <i class="fas fa-plus text-white-50" id="icon-0"></i>
                </div>
                {{-- Argent --}}
                <div class="podium-slot" id="slot-1" onclick="removeSelection(1)" title="Cliquez pour retirer">
                    <div class="podium-rank" style="background: linear-gradient(135deg, #e0e0e0 0%, #bdbdbd 100%);">2</div>
                    <img src="" class="slot-img" id="img-1">
                    <i class="fas fa-plus text-white-50" id="icon-1"></i>
                </div>
                {{-- Bronze --}}
                <div class="podium-slot" id="slot-2" onclick="removeSelection(2)" title="Cliquez pour retirer">
                    <div class="podium-rank" style="background: linear-gradient(135deg, #cd7f32 0%, #8b4513 100%); color: white;">3</div>
                    <img src="" class="slot-img" id="img-2">
                    <i class="fas fa-plus text-white-50" id="icon-2"></i>
                </div>
            </div>

            {{-- Action --}}
            <div>
                @if(!$aDejaVote)
                    <button type="button" class="btn-gold-action" id="btn-submit" onclick="submitVote()">
                        <span>Valider le vote</span> <i class="fas fa-check ms-2"></i>
                    </button>
                @else
                    <div class="d-flex align-items-center text-success fw-bold">
                        <i class="fas fa-check-circle fa-2x me-2"></i> 
                        <span class="d-none d-md-inline">VOTE ENREGISTRÉ</span>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- GRILLE DES JOUEURS --}}
    <div class="container mt-5">
        
        {{-- Messages d'erreur --}}
        @if($errors->any())
            <div class="alert alert-danger bg-danger text-white border-0 mb-4 text-center">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ $errors->first() }}
            </div>
        @endif

        <div class="cards-grid">
            @foreach($candidats as $candidat)
                <div class="player-card {{ $aDejaVote ? 'is-selected' : '' }}" 
                     id="card-{{ $candidat->idjoueur }}"
                     onclick="selectPlayer({{ $candidat->idjoueur }}, '{{ $candidat->url_photo }}')">
                    
                    {{-- Overlay "Déjà sélectionné" --}}
                    <div class="selected-overlay">
                        <i class="fas fa-check check-icon"></i>
                    </div>

                    <div class="card-image-container">
                        <img src="{{ $candidat->url_photo ?? asset('img/joueurs/default.jpg') }}" 
                             alt="{{ $candidat->nom_joueur }}" 
                             class="card-img">
                        
                        <div class="card-overlay">
                            <div class="player-name">
                                {{ $candidat->prenom_joueur }}<br>
                                {{ $candidat->nom_joueur }}
                            </div>
                            <div class="player-club">
                                {{ $candidat->type_affichage ?? 'International' }}
                            </div>
                            <div class="player-stats">
                                <div class="stat-pill">
                                    <i class="fas fa-ruler-vertical me-1"></i> {{ $candidat->taille_joueur }}m
                                </div>
                                <div class="stat-pill">
                                    <i class="fas fa-flag me-1"></i> {{ $candidat->nombre_selection ?? 0 }} Sel.
                                </div>
                            </div>
                            
                            {{-- Bouton Détails (Modal) --}}
                            <button type="button" class="btn btn-sm btn-outline-light mt-3 w-100 rounded-pill" 
                                    style="border-color: rgba(255,255,255,0.3); font-size: 0.8rem;"
                                    onclick="event.stopPropagation();"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalCandidat{{ $candidat->idjoueur }}">
                                Voir bio & news
                            </button>
                        </div>
                    </div>
                </div>

                {{-- MODALE INFO (Gardée simple mais sombre) --}}
                <div class="modal fade" id="modalCandidat{{ $candidat->idjoueur }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content text-white" style="background: #222; border: 1px solid #444;">
                            <div class="modal-header border-bottom border-secondary">
                                <h5 class="modal-title">{{ $candidat->prenom_joueur }} {{ $candidat->nom_joueur }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center mb-3">
                                    <img src="{{ $candidat->url_photo ?? asset('img/joueurs/default.jpg') }}" 
                                         class="img-fluid rounded shadow" style="max-height: 200px;">
                                </div>
                                <p class="text-white-50">Dernières actualités associées :</p>
                                @if(isset($candidat->articles_lies) && count($candidat->articles_lies) > 0)
                                    <div class="list-group">
                                        @foreach($candidat->articles_lies as $art)
                                            <a href="{{ route('blog.show', $art->id_publication) }}" class="list-group-item list-group-item-action bg-dark text-white border-secondary">
                                                <i class="fas fa-newspaper me-2 text-warning"></i> {{ $art->titre_publication }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="small text-muted fst-italic">Aucune actualité récente.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</form>

{{-- ======================================================================= --}}
{{-- JS LOGIQUE --}}
{{-- ======================================================================= --}}
<script>
    const MAX_CHOICES = 3;
    const aDejaVote = @json($aDejaVote);
    
    // État du podium : [id1, id2, id3]
    let selections = [null, null, null]; 

    function selectPlayer(id, photoUrl) {
        if (aDejaVote) return;

        // Si déjà sélectionné -> on retire
        const existingIndex = selections.indexOf(id);
        if (existingIndex !== -1) {
            removeSelection(existingIndex);
            return;
        }

        // Sinon -> on cherche un slot vide
        const emptyIndex = selections.indexOf(null);
        if (emptyIndex === -1) {
            // Podium plein : petit effet visuel ou alert
            alert("Votre Top 3 est complet ! Retirez un joueur pour en changer.");
            return;
        }

        // Ajout
        selections[emptyIndex] = id;
        renderSelection(emptyIndex, id, photoUrl);
    }

    function removeSelection(index) {
        if (aDejaVote) return;
        if (selections[index] === null) return;

        const idRemoved = selections[index];
        selections[index] = null;

        // Reset UI du slot
        document.getElementById('slot-' + index).classList.remove('filled');
        document.getElementById('img-' + index).src = '';
        document.getElementById('icon-' + index).style.display = 'block';

        // Reset UI de la carte
        const card = document.getElementById('card-' + idRemoved);
        if(card) card.classList.remove('is-selected');

        updateInputsAndButton();
    }

    function renderSelection(index, id, photoUrl) {
        // UI Slot
        document.getElementById('slot-' + index).classList.add('filled');
        document.getElementById('img-' + index).src = photoUrl ? photoUrl : "{{ asset('img/joueurs/default.jpg') }}";
        document.getElementById('icon-' + index).style.display = 'none';

        // UI Carte
        const card = document.getElementById('card-' + id);
        if(card) {
            card.classList.add('is-selected');
            // Animation flash
            card.animate([
                { transform: 'scale(1)', filter: 'brightness(1.5)' },
                { transform: 'scale(0.95)', filter: 'brightness(1)' }
            ], { duration: 300 });
        }

        updateInputsAndButton();
    }

    function updateInputsAndButton() {
        // 1. Générer les inputs hidden
        const container = document.getElementById('hidden-inputs');
        container.innerHTML = '';
        
        let count = 0;
        selections.forEach(id => {
            if(id !== null) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'candidats[]';
                input.value = id;
                container.appendChild(input);
                count++;
            }
        });

        // 2. Activer/Désactiver le bouton
        const btn = document.getElementById('btn-submit');
        if(btn) {
            if(count === MAX_CHOICES) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        }
    }

    function submitVote() {
        const btn = document.getElementById('btn-submit');
        if(btn.classList.contains('active')) {
            document.getElementById('voteForm').submit();
        }
    }
</script>

@endsection