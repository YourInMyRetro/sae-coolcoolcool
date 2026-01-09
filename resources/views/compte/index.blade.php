@extends('layout')

@section('content')
<div class="account-page-wrapper" style="background-color: #f4f6f9; min-height: 100vh; padding: 50px 0;">
    <div class="container" style="max-width: 1200px; padding: 0 20px;">
        
        <div class="account-header" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="color: #326295; margin: 0; font-size: 2rem;">
                    <i class="fas fa-user-circle"></i> Mon Espace Personnel
                    <i class="fas fa-home help-trigger" 
                       data-title="C'est quoi cette page ?" 
                       data-content="C'est votre quartier général ! Ici, vous pouvez tout gérer : vos infos personnelles, vos commandes en cours et la sécurité de votre compte."
                       data-link="{{ route('aide') }}#section-compte"></i>
                </h1>
                <p style="color: #777; margin-top: 10px; margin-bottom: 0;">
                    Bienvenue, <strong>{{ $utilisateur->prenom }} {{ $utilisateur->nom }}</strong>
                    <span style="background: #e0f7fa; color: #006064; padding: 2px 8px; border-radius: 10px; font-size: 0.8em; margin-left: 10px;">
                        <i class="fas fa-check"></i> Compte Actif
                    </span>
                </p>
            </div>
            
            <div style="text-align: right;">
                 <a href="{{ route('home') }}" style="color: #666; text-decoration: none; font-size: 0.9em;">
                    <i class="fas fa-arrow-left"></i> Retour à l'accueil
                 </a>
            </div>
        </div>

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;">

            <div class="account-card" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-top: 4px solid #326295;">
                <h2 style="color: #326295; font-size: 1.4rem; margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                    <i class="far fa-id-card"></i> Mes Informations
                    <i class="fas fa-pen-square help-trigger" 
                       style="float: right; color: #326295;"
                       data-title="Modifier mes infos" 
                       data-content="Vous avez déménagé ? Vous avez changé de numéro ? C'est ici qu'il faut regarder. Cliquez sur 'Modifier mon profil' en bas pour mettre à jour."
                       data-link="{{ route('aide') }}#section-compte"></i>
                </h2>
                
                <ul style="list-style: none; padding: 0; line-height: 2; color: #555;">
                    <li>
                        <strong>Nom complet :</strong> {{ $utilisateur->prenom }} {{ $utilisateur->nom }}
                        <i class="fas fa-info-circle help-trigger" data-title="Identité" data-content="C'est le nom qui figurera sur vos colis."></i>
                    </li>
                    <li>
                        <strong>Email :</strong> {{ $utilisateur->mail }}
                        <i class="fas fa-envelope help-trigger" data-title="Contact" data-content="Votre identifiant de connexion et l'adresse où on envoie les factures."></i>
                    </li>
                    <li>
                        <strong>Téléphone :</strong> {{ $utilisateur->telephone ?? 'Non renseigné' }}
                        <i class="fas fa-phone help-trigger" data-title="SMS" data-content="Utile pour le livreur ou pour la sécurité 2FA."></i>
                    </li>
                    <li>
                        <strong>Pays :</strong> {{ $utilisateur->pays_naissance }}
                        <i class="fas fa-globe help-trigger" data-title="Zone" data-content="Détermine les frais de port par défaut."></i>
                    </li>
                </ul>

                <a href="{{ route('compte.edit') }}" class="btn-fifa-secondary" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #f0f4f8; color: #326295; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background 0.2s;">
                    <i class="fas fa-edit"></i> Modifier mon profil
                </a>
            </div>

            <div class="account-card" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-top: 4px solid #2ecc71;">
                <h2 style="color: #27ae60; font-size: 1.4rem; margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                    <i class="fas fa-box-open"></i> Mes Commandes
                    <i class="fas fa-truck help-trigger" 
                       style="float: right; color: #2ecc71;"
                       data-title="Suivi de colis" 
                       data-content="C'est ici que vous pouvez voir où en est votre maillot ! 'En préparation', 'Expédié' ou 'Livré'."></i>
                </h2>
                
                <p style="color: #666; font-size: 0.95rem; margin-bottom: 20px;">
                    Retrouvez l'historique de vos achats, téléchargez vos factures et suivez la livraison en temps réel.
                </p>

                <div style="background: #f1f8e9; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c5e1a5;">
                    <strong style="color: #33691e;"><i class="fas fa-lightbulb"></i> Le saviez-vous ?</strong>
                    <p style="font-size: 0.85rem; margin: 5px 0 0 0; color: #558b2f;">
                        Si un colis arrive abîmé, vous devez le signaler ici dans les 15 jours.
                        <i class="fas fa-exclamation-circle help-trigger" data-title="SAV" data-content="Un bouton 'Signaler un problème' apparaîtra sur la commande concernée une fois qu'elle sera livrée."></i>
                    </p>
                </div>

                <a href="{{ route('compte.commandes') }}" class="btn-fifa-primary" style="display: block; text-align: center; padding: 12px; background: #2ecc71; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Voir mes commandes <i class="fas fa-chevron-right"></i>
                </a>
            </div>

            <div class="account-card" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-top: 4px solid #e74c3c;">
                <h2 style="color: #c0392b; font-size: 1.4rem; margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                    <i class="fas fa-shield-alt"></i> Sécurité 2FA
                    <i class="fas fa-lock help-trigger" 
                       style="float: right; color: #e74c3c;"
                       data-title="C'est quoi la 2FA ?" 
                       data-content="La 'Double Authentification' est un super bouclier. Même si on vole votre mot de passe, on ne peut pas entrer sans le code envoyé sur votre téléphone."
                       data-link="{{ route('aide') }}#section-securite"></i>
                </h2>

                <ul style="list-style: none; padding: 0; margin-bottom: 20px;">
                    <li style="margin-bottom: 10px;">
                        <strong>Statut :</strong>
                        @if(Auth::user()->double_auth_active)
                            <span style="color: #27ae60; font-weight: bold; background: #e8f5e9; padding: 2px 8px; border-radius: 4px;">
                                <i class="fas fa-check-circle"></i> Protégé (Activé)
                            </span>
                        @else
                            <span style="color: #c0392b; font-weight: bold; background: #ffebee; padding: 2px 8px; border-radius: 4px;">
                                <i class="fas fa-times-circle"></i> Non protégé (Désactivé)
                            </span>
                            <i class="fas fa-exclamation-triangle help-trigger" data-title="Attention" data-content="Votre compte est vulnérable. Nous vous recommandons vivement de l'activer !"></i>
                        @endif
                    </li>
                    <li>
                        <strong>Méthode :</strong> Code par SMS/Email
                    </li>
                </ul>

                @if(session('verify_2fa'))
                    <div style="background: #e3f2fd; padding: 15px; border-radius: 5px; border: 1px solid #90caf9;">
                        <p style="color: #0d47a1; font-size: 0.9em; margin-bottom: 10px; font-weight: bold;">
                            <i class="fas fa-sms"></i> Code de vérification envoyé !
                            <i class="fas fa-question-circle help-trigger" data-title="Où est le code ?" data-content="Regardez vos SMS ou vos emails. C'est un code à 6 chiffres."></i>
                        </p>
                        <form action="{{ route('compte.2fa.verify') }}" method="POST" style="display: flex; gap: 10px;">
                            @csrf
                            <input type="text" name="code" placeholder="123456" maxlength="6" required 
                                   style="flex: 1; padding: 8px; border: 1px solid #bbb; border-radius: 4px; text-align: center; letter-spacing: 2px;">
                            <button type="submit" style="padding: 8px 15px; background: #2196f3; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                OK
                            </button>
                        </form>
                    </div>
                @else
                    @if(!Auth::user()->double_auth_active)
                        <form action="{{ route('compte.2fa.send') }}" method="POST">
                            @csrf
                            <button type="submit" style="width: 100%; padding: 12px; background: #326295; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
                                <i class="fas fa-lock"></i> Activer la sécurité maintenant
                            </button>
                        </form>
                    @else
                        <form action="{{ route('compte.2fa.disable') }}" method="POST">
                            @csrf
                            <button type="submit" style="width: 100%; padding: 12px; background: white; color: #c0392b; border: 1px solid #c0392b; border-radius: 5px; cursor: pointer;">
                                <i class="fas fa-unlock"></i> Désactiver (Déconseillé)
                            </button>
                        </form>
                    @endif
                @endif
            </div>

            @if($estPro)
            <div class="account-card" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-top: 4px solid #ffd700; grid-column: 1 / -1;">
                <h2 style="color: #f39c12; font-size: 1.4rem; margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                    <i class="fas fa-briefcase"></i> Espace Professionnel (Club / Asso)
                    <i class="fas fa-star help-trigger" 
                       style="float: right; color: #ffd700;"
                       data-title="Compte Pro" 
                       data-content="En tant que partenaire, vous avez accès à des commandes de gros et des demandes de personnalisation uniques."></i>
                </h2>
                
                <div style="display: flex; gap: 40px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 300px;">
                        <h4 style="margin-top: 0; color: #555;">Vos Infos Société</h4>
                        <ul style="color: #666; line-height: 1.8;">
                            <li><strong>Société :</strong> {{ $infosPro->nom_societe }}</li>
                            <li><strong>Activité :</strong> {{ $infosPro->activite }}</li>
                            <li><strong>TVA :</strong> {{ $infosPro->numero_tva_intracommunautaire }}</li>
                        </ul>
                    </div>

                    <div style="flex: 1; min-width: 300px;">
                        <h4 style="margin-top: 0; color: #555;">
                            Demandes Spéciales
                            <i class="fas fa-comments help-trigger" 
                               data-title="Sur mesure" 
                               data-content="Besoin de 50 maillots avec le logo de votre club ? Faites une demande ici. Le service commercial vous répondra."></i>
                        </h4>
                        
                        @if(count($mesDemandes) > 0)
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.9em;">
                                <thead style="background: #f9f9f9; color: #555;">
                                    <tr>
                                        <th style="padding: 8px; text-align: left;">Date</th>
                                        <th style="padding: 8px; text-align: left;">Sujet</th>
                                        <th style="padding: 8px; text-align: left;">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mesDemandes as $d)
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 8px;">{{ \Carbon\Carbon::parse($d->date_demande)->format('d/m/Y') }}</td>
                                        <td style="padding: 8px;">{{ $d->sujet }}</td>
                                        <td style="padding: 8px;">
                                            @if($d->statut == 'En attente')
                                                <span style="color: orange; font-weight: bold;">{{ $d->statut }}</span>
                                            @elseif($d->statut == 'Validée')
                                                <span style="color: green; font-weight: bold;">{{ $d->statut }}</span>
                                            @else
                                                <span style="color: red; font-weight: bold;">{{ $d->statut }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p style="color: #999; font-style: italic;">Aucune demande en cours.</p>
                        @endif

                        <a href="{{ route('compte.demande.create') }}" class="btn-fifa-secondary" style="display: inline-block; margin-top: 15px; padding: 8px 15px; border: 1px solid #326295; color: #326295; text-decoration: none; border-radius: 4px;">
                            <i class="fas fa-plus"></i> Nouvelle demande
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="account-card" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); grid-column: 1 / -1; text-align: center;">
                <h3 style="color: #555; font-size: 1.2rem; margin-top: 0;">Paramètres de session</h3>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="padding: 12px 30px; background: #555; color: white; border: none; border-radius: 50px; cursor: pointer; transition: background 0.2s;">
                        <i class="fas fa-sign-out-alt"></i> Se déconnecter
                    </button>
                </form>
                
                <div style="margin-top: 10px;">
                    <i class="fas fa-exclamation-triangle help-trigger" 
                       style="color: #e67e22;"
                       data-title="Important" 
                       data-content="Si vous êtes sur un ordinateur public (école, bibliothèque), n'oubliez JAMAIS de vous déconnecter avant de partir. Sinon, le prochain utilisateur aura accès à votre compte !"></i>
                    <span style="font-size: 0.85em; color: #999; margin-left: 5px;">Pourquoi se déconnecter ?</span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection