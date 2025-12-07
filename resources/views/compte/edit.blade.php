@extends('layout')

@section('content')
<div class="account-page-wrapper">
    <div class="account-container" style="max-width: 800px;">
        
        <div class="account-header">
            <div>
                <h1>Modifier mon profil</h1>
                <p style="color: #888;">Mettez à jour vos informations personnelles.</p>
            </div>
            <a href="{{ route('compte.index') }}" class="btn-cancel-dark">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <div class="account-card">
            
            <form action="{{ route('compte.update') }}" method="POST">
                @csrf
                
                {{-- SECTION IDENTITÉ --}}
                <h3 style="color: #fff; border-bottom: 1px solid #444; padding-bottom: 10px; margin-bottom: 20px;">
                    <i class="far fa-user"></i> Informations Générales
                </h3>

                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="fifa-form-group" style="flex: 1; min-width: 300px;">
                        <label>Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" class="fifa-input" required>
                        @error('prenom') <span style="color: #ff6b6b; font-size: 0.85em;">{{ $message }}</span> @enderror
                    </div>

                    <div class="fifa-form-group" style="flex: 1; min-width: 300px;">
                        <label>Nom</label>
                        <input type="text" name="nom" value="{{ old('nom', $user->nom) }}" class="fifa-input" required>
                        @error('nom') <span style="color: #ff6b6b; font-size: 0.85em;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="fifa-form-group" style="flex: 1;">
                        <label>Surnom</label>
                        <input type="text" name="surnom" value="{{ old('surnom', $user->surnom) }}" class="fifa-input">
                    </div>
                    <div class="fifa-form-group" style="flex: 1;">
                        <label>Langue</label>
                        <select name="langue" class="fifa-input">
                            <option value="Français" {{ $user->langue == 'Français' ? 'selected' : '' }}>Français</option>
                            <option value="Anglais" {{ $user->langue == 'Anglais' ? 'selected' : '' }}>Anglais</option>
                            <option value="Espagnol" {{ $user->langue == 'Espagnol' ? 'selected' : '' }}>Espagnol</option>
                            <option value="Allemand" {{ $user->langue == 'Allemand' ? 'selected' : '' }}>Allemand</option>
                        </select>
                    </div>
                </div>

                {{-- EMAIL (Lecture seule pour sécurité) --}}
                <div class="fifa-form-group">
                    <label>Adresse Email <small>(Non modifiable)</small></label>
                    <input type="email" value="{{ $user->mail }}" class="fifa-input" disabled>
                </div>

                {{-- SECTION PROFESSIONNELLE (CONDITIONNELLE) --}}
                @if($user->estProfessionnel())
                <div class="pro-edit-section">
                    <h3><i class="fas fa-building"></i> Informations Société</h3>
                    <p style="color: #ccc; font-size: 0.9em; margin-bottom: 15px;">Espace réservé aux comptes B2B.</p>

                    <div class="fifa-form-group">
                        <label>Raison Sociale</label>
                        <input type="text" name="nom_societe" value="{{ old('nom_societe', $user->professionel->nom_societe) }}" class="fifa-input" required>
                    </div>

                    <div class="fifa-form-group">
                        <label>Secteur d'activité</label>
                        <input type="text" name="activite" value="{{ old('activite', $user->professionel->activite) }}" class="fifa-input" required>
                    </div>

                    <div class="fifa-form-group">
                        <label>N° TVA Intracommunautaire <small>(Contactez le support pour modifier)</small></label>
                        <input type="text" value="{{ $user->professionel->numero_tva_intracommunautaire }}" class="fifa-input" disabled>
                    </div>
                </div>
                @endif

                {{-- SECTION SÉCURITÉ --}}
                <h3 style="color: #fff; border-bottom: 1px solid #444; padding-bottom: 10px; margin: 30px 0 20px 0;">
                    <i class="fas fa-lock"></i> Sécurité
                </h3>
                
                <div class="fifa-form-group">
                    <label>Nouveau mot de passe <small>(Laisser vide pour ne pas changer)</small></label>
                    <input type="password" name="password" class="fifa-input" placeholder="••••••••">
                    @error('password') <span style="color: #ff6b6b; font-size: 0.85em;">{{ $message }}</span> @enderror
                </div>

                <div class="fifa-form-group">
                    <label>Confirmer le nouveau mot de passe</label>
                    <input type="password" name="password_confirmation" class="fifa-input" placeholder="••••••••">
                </div>

                {{-- ACTIONS --}}
                <div style="margin-top: 40px; border-top: 1px solid #333; padding-top: 20px;">
                    <button type="submit" class="btn-save-changes">
                        Enregistrer les modifications
                    </button>
                    <a href="{{ route('compte.index') }}" class="btn-cancel-dark">Annuler</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection