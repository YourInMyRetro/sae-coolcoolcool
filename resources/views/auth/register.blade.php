@extends('layout')

@section('content')
<div class="auth-page-wrapper" style="padding: 50px 0; background: #f4f6f9;">
    <div class="auth-card-premium" style="max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
        
        <div class="auth-header" style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #326295; font-size: 2rem; margin-bottom: 10px;">
                Créer un compte
                <i class="fas fa-user-plus help-trigger" 
                   data-title="Pourquoi s'inscrire ?" 
                   data-content="Créer un compte vous permet de passer commande, de suivre vos livraisons et de voter pour les trophées FIFA. C'est gratuit et ça prend 2 minutes."
                   data-link="{{ route('aide') }}#section-compte"></i>
            </h1>
            <p style="color: #666;">Rejoignez la communauté FIFA et profitez d'avantages exclusifs.</p>
            <p style="font-size: 0.8em; color: #e74c3c; margin-top: 5px;">* Champs obligatoires</p>
        </div>

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf
            
            <div style="background: #f8faff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e1e8ed;">
                <h4 style="margin-top: 0; color: #326295; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 15px;">
                    <i class="far fa-id-card"></i> Votre Identité
                </h4>

                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="fifa-form-group" style="flex: 1; min-width: 250px;">
                        <label for="prenom" style="font-weight: bold; display: block; margin-bottom: 5px;">
                            Prénom *
                            <i class="fas fa-info-circle help-trigger" 
                               data-title="Vrai Prénom" 
                               data-content="Utilisez votre vrai prénom (pas de pseudo ici) pour que le facteur puisse vous livrer."></i>
                        </label>
                        <input type="text" 
                               name="prenom" 
                               id="prenom" 
                               class="fifa-input" 
                               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"
                               value="{{ $errors->has('prenom') ? '' : old('prenom') }}" 
                               required 
                               minlength="2" 
                               maxlength="50"
                               placeholder="Ex: Kylian">
                        @error('prenom') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                    </div>
                    <div class="fifa-form-group" style="flex: 1; min-width: 250px;">
                        <label for="nom" style="font-weight: bold; display: block; margin-bottom: 5px;">
                            Nom *
                            <i class="fas fa-info-circle help-trigger" 
                               data-title="Vrai Nom" 
                               data-content="Indispensable pour la facturation et la livraison."></i>
                        </label>
                        <input type="text" 
                               name="nom" 
                               id="nom" 
                               class="fifa-input" 
                               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"
                               value="{{ $errors->has('nom') ? '' : old('nom') }}" 
                               required 
                               minlength="2" 
                               maxlength="50"
                               placeholder="Ex: Mbappé">
                        @error('nom') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="fifa-form-group" style="margin-top: 15px;">
                    <label for="surnom" style="font-weight: bold; display: block; margin-bottom: 5px;">
                        Surnom <span style="font-weight:normal; color:#999;">(Optionnel)</span>
                        <i class="fas fa-question-circle help-trigger" 
                           data-title="Pseudo" 
                           data-content="C'est ce nom qui s'affichera si vous laissez un commentaire sur le blog. Si vous n'en mettez pas, on utilisera votre Prénom."></i>
                    </label>
                    <input type="text" 
                           name="surnom" 
                           id="surnom" 
                           class="fifa-input" 
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"
                           value="{{ $errors->has('surnom') ? '' : old('surnom') }}" 
                           maxlength="50"
                           placeholder="Ex: Kyks du 93">
                    @error('surnom') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                </div>

                <div style="display: flex; gap: 20px; margin-top: 15px; flex-wrap: wrap;">
                    <div class="fifa-form-group" style="flex: 1; min-width: 250px;">
                        <label for="date_naissance" style="font-weight: bold; display: block; margin-bottom: 5px;">
                            Date de naissance *
                            <i class="fas fa-birthday-cake help-trigger" 
                               data-title="Âge requis" 
                               data-content="Vous devez avoir au moins 15 ans pour créer un compte. Cette date ne sera pas affichée publiquement."></i>
                        </label>
                        <input type="date" 
                               name="date_naissance" 
                               id="date_naissance" 
                               class="fifa-input" 
                               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"
                               value="{{ $errors->has('date_naissance') ? '' : old('date_naissance') }}" 
                               required
                               max="{{ date('Y-m-d') }}">
                         @error('date_naissance') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                    </div>
                    <div class="fifa-form-group" style="flex: 1; min-width: 250px;">
                        <label for="langue" style="font-weight: bold; display: block; margin-bottom: 5px;">
                            Langue préférée *
                            <i class="fas fa-language help-trigger" 
                               data-title="Communication" 
                               data-content="Dans quelle langue souhaitez-vous recevoir nos emails ?"></i>
                        </label>
                        <select name="langue" id="langue" class="fifa-input" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" required>
                            <option value="Français" {{ old('langue') == 'Français' ? 'selected' : '' }}>Français</option>
                            <option value="Anglais" {{ old('langue') == 'Anglais' ? 'selected' : '' }}>Anglais</option>
                            <option value="Espagnol" {{ old('langue') == 'Espagnol' ? 'selected' : '' }}>Espagnol</option>
                            <option value="Allemand" {{ old('langue') == 'Allemand' ? 'selected' : '' }}>Allemand</option>
                        </select>
                    </div>
                </div>

                <div class="fifa-form-group" style="margin-top: 15px;">
                    <label for="pays_naissance" style="font-weight: bold; display: block; margin-bottom: 5px;">
                        Pays de résidence *
                        <i class="fas fa-globe help-trigger" 
                           data-title="Livraison" 
                           data-content="Important pour calculer les frais de port et la TVA correcte."></i>
                    </label>
                    <input type="text" 
                           name="pays_naissance" 
                           id="pays_naissance" 
                           class="fifa-input" 
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"
                           value="{{ $errors->has('pays_naissance') ? '' : old('pays_naissance') }}" 
                           required 
                           minlength="3"
                           placeholder="Ex: France">
                    @error('pays_naissance') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="background: #fff8e1; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffe0b2;">
                <h4 style="margin-top: 0; color: #f57c00; border-bottom: 1px solid #ffe0b2; padding-bottom: 10px; margin-bottom: 15px;">
                    <i class="fas fa-lock"></i> Connexion & Sécurité
                </h4>

                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="fifa-form-group" style="flex: 1; min-width: 250px;">
                        <label for="mail" style="font-weight: bold; display: block; margin-bottom: 5px;">
                            Adresse Email *
                            <i class="fas fa-at help-trigger" 
                               data-title="Identifiant de connexion" 
                               data-content="C'est ce qui vous servira de login. Nous vous enverrons aussi votre facture ici. Pas de spam inutile !"></i>
                        </label>
                        <input type="email" 
                               name="mail" 
                               id="mail" 
                               class="fifa-input" 
                               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"
                               value="{{ $errors->has('mail') ? '' : old('mail') }}" 
                               required 
                               placeholder="exemple@email.com">
                        @error('mail') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                    </div>

                    <div class="fifa-form-group" style="flex: 1; min-width: 250px;">
                        <label for="telephone" style="font-weight: bold; display: block; margin-bottom: 5px;">
                            Téléphone mobile
                            <i class="fas fa-mobile-alt help-trigger" 
                               data-title="Suivi SMS" 
                               data-content="Recommandé pour recevoir un SMS quand le livreur arrive chez vous. Utile aussi pour la sécurité (2FA)."></i>
                        </label>
                        <input type="tel" 
                               name="telephone" 
                               id="telephone" 
                               class="fifa-input" 
                               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"
                               value="{{ $errors->has('telephone') ? '' : old('telephone') }}" 
                               placeholder="06 12 34 56 78">
                        @error('telephone') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="fifa-form-group" style="margin-top: 15px;">
                    <label for="password" style="font-weight: bold; display: block; margin-bottom: 5px;">
                        Mot de passe *
                        <i class="fas fa-key help-trigger" 
                           style="color: #e74c3c;"
                           data-title="Sécurité critique" 
                           data-content="Choisissez un code compliqué ! Au moins 8 caractères. Mélangez des majuscules (A-Z) et des chiffres (0-9). Ne mettez pas votre date de naissance !"></i>
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           class="fifa-input" 
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"
                           required 
                           minlength="8"
                           placeholder="Minimum 8 caractères">
                    @error('password') <span style="color:red; font-size:0.8em;">{{ $message }}</span> @enderror
                </div>

                <div class="fifa-form-group" style="margin-top: 15px;">
                    <label for="password_confirmation" style="font-weight: bold; display: block; margin-bottom: 5px;">
                        Confirmer le mot de passe *
                        <i class="fas fa-check-double help-trigger" 
                           data-title="Vérification" 
                           data-content="Retapez exactement la même chose pour être sûr qu'il n'y a pas de faute de frappe."></i>
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           class="fifa-input" 
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"
                           required 
                           minlength="8"
                           placeholder="Répétez le mot de passe">
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">

            <div class="fifa-form-group" style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 15px;">
                <input type="checkbox" name="cgu_consent" id="cgu_consent" required style="margin-top: 5px; transform: scale(1.2);" {{ old('cgu_consent') ? 'checked' : '' }}>
                <label for="cgu_consent" style="font-size: 0.9rem; line-height: 1.5; color: #555; font-weight: normal; cursor: pointer;">
                    J'accepte les 
                    <a href="#" target="_blank" style="text-decoration: underline; color: #326295;">Conditions Générales</a> 
                    et la Politique de Confidentialité. *
                    <i class="fas fa-file-contract help-trigger" 
                       data-title="C'est quoi ça ?" 
                       data-content="C'est le contrat légal entre vous et nous. En gros : vous acceptez de ne pas pirater le site, et nous nous engageons à protéger vos données."
                       data-link="{{ route('aide') }}#section-securite"></i>
                </label>
            </div>
            @error('cgu_consent') <div style="color:red; font-size:0.8em; margin-bottom: 10px; margin-left: 25px;">{{ $message }}</div> @enderror

            <div class="fifa-form-group" style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 30px;">
                <input type="checkbox" name="newsletter_optin" id="newsletter_optin" value="1" style="margin-top: 5px; transform: scale(1.2);" {{ old('newsletter_optin') ? 'checked' : '' }}>
                <label for="newsletter_optin" style="font-size: 0.9rem; line-height: 1.5; color: #555; font-weight: normal; cursor: pointer;">
                    Je souhaite recevoir les actualités et offres spéciales FIFA par email.
                    <i class="fas fa-envelope-open-text help-trigger" 
                       data-title="Newsletter" 
                       data-content="Si vous cochez, on vous enverra un email par semaine max avec les promos sur les maillots."></i>
                </label>
            </div>

            <div style="text-align: center;">
                <button type="submit" class="btn-fifa-submit" 
                        style="background: #00cfb7; color: #003366; border: none; padding: 15px 40px; font-size: 1.2rem; font-weight: bold; border-radius: 50px; cursor: pointer; transition: transform 0.2s; box-shadow: 0 4px 10px rgba(0, 207, 183, 0.4);">
                    S'inscrire maintenant <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                </button>
                <div style="margin-top: 10px;">
                    <i class="fas fa-mouse-pointer help-trigger" 
                       style="font-size: 1.5em; color: #00cfb7;"
                       data-title="Terminé ?" 
                       data-content="Cliquez ici pour valider le formulaire. Si ça ne marche pas, vérifiez qu'il n'y a pas de message d'erreur rouge quelque part."></i>
                </div>
            </div>

        </form>

        <div class="auth-footer-links" style="text-align: center; margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px;">
            <p>Déjà inscrit ? <a href="{{ route('login') }}" style="color: #326295; font-weight: bold;">Se connecter</a></p>
            <p><a href="{{ route('register.pro.form') }}" style="color: #888; font-size: 0.9em; text-decoration: underline;">Vous êtes une entreprise (Club/Asso) ? Créer un compte PRO</a></p>
        </div>
    </div>
</div>
@endsection