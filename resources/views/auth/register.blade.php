@extends('layout')

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-card-premium">
        
        <div class="auth-header">
            <h1>Créer un compte</h1>
            <p>Rejoignez la communauté FIFA et profitez d'avantages exclusifs.</p>
        </div>

        {{-- Le navigateur fera la validation native grâce aux attributs ajoutés --}}
        <form action="{{ route('register.submit') }}" method="POST">
            @csrf
            
            <div style="display: flex; gap: 15px;">
                <div class="fifa-form-group" style="flex: 1;">
                    <label for="prenom">Prénom *</label>
                    <input type="text" 
                           name="prenom" 
                           id="prenom" 
                           class="fifa-input" 
                           value="{{ old('prenom') }}" 
                           required 
                           minlength="2" 
                           maxlength="50"
                           title="Le prénom doit contenir au moins 2 caractères."
                           placeholder="Kylian">
                    @error('prenom') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                </div>
                <div class="fifa-form-group" style="flex: 1;">
                    <label for="nom">Nom *</label>
                    <input type="text" 
                           name="nom" 
                           id="nom" 
                           class="fifa-input" 
                           value="{{ old('nom') }}" 
                           required 
                           minlength="2" 
                           maxlength="50"
                           title="Le nom doit contenir au moins 2 caractères."
                           placeholder="Mbappé">
                    @error('nom') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="fifa-form-group">
                <label for="surnom">Surnom <span style="font-weight:normal; color:#999; text-transform:none;">(Optionnel)</span></label>
                <input type="text" 
                       name="surnom" 
                       id="surnom" 
                       class="fifa-input" 
                       value="{{ old('surnom') }}" 
                       maxlength="50"
                       placeholder="Kyks">
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="fifa-form-group" style="flex: 1;">
                    <label for="date_naissance">Date de naissance *</label>
                    <input type="date" 
                           name="date_naissance" 
                           id="date_naissance" 
                           class="fifa-input" 
                           value="{{ old('date_naissance') }}" 
                           required>
                </div>
                <div class="fifa-form-group" style="flex: 1;">
                    <label for="langue">Langue *</label>
                    <select name="langue" id="langue" class="fifa-input" required>
                        <option value="Français">Français</option>
                        <option value="Anglais">Anglais</option>
                        <option value="Espagnol">Espagnol</option>
                        <option value="Allemand">Allemand</option>
                    </select>
                </div>
            </div>

            <div class="fifa-form-group">
                <label for="pays_naissance">Pays de résidence *</label>
                <input type="text" 
                       name="pays_naissance" 
                       id="pays_naissance" 
                       class="fifa-input" 
                       value="{{ old('pays_naissance') }}" 
                       required 
                       minlength="3"
                       placeholder="France">
            </div>

            <div class="fifa-form-group">
                <label for="mail">Adresse Email *</label>
                {{-- PATTERN : Force le format quelquechose@domaine.extension --}}
                <input type="email" 
                       name="mail" 
                       id="mail" 
                       class="fifa-input" 
                       value="{{ old('mail') }}" 
                       required 
                       pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                       title="Veuillez entrer une adresse email valide contenant un '@' et un point '.' (ex: nom@domaine.com)"
                       placeholder="exemple@email.com">
                @error('mail') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
            </div>

            <div class="fifa-form-group">
                <label for="password">Mot de passe *</label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="fifa-input" 
                       required 
                       minlength="4"
                       title="Le mot de passe doit contenir au moins 4 caractères."
                       placeholder="Minimum 4 caractères">
                @error('password') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
            </div>

            <div class="fifa-form-group">
                <label for="password_confirmation">Confirmer le mot de passe *</label>
                <input type="password" 
                       name="password_confirmation" 
                       id="password_confirmation" 
                       class="fifa-input" 
                       required 
                       minlength="4"
                       placeholder="Répétez le mot de passe">
            </div>

            <button type="submit" class="btn-fifa-submit">
                S'inscrire <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </form>

        <div class="auth-footer-links">
            Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a>
            <br><br>
            <a href="{{ route('register.pro.form') }}" style="color: #666; font-size: 0.85em;">Vous êtes une entreprise ? Créer un compte PRO</a>
        </div>
    </div>
</div>
@endsection