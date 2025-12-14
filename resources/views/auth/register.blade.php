@extends('layout')

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-card-premium">
        
        <div class="auth-header">
            <h1>Créer un compte</h1>
            <p>Rejoignez la communauté FIFA et profitez d'avantages exclusifs.</p>
            <p style="font-size: 0.8em; color: #ff6b6b; margin-top: 5px;">* Champs obligatoires</p>
        </div>

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf
            
            <div style="display: flex; gap: 15px;">
                <div class="fifa-form-group" style="flex: 1;">
                    <label for="prenom">Prénom *</label>
                    <input type="text" 
                           name="prenom" 
                           id="prenom" 
                           class="fifa-input" 
                           {{-- LOGIQUE : Si erreur sur 'prenom', on vide. Sinon on remet l'ancienne valeur --}}
                           value="{{ $errors->has('prenom') ? '' : old('prenom') }}" 
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
                           value="{{ $errors->has('nom') ? '' : old('nom') }}" 
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
                       value="{{ $errors->has('surnom') ? '' : old('surnom') }}" 
                       maxlength="50"
                       placeholder="Kyks">
                @error('surnom') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="fifa-form-group" style="flex: 1;">
                    <label for="date_naissance">Date de naissance *</label>
                    <input type="date" 
                           name="date_naissance" 
                           id="date_naissance" 
                           class="fifa-input" 
                           value="{{ $errors->has('date_naissance') ? '' : old('date_naissance') }}" 
                           required
                           max="{{ date('Y-m-d') }}"
                           min="{{ date('Y-m-d', strtotime('-120 years')) }}">
                     @error('date_naissance') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                </div>
                <div class="fifa-form-group" style="flex: 1;">
                    <label for="langue">Langue *</label>
                    <select name="langue" id="langue" class="fifa-input" required>
                        <option value="Français" {{ old('langue') == 'Français' ? 'selected' : '' }}>Français</option>
                        <option value="Anglais" {{ old('langue') == 'Anglais' ? 'selected' : '' }}>Anglais</option>
                        <option value="Espagnol" {{ old('langue') == 'Espagnol' ? 'selected' : '' }}>Espagnol</option>
                        <option value="Allemand" {{ old('langue') == 'Allemand' ? 'selected' : '' }}>Allemand</option>
                    </select>
                </div>
            </div>

            <div class="fifa-form-group">
                <label for="pays_naissance">Pays de résidence *</label>
                <input type="text" 
                       name="pays_naissance" 
                       id="pays_naissance" 
                       class="fifa-input" 
                       value="{{ $errors->has('pays_naissance') ? '' : old('pays_naissance') }}" 
                       required 
                       minlength="3"
                       placeholder="France">
                @error('pays_naissance') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
            </div>

            {{-- MODIFICATION ICI : On groupe Email et Téléphone sur la même ligne --}}
            <div style="display: flex; gap: 15px;">
                <div class="fifa-form-group" style="flex: 1;">
                    <label for="mail">Adresse Email *</label>
                    <input type="email" 
                           name="mail" 
                           id="mail" 
                           class="fifa-input" 
                           value="{{ $errors->has('mail') ? '' : old('mail') }}" 
                           required 
                           pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                           placeholder="exemple@email.com">
                    @error('mail') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                </div>

                <div class="fifa-form-group" style="flex: 1;">
                    <label for="telephone">Téléphone mobile</label>
                    <input type="tel" 
                           name="telephone" 
                           id="telephone" 
                           class="fifa-input" 
                           value="{{ $errors->has('telephone') ? '' : old('telephone') }}" 
                           {{-- Pas 'required' strict pour éviter de bloquer les autres, mais conseillé --}}
                           minlength="10"
                           maxlength="20"
                           placeholder="06 12 34 56 78">
                    @error('telephone') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="fifa-form-group">
                <label for="password">Mot de passe *</label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="fifa-input" 
                       required 
                       minlength="8"
                       title="Le mot de passe doit contenir au moins 8 caractères."
                       placeholder="Minimum 8 caractères">
                @error('password') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
            </div>

            <div class="fifa-form-group">
                <label for="password_confirmation">Confirmer le mot de passe *</label>
                <input type="password" 
                       name="password_confirmation" 
                       id="password_confirmation" 
                       class="fifa-input" 
                       required 
                       minlength="8"
                       placeholder="Répétez le mot de passe">
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

            <div class="fifa-form-group" style="display: flex; align-items: flex-start; gap: 10px;">
                {{-- Checkbox : On garde coché si old('cgu_consent') est vrai --}}
                <input type="checkbox" name="cgu_consent" id="cgu_consent" required style="margin-top: 4px; width: auto;" {{ old('cgu_consent') ? 'checked' : '' }}>
                <label for="cgu_consent" style="font-size: 0.85rem; line-height: 1.4; color: #555; font-weight: normal;">
                    J'accepte les 
                    <a href="https://www.fifa.com/fr/legal/terms-of-service" target="_blank" rel="noopener noreferrer" style="text-decoration: underline; color: #00cfb7;">
                        Conditions Générales d'Utilisation
                    </a> 
                    et la 
                    <a href="{{ route('privacy') }}" target="_blank" style="text-decoration: underline; color: #00cfb7;">
                        Politique de Confidentialité
                    </a>. *
                </label>
            </div>
            @error('cgu_consent') <div style="color:red; font-size:0.8em; margin-bottom: 10px;">{{ $message }}</div> @enderror

            <div class="fifa-form-group" style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 25px;">
                <input type="checkbox" name="newsletter_optin" id="newsletter_optin" value="1" style="margin-top: 4px; width: auto;" {{ old('newsletter_optin') ? 'checked' : '' }}>
                <label for="newsletter_optin" style="font-size: 0.85rem; line-height: 1.4; color: #555; font-weight: normal;">
                    J'accepte de recevoir les offres et actualités de la FIFA par email.
                </label>
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