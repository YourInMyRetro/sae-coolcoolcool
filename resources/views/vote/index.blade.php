@extends('layout')

@section('content')
<div class="container" style="padding: 50px 0; text-align: center;">
    <h1>Espace de Vote FIFA</h1>
    <p>Sélectionnez un événement pour voter pour vos candidats préférés.</p>

    <div style="display: flex; justify-content: center; gap: 30px; margin-top: 40px; flex-wrap: wrap;">
        {{-- Remplacement de $competitions par $votes --}}
        @foreach($votes as $vote) 
            <div class="card" style="width: 300px; padding: 20px; border: 1px solid #ddd; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <h3>{{ $vote->nom_theme }}</h3>
                <p>Clôture le : {{ $vote->date_fermeture ?? 'Non définie' }}</p>

                @auth
                    {{-- Attention : bien utiliser la variable $vote ici aussi --}}
                    <a href="{{ route('vote.show', $vote->idtheme) }}" class="btn-fifa-cta" style="display: block; margin-top: 15px; text-decoration: none;">
                        Voir les candidats
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-fifa-cta" style="display: block; margin-top: 15px; text-decoration: none; background-color: #7f8c8d; cursor: not-allowed;">
                        <i class="fas fa-lock"></i> Se connecter pour voter
                    </a>
                @endauth
            </div>
        @endforeach
    </div>
</div>
@endsection