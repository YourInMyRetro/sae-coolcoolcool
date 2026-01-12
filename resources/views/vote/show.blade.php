@extends('layout')

@section('content')
{{-- CSS Spécifique pour cette page (Sera ignoré par le reste du site) --}}
<style>
    :root {
        --fifa-blue: #326295;
        --fifa-green: #00cfb7; /* Turquoise FIFA */
        --fifa-dark: #101010;
    }

    /* Hero Section avec dégradé subtil */
    .vote-hero {
        background: linear-gradient(135deg, var(--fifa-blue) 0%, #1a2a3a 100%);
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    /* Carte Joueur "Ultimate Team" Style */
    .player-card {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 2px solid transparent;
        background: white;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
    }

    .player-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }

    /* État SÉLECTIONNÉ */
    .player-card.selected {
        border-color: var(--fifa-green);
        background-color: #f0fffd;
        box-shadow: 0 0 20px rgba(0, 207, 183, 0.4);
        transform: translateY(-5px);
    }

    .player-card.selected::after {
        content: "\f00c"; /* FontAwesome Check */
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        position: absolute;
        top: 10px;
        right: 10px;
        background: var(--fifa-green);
        color: #003366;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        z-index: 20;
        animation: popIn 0.3s ease-out;
    }

    /* État DÉSACTIVÉ (quand 3 sont déjà choisis) */
    .player-card.disabled-state {
        opacity: 0.5;
        filter: grayscale(0.8);
        cursor: not-allowed;
    }

    /* Image du joueur */
    .card-img-wrapper {
        height: 280px;
        overflow: hidden;
        position: relative;
        background: #e9ecef;
    }
    
    .card-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
        transition: transform 0.5s ease;
    }

    .player-card:hover .card-img-wrapper img {
        transform: scale(1.05);
    }

    /* Bouton Info flottant sur la carte */
    .btn-info-float {
        position: absolute;
        bottom: 10px;
        right: 10px;
        z-index: 15;
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--fifa-blue);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }
    .btn-info-float:hover {
        background: var(--fifa-blue);
        color: white;
        transform: scale(1.1);
    }

    /* Barre du bas */
    .validation-bar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
        padding: 15px 0;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        transform: translateY(100%); /* Caché par défaut */
        transition: transform 0.4s ease-out;
    }

    .validation-bar.visible {
        transform: translateY(0);
    }

    @keyframes popIn {
        0% { transform: scale(0); }
        80% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
</style>

{{-- HEADER DU VOTE --}}
<div class="vote-hero text-center">
    <div class="container">
        <h5 class="text-uppercase text-white-50 mb-2">Vote Officiel</h5>
        <h1 class="display-4 fw-bold mb-3">{{ $vote->nom_theme }}</h1>
        <p class="lead mb-0" style="max-width: 700px; margin: 0 auto; opacity: 0.9;">
            Sélectionnez exactement <strong>3 candidats</strong>.
            <i class="fas fa-info-circle help-trigger" style="color: var(--fifa-green);"
               data-title="Comment voter ?" 
               data-content="Cliquez simplement sur les cartes des joueurs pour les sélectionner. Une fois 3 joueurs choisis, validez votre vote en bas de l'écran."></i>
        </p>
    </div>
</div>

<div class="container pb-5 mb-5">
    
    @if($aDejaVote)
        <div class="alert alert-success text-center py-4 shadow-sm border-0 mb-5" style="background-color: #d1e7dd; color: #0f5132;">
            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
            <h4>Vote enregistré !</h4>
            <p class="mb-0">Merci d'avoir participé à cette élection.</p>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger text-center shadow-sm border-0 mb-5">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('vote.store', $vote->idtheme) }}" method="POST" id="voteForm">
        @csrf
        
        <div class="row g-4">
            @foreach($candidats as $candidat)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    
                    {{-- CARTE JOUEUR --}}
                    {{-- L'ID 'card-XXX' sert au JS pour gérer le clic --}}
                    <div class="player-card h-100 shadow-sm {{ $aDejaVote ? 'disabled-state' : '' }}" 
                         id="card-{{ $candidat->idjoueur }}"
                         onclick="toggleSelection({{ $candidat->idjoueur }})">

                        {{-- Checkbox cachée (C'est elle qui envoie la donnée) --}}
                        <input type="checkbox" name="candidats[]" 
                               id="checkbox-{{ $candidat->idjoueur }}" 
                               value="{{ $candidat->idjoueur }}" 
                               style="display: none;" 
                               {{ $aDejaVote ? 'disabled' : '' }}>

                        <div class="card-img-wrapper">
                            <img src="{{ $candidat->url_photo ?? 'https://placehold.co/400x500?text=No+Image' }}" 
                                 alt="{{ $candidat->nom_joueur }}">
                            
                            {{-- Bouton Info (arrête la propagation du clic pour ne pas sélectionner) --}}
                            <button type="button" class="btn-info-float" 
                                    onclick="event.stopPropagation();" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalCandidat{{ $candidat->idjoueur }}"
                                    title="Voir les stats">
                                <i class="fas fa-info"></i>
                            </button>
                        </div>

                        <div class="card-body text-center py-4">
                            <h5 class="fw-bold text-dark mb-1">{{ $candidat->prenom_joueur }} {{ strtoupper($candidat->nom_joueur) }}</h5>
                            <p class="text-muted small text-uppercase fw-bold mb-0" style="color: var(--fifa-blue);">
                                {{ $candidat->type_affichage ?? 'Nominé' }}
                            </p>
                        </div>
                    </div>

                </div>

                {{-- MODALE (inchangée mais nécessaire) --}}
                <div class="modal fade" id="modalCandidat{{ $candidat->idjoueur }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header text-white" style="background: var(--fifa-blue);">
                                <h5 class="modal-title fw-bold">
                                    <i class="fas fa-user-circle me-2"></i>{{ $candidat->prenom_joueur }} {{ $candidat->nom_joueur }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center mb-4 mb-md-0">
                                        <img src="{{ $candidat->url_photo ?? asset('img/joueurs/default.jpg') }}" class="img-fluid rounded shadow-sm">
                                    </div>
                                    <div class="col-md-8">
                                        <h5 class="fw-bold text-primary mb-3">Biographie & Stats</h5>
                                        <ul class="list-unstyled text-muted mb-4">
                                            <li><i class="fas fa-ruler-vertical me-2 w-25px"></i> Taille : <strong>{{ $candidat->taille_joueur }} m</strong></li>
                                            <li><i class="fas fa-weight-hanging me-2 w-25px"></i> Poids : <strong>{{ $candidat->poids_joueur }} kg</strong></li>
                                            <li><i class="fas fa-tshirt me-2 w-25px"></i> Sélections : <strong>{{ $candidat->nombre_selection ?? 0 }}</strong></li>
                                        </ul>

                                        <h6 class="fw-bold border-bottom pb-2 mb-3">Dernières Actualités</h6>
                                        @if(isset($candidat->articles_lies) && $candidat->articles_lies->count() > 0)
                                            <div class="list-group list-group-flush">
                                                @foreach($candidat->articles_lies as $article)
                                                    <a href="{{ route('blog.show', $article->id_publication) }}" class="list-group-item list-group-item-action px-0">
                                                        <i class="fas fa-newspaper me-2 text-primary"></i> {{ $article->titre_publication }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="small text-muted fst-italic">Aucun article récent lié à ce joueur.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- BARRE DE VALIDATION FLOTTANTE --}}
        @if(!$aDejaVote)
        <div id="validationBar" class="validation-bar">
            <div class="container d-flex justify-content-between align-items-center">
                
                {{-- Indicateur de progression --}}
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="text-muted small text-uppercase fw-bold">Votre sélection</span>
                        <div class="fs-4 fw-bold text-dark" id="counter-text">0 / 3</div>
                    </div>
                    {{-- Petites boules de progression --}}
                    <div class="d-flex gap-1">
                        <div class="progress-dot rounded-circle bg-secondary" style="width:10px; height:10px;" id="dot-1"></div>
                        <div class="progress-dot rounded-circle bg-secondary" style="width:10px; height:10px;" id="dot-2"></div>
                        <div class="progress-dot rounded-circle bg-secondary" style="width:10px; height:10px;" id="dot-3"></div>
                    </div>
                    <i class="fas fa-info-circle help-trigger ms-3" 
                       data-title="Progression" 
                       data-content="La barre se remplit au fur et à mesure. Vous devez atteindre 3/3 pour débloquer le bouton de validation."></i>
                </div>

                {{-- Bouton Valider --}}
                <button type="submit" id="btn-valider" class="btn btn-primary btn-lg rounded-pill px-5 shadow fw-bold" disabled 
                        style="background-color: var(--fifa-green); border: none; color: #003366;">
                    CONFIRMER MON VOTE <i class="fas fa-check ms-2"></i>
                </button>

            </div>
        </div>
        @endif

    </form>
</div>

{{-- JAVASCRIPT DE GESTION DU VOTE --}}
<script>
    const MAX_CHOICES = 3;
    const aDejaVote = @json($aDejaVote); // Récupère la valeur PHP

    function toggleSelection(id) {
        if (aDejaVote) return; // Sécurité si déjà voté

        const checkbox = document.getElementById('checkbox-' + id);
        const card = document.getElementById('card-' + id);
        
        // Compter les cochés actuels
        const allCheckboxes = document.querySelectorAll('input[name="candidats[]"]');
        let checkedCount = 0;
        allCheckboxes.forEach(cb => { if(cb.checked) checkedCount++; });

        // Logique de bascule
        if (!checkbox.checked) {
            // Tentative de sélection
            if (checkedCount < MAX_CHOICES) {
                checkbox.checked = true;
                card.classList.add('selected');
            } else {
                // Animation "Shake" ou alerte visuelle si on dépasse 3 ?
                alert("Vous ne pouvez sélectionner que 3 candidats maximum !");
                return;
            }
        } else {
            // Désélection
            checkbox.checked = false;
            card.classList.remove('selected');
        }

        updateUI();
    }

    function updateUI() {
        const allCheckboxes = document.querySelectorAll('input[name="candidats[]"]');
        const allCards = document.querySelectorAll('.player-card');
        
        let checkedCount = 0;
        allCheckboxes.forEach(cb => { if(cb.checked) checkedCount++; });

        // Mise à jour de la barre du bas
        const bar = document.getElementById('validationBar');
        const counterText = document.getElementById('counter-text');
        const btn = document.getElementById('btn-valider');
        
        // Mise à jour texte et points
        if(counterText) counterText.innerHTML = checkedCount + " / " + MAX_CHOICES;
        
        for(let i=1; i<=3; i++) {
            const dot = document.getElementById('dot-'+i);
            if(dot) {
                if(i <= checkedCount) {
                    dot.classList.remove('bg-secondary');
                    dot.style.backgroundColor = 'var(--fifa-green)';
                } else {
                    dot.classList.add('bg-secondary');
                    dot.style.backgroundColor = '';
                }
            }
        }

        // Afficher/Cacher barre et activer bouton
        if(bar) {
            if(checkedCount > 0) bar.classList.add('visible');
            else bar.classList.remove('visible');
        }

        if(btn) {
            if(checkedCount === MAX_CHOICES) {
                btn.disabled = false;
                btn.classList.add('pulse-animation'); // Petite animation pour attirer l'oeil
            } else {
                btn.disabled = true;
                btn.classList.remove('pulse-animation');
            }
        }

        // Gestion visuelle des cartes non sélectionnées quand on est au max
        allCards.forEach(card => {
            if (!card.classList.contains('selected')) {
                if (checkedCount >= MAX_CHOICES) {
                    card.classList.add('disabled-state');
                } else {
                    card.classList.remove('disabled-state');
                }
            } else {
                card.classList.remove('disabled-state');
            }
        });
    }

    // Initialisation au chargement (pour le retour arrière navigateur)
    document.addEventListener('DOMContentLoaded', updateUI);
</script>

<style>
    /* Animation pulsation bouton valider */
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(0, 207, 183, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(0, 207, 183, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 207, 183, 0); }
    }
    .pulse-animation {
        animation: pulse-green 2s infinite;
    }
</style>
@endsection