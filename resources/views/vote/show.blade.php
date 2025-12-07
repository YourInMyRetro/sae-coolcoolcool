@extends('layout')

@section('content')
<div class="container" style="padding: 50px 0;">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1>Votez pour : {{ $competition->nom_theme }}</h1>
        <a href="{{ route('vote.index') }}">← Retour aux votes</a>
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
        @forelse($joueurs as $joueur)
            <div class="card" style="width: 250px; border: 1px solid #eee; border-radius: 10px; overflow: hidden; background: white;">
                
                {{-- ICI : On ne regarde même plus la BDD. On force l'image direct. --}}
                <img src="{{ asset('img/produits/maillot-france-2025-authentic.jpg') }}" 
                     alt="{{ $joueur->nom_joueur }}"
                     style="width: 100%; height: 250px; object-fit: cover;">
                     
                <div style="padding: 15px; text-align: center;">
                    <h4 style="margin: 0;">{{ $joueur->prenom_joueur }} {{ $joueur->nom_joueur }}</h4>
                    <p style="color: #666; font-size: 0.9em;">
                        {{ $joueur->nom_affichage ?? 'Candidat officiel' }}
                    </p>
                    
                    <button class="btn-fifa-cta" style="width: 100%; margin-top: 10px; cursor: pointer;">
                        Voter
                    </button>
                </div>
            </div>
        @empty
            <p>Aucun candidat listé pour ce vote pour le moment.</p>
        @endforelse
    </div>
</div>
@endsection