@extends('layout')

@section('content')
<div class="container-fifa">
    <h1>Modifier mon profil</h1>
    
    <form action="{{ route('compte.update') }}" method="POST" class="form-fifa">
        @csrf
        <div class="form-group">
            <label>Nom :</label>
            <input type="text" name="nom" value="{{ old('nom', $user->nom) }}" required>
        </div>

        <div class="form-group">
            <label>Prénom :</label>
            <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" required>
        </div>

        <div class="form-group">
            <label>Surnom :</label>
            <input type="text" name="surnom" value="{{ old('surnom', $user->surnom) }}">
        </div>

        <div class="form-group">
            <label>Langue :</label>
            <select name="langue">
                <option value="Français" {{ $user->langue == 'Français' ? 'selected' : '' }}>Français</option>
                <option value="Anglais" {{ $user->langue == 'Anglais' ? 'selected' : '' }}>Anglais</option>
                <option value="Espagnol" {{ $user->langue == 'Espagnol' ? 'selected' : '' }}>Espagnol</option>
                <option value="Allemand" {{ $user->langue == 'Allemand' ? 'selected' : '' }}>Allemand</option>
            </select>
        </div>

        @if($user->estProfessionnel())
        <div class="pro-edit-section" style="border-left: 4px solid #003366; padding-left: 15px; margin: 20px 0;">
            <h3>Modification Société</h3>
            <div class="form-group">
                <label>Nom Société :</label>
                <input type="text" name="nom_societe" value="{{ old('nom_societe', $user->professionel->nom_societe) }}" required>
            </div>
            <div class="form-group">
                <label>Activité :</label>
                <input type="text" name="activite" value="{{ old('activite', $user->professionel->activite) }}" required>
            </div>
            </div>
        @endif

        <div class="form-group">
            <label>Nouveau mot de passe (laisser vide pour ne pas changer) :</label>
            <input type="password" name="password">
        </div>

        <div class="form-group">
            <label>Confirmer le nouveau mot de passe :</label>
            <input type="password" name="password_confirmation">
        </div>

        <div class="actions">
            <button type="submit" class="btn-fifa-primary">Enregistrer les modifications</button>
            <a href="{{ route('compte.index') }}" class="btn-cancel">Annuler</a>
        </div>
    </form>
</div>
@endsection