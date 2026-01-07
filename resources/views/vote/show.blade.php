@extends('layout')

@section('content')
<div class="container py-5">
    
    {{-- En-tête du vote --}}
    <div class="text-center mb-5">
        <h1 class="fw-bold text-primary">{{ $vote->nom_vote_theme }}</h1>
        <p class="text-muted">Sélectionnez exactement <strong>3 joueurs</strong> et validez votre choix.</p>
        
        @if($aDejaVote)
            <div class="alert alert-success d-inline-block">
                <i class="fas fa-check-circle me-2"></i> Vous avez déjà participé à ce vote.
            </div>
        @endif
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vote.store', $vote->id_vote_theme) }}" method="POST">
        @csrf
        <div class="row g-4">
            @foreach($candidats as $candidat)
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm candidate-card position-relative">
                        
                        {{-- [ID 23] Checkbox de sélection (Cachée si déjà voté) --}}
                        @if(!$aDejaVote)
                        <div class="position-absolute top-0 end-0 p-3" style="z-index: 10;">
                            <input type="checkbox" name="candidats[]" value="{{ $candidat->id_candidat }}" 
                                   class="form-check-input shadow-sm border-2 border-primary candidate-checkbox" 
                                   style="width: 1.5em; height: 1.5em; cursor: pointer;">
                        </div>
                        @endif

                        {{-- Image du joueur --}}
                        <div class="ratio ratio-1x1 overflow-hidden rounded-top">
                            <img src="{{ asset('img/joueurs/' . ($candidat->photo_url ?? 'default.jpg')) }}" 
                                 class="card-img-top object-fit-cover" 
                                 alt="{{ $candidat->nom_candidat }}">
                        </div>

                        <div class="card-body text-center">
                            <h5 class="fw-bold mb-1">{{ $candidat->nom_candidat }}</h5>
                            <p class="text-muted small mb-3">{{ $candidat->nationalite ?? 'International' }}</p>

                            {{-- [ID 22] Bouton "En savoir plus" --}}
                            <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalCandidat{{ $candidat->id_candidat }}">
                                <i class="fas fa-info-circle me-1"></i> Infos & Stats
                            </button>
                        </div>
                    </div>
                </div>

                {{-- [ID 22] MODALE DÉTAILS JOUEUR + ARTICLES LIÉS --}}
                <div class="modal fade" id="modalCandidat{{ $candidat->id_candidat }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-dark text-white">
                                <h5 class="modal-title fw-bold">{{ $candidat->nom_candidat }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <img src="{{ asset('img/joueurs/' . ($candidat->photo_url ?? 'default.jpg')) }}" class="img-fluid rounded mb-3 shadow-sm">
                                        <div class="bg-light p-2 rounded">
                                            <small class="text-muted d-block text-uppercase">Poste</small>
                                            <strong>{{ $candidat->poste ?? 'Attaquant' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Biographie</h6>
                                        <p class="text-muted">{{ $candidat->description ?? "Aucune description disponible pour ce joueur légendaire." }}</p>

                                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3 mt-4">Dans l'actualité (Blog)</h6>
                                        @if($candidat->articles_lies->count() > 0)
                                            <ul class="list-group list-group-flush">
                                                @foreach($candidat->articles_lies as $article)
                                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                        <span>{{ $article->titre_publication }}</span>
                                                        <a href="{{ route('blog.show', $article->id_publication) }}" class="btn btn-sm btn-light text-primary fw-bold">Lire <i class="fas fa-arrow-right"></i></a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="small text-muted fst-italic">Aucun article récent mentionnant ce joueur.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- [ID 23] Barre de validation flottante --}}
        @if(!$aDejaVote)
        <div class="fixed-bottom bg-white border-top shadow py-3 px-4 d-flex justify-content-between align-items-center" style="z-index: 1000;">
            <div>
                <span class="fw-bold">Votre sélection : </span>
                <span id="count-selection" class="badge bg-secondary fs-6">0 / 3</span>
            </div>
            <button type="submit" id="btn-valider" class="btn btn-primary fw-bold px-5 rounded-pill" disabled>
                VALIDER LE VOTE <i class="fas fa-vote-yea ms-2"></i>
            </button>
        </div>
        @endif
    </form>
</div>

{{-- Petit script JS pour limiter à 3 choix --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.candidate-checkbox');
    const counter = document.getElementById('count-selection');
    const btn = document.getElementById('btn-valider');
    const max = 3;

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const checked = document.querySelectorAll('.candidate-checkbox:checked');
            
            // Mise à jour du compteur
            counter.innerText = checked.length + " / " + max;
            counter.className = checked.length === max ? "badge bg-success fs-6" : "badge bg-secondary fs-6";

            // Activation du bouton
            btn.disabled = checked.length !== max;

            // Bloquer les autres si 3 sélectionnés
            if (checked.length >= max) {
                checkboxes.forEach(box => {
                    if (!box.checked) box.disabled = true;
                });
            } else {
                checkboxes.forEach(box => {
                    box.disabled = false;
                });
            }
        });
    });
});
</script>

<style>
    /* Petit effet hover sur les cartes */
    .candidate-card { transition: transform 0.2s, box-shadow 0.2s; }
    .candidate-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>
@endsection