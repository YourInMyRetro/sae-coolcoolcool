@extends('layout')

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-card-premium is-wide">
        
        <div class="auth-header">
            <h1>Compte FIFA Professionnel <span class="badge-pro-title">B2B</span></h1>
            <p>Accédez aux tarifs revendeurs et aux commandes en gros.</p>
        </div>

        <form action="{{ route('register.pro.submit') }}" method="POST">
                @if ($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <strong>Oups ! Il y a des problèmes :</strong>
                <ul style="margin-top: 10px; margin-bottom: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
                    @endif
            @csrf
            
            <div class="pro-grid">
                
                <div class="left-col">
                    <h3 style="color: #326295; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                        <i class="fas fa-user-tie"></i> Responsable du compte
                    </h3>

                    <div style="display: flex; gap: 15px;">
                        <div class="fifa-form-group" style="flex: 1;">
                            <label>Prénom *</label>
                            <input type="text" name="prenom" class="fifa-input" value="{{ old('prenom') }}" 
                                   required minlength="2" maxlength="50" title="Minimum 2 caractères">
                        </div>
                        <div class="fifa-form-group" style="flex: 1;">
                            <label>Nom *</label>
                            <input type="text" name="nom" class="fifa-input" value="{{ old('nom') }}" 
                                   required minlength="2" maxlength="50" title="Minimum 2 caractères">
                        </div>
                    </div>

                    <div class="fifa-form-group">
                        <label>Email Professionnel *</label>
                        {{-- Validation stricte de l'email --}}
                        <input type="email" name="mail" class="fifa-input" value="{{ old('mail') }}" 
                               required 
                               pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                               title="Format requis : nom@entreprise.com (doit contenir @ et .)"
                               placeholder="nom@entreprise.com">
                        @error('mail') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <div class="fifa-form-group" style="flex: 1;">
                            <label>Date de naissance *</label>
                            <input type="date" name="date_naissance" class="fifa-input" value="{{ old('date_naissance') }}" required>
                        </div>
                        <div class="fifa-form-group" style="flex: 1;">
                            <label>Langue *</label>
                            <select name="langue" class="fifa-input" required>
                                <option value="Français">Français</option>
                                <option value="Anglais">Anglais</option>
                            </select>
                        </div>
                    </div>

                    <div class="fifa-form-group">
                        <label>Pays de résidence *</label>
                        <input type="text" name="pays_naissance" class="fifa-input" value="{{ old('pays_naissance') }}" 
                               required minlength="3">
                    </div>

                    <div class="fifa-form-group">
                        <label>Mot de passe *</label>
                        <input type="password" name="password" class="fifa-input" 
                               required minlength="4" title="Minimum 4 caractères">
                    </div>
                    <div class="fifa-form-group">
                        <label>Confirmation *</label>
                        <input type="password" name="password_confirmation" class="fifa-input" 
                               required minlength="4">
                    </div>
                </div>

                <div class="right-col" style="background-color: #f8faff; padding: 20px; border-radius: 8px; border: 1px dashed #cce0ff;">
                    <h3 style="color: #003366; border-bottom: 1px solid #cce0ff; padding-bottom: 10px; margin-bottom: 20px;">
                        <i class="fas fa-building"></i> Informations Société
                    </h3>

                    <div class="fifa-form-group">
                        <label for="nom_societe">Raison Sociale *</label>
                        <input type="text" name="nom_societe" id="nom_societe" class="fifa-input" value="{{ old('nom_societe') }}" 
                               required minlength="2" maxlength="100" 
                               placeholder="Ex: FIFA Store Paris SAS">
                        @error('nom_societe') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                    </div>

                    <div class="fifa-form-group">
                        <label for="activite">Secteur d'activité *</label>
                        <select name="activite" id="activite" class="fifa-input" required>
                            <option value="">-- Choisir --</option>
                            <option value="Revendeur Sport">Revendeur Sport</option>
                            <option value="Club de Football">Club de Football</option>
                            <option value="Evènementiel">Evènementiel</option>
                            <option value="Autre">Autre</option>
                        </select>
                        @error('activite') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                    </div>

                    <div class="fifa-form-group">
                        <label for="numero_tva">Numéro de TVA Intracommunautaire *</label>
                        {{-- Validation basique pour TVA (au moins 5 caractères) --}}
                        <input type="text" name="numero_tva" id="numero_tva" class="fifa-input" value="{{ old('numero_tva') }}" 
                               required minlength="5" maxlength="30"
                               placeholder="FRXX 999999999">
                        @error('numero_tva') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                    </div>

                    <div style="margin-top: 30px; font-size: 0.85em; color: #555; background: #fff; padding: 10px; border-radius: 4px;">
                        <i class="fas fa-info-circle" style="color: #00d4ff;"></i> 
                        En créant un compte professionnel, vous certifiez agir au nom de la société mentionnée ci-dessus. Votre numéro de TVA sera vérifié avant validation définitive.
                    </div>
                </div>
                <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                
                {{-- Case CGU --}}
                <div class="fifa-form-group" style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px;">
                    <input type="checkbox" name="cgu_consent" id="cgu_consent_pro" required style="width: 18px; height: 18px; margin-top: 3px;">
                    <label for="cgu_consent_pro" style="font-size: 0.9em; color: #555; text-transform: none; font-weight: normal;">
                        J'accepte les <a href="{{ route('cgu') }}" target="_blank" style="color: #00d4ff; text-decoration: underline;">Conditions Générales d'Utilisation</a> 
                        et la <a href="{{ route('privacy') }}" target="_blank" style="color: #00d4ff; text-decoration: underline;">Politique de Confidentialité</a>. *
                    </label>
                </div>
                @error('cgu_consent') 
                    <span style="color:red; font-size:0.85em; display:block; margin-left: 30px;">{{ $message }}</span> 
                @enderror

                {{-- Case Newsletter (Optionnelle mais conseillée) --}}
                <div class="fifa-form-group" style="display: flex; align-items: flex-start; gap: 10px;">
                    <input type="checkbox" name="newsletter_optin" id="newsletter_optin_pro" value="1" style="width: 18px; height: 18px; margin-top: 3px;">
                    <label for="newsletter_optin_pro" style="font-size: 0.9em; color: #555; text-transform: none; font-weight: normal;">
                        J'accepte de recevoir les offres B2B et actualités de la FIFA par email.
                    </label>
                </div>

            </div>

            </div> <button type="submit" class="btn-fifa-submit pro-style">
                Valider ma demande PRO <i class="fas fa-check-circle" style="margin-left: 8px;"></i>
            </button>
        </form>

        <div class="auth-footer-links">
            <a href="{{ route('register.form') }}">Retour à l'inscription Particulier</a>
        </div>
    </div>
</div>
@endsection