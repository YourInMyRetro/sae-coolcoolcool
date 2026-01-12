@extends('layout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
                    <div>
                        <h2 class="text-white fw-bold m-0">Sélection des Candidats</h2>
                        <p class="text-fifa-cyan m-0">Pour : {{ $competition->nom_competition }}</p>
                    </div>
                    <a href="{{ route('vente.votation.list') }}" class="btn btn-outline-light btn-sm">Retour</a>
                </div>

                <form action="{{ route('vente.votation.candidats.update', $competition->id_competition) }}" method="POST">
                    @csrf
                    
                    <div class="alert alert-info bg-dark border-secondary text-light">
                        <i class="fas fa-info-circle me-2"></i>Cochez les joueurs qui participent à ce vote.
                    </div>

                    <div class="row g-3 mb-4" style="max-height: 600px; overflow-y: auto;">
                        @foreach($tousLesJoueurs as $joueur)
                            <div class="col-md-4 col-sm-6">
                                <label class="player-checkbox-card d-flex align-items-center p-3 rounded border border-secondary h-100 position-relative">
                                    
                                    {{-- Checkbox cachée mais fonctionnelle --}}
                                    <input type="checkbox" name="joueurs[]" value="{{ $joueur->idjoueur }}" 
                                           class="form-check-input me-3" 
                                           {{ in_array($joueur->idjoueur, $selectedJoueurs) ? 'checked' : '' }}>
                                    
                                    <div class="d-flex align-items-center">
                                        {{-- Avatar (ou initiale si pas de photo) --}}
                                        <div class="avatar me-3 bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 45px; height: 45px; min-width: 45px;">
                                            {{ substr($joueur->prenom_joueur, 0, 1) }}{{ substr($joueur->nom_joueur, 0, 1) }}
                                        </div>
                                        
                                        <div class="overflow-hidden">
                                            <span class="text-white fw-bold d-block text-truncate">
                                                {{ $joueur->prenom_joueur }} {{ $joueur->nom_joueur }}
                                            </span>
                                            <small class="text-muted d-block text-truncate">
                                                {{ $joueur->club->nomclub ?? 'Sans club' }}
                                                @if($joueur->nombre_selection)
                                                 • {{ $joueur->nombre_selection }} sél.
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-end border-top border-secondary pt-3">
                        <button type="submit" class="btn btn-fifa-cyan fw-bold px-5 py-2 text-uppercase">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Style pour rendre les cases cliquables jolies */
    .player-checkbox-card {
        cursor: pointer;
        transition: all 0.2s;
        background-color: #1a202c;
    }
    .player-checkbox-card:hover {
        background-color: #2d3748;
    }
    
    /* Effet quand c'est coché : bordure cyan */
    .player-checkbox-card:has(input:checked) {
        border-color: #00cfb7 !important;
        background-color: rgba(0, 207, 183, 0.05);
    }

    .form-check-input:checked {
        background-color: #00cfb7;
        border-color: #00cfb7;
    }
    
    .text-fifa-cyan { color: #00cfb7; }
    .btn-fifa-cyan {
        background-color: #00cfb7;
        color: #0f1623;
        border: none;
    }
    .btn-fifa-cyan:hover {
        background-color: #00b39d; 
    }
</style>
@endsection