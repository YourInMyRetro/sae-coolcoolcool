@extends('layout')

@section('content')
<div class="faq-page-wrapper" style="background-color: #f4f6f9; min-height: 100vh; padding-bottom: 60px;">
    
    <div style="background: linear-gradient(135deg, #326295 0%, #003366 100%); padding: 60px 20px; text-align: center; color: white; margin-bottom: 40px;">
        <h1 style="font-size: 3rem; margin-bottom: 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px;">Centre d'Aide FIFA</h1>
        <p style="font-size: 1.2rem; max-width: 800px; margin: 0 auto; opacity: 0.9; line-height: 1.6;">
            Bienvenue dans notre base de connaissances. <br>
            Nous avons détaillé ici chaque fonctionnalité du site pour vous accompagner étape par étape.
        </p>
    </div>

    <div class="container" style="max-width: 1200px; margin: 0 auto; display: flex; gap: 40px; align-items: flex-start; padding: 0 20px;">

        <nav style="width: 300px; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); position: sticky; top: 100px; overflow: hidden; flex-shrink: 0; border: 1px solid #e1e8ed;">
            <div style="background: #00cfb7; padding: 15px 20px; font-weight: bold; color: #003366; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem;">
                <i class="fas fa-list-ul" style="margin-right: 10px;"></i> Sommaire
            </div>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li><a href="#section-general" class="faq-nav-link">1. Premiers Pas & Concept</a></li>
                <li><a href="#section-compte" class="faq-nav-link">2. Mon Compte & Accès</a></li>
                <li><a href="#section-boutique" class="faq-nav-link">3. Produits & Stocks</a></li>
                <li><a href="#section-panier" class="faq-nav-link">4. Panier & Commande</a></li>
                <li><a href="#section-paiement" class="faq-nav-link">5. Paiement Sécurisé</a></li>
                <li><a href="#section-livraison" class="faq-nav-link">6. Livraison & Suivi</a></li>
                <li><a href="#section-sav" class="faq-nav-link">7. Retours & SAV</a></li>
                <li><a href="#section-votes" class="faq-nav-link">8. Votes "The Best"</a></li>
                <li><a href="#section-securite" class="faq-nav-link">9. Sécurité & 2FA</a></li>
            </ul>
        </nav>

        <div style="flex-grow: 1;">

            <section id="section-general" class="faq-section">
                <h2 class="faq-title"><i class="fas fa-flag"></i> 1. Premiers Pas sur le site</h2>
                
                <div class="faq-card">
                    <h3>À quoi sert ce site ?</h3>
                    <p>Ce site est la plateforme officielle FIFA Store. Il regroupe trois fonctionnalités majeures :</p>
                    <ol>
                        <li><strong>La Boutique :</strong> Acheter des produits officiels (maillots, ballons, accessoires, etc.).</li>
                        <li><strong>Les Votes :</strong> Participer à l'élection des trophées "The Best" (Joueur de l'année, etc.).</li>
                        <li><strong>L'Actualité :</strong> Suivre les dernières nouvelles du football mondial via notre Blog.</li>
                    </ol>
                </div>

                <div class="faq-card">
                    <h3>Je suis perdu, comment trouver de l'aide ?</h3>
                    <p>Nous avons disséminé des <strong>bulles d'aide interactives</strong> partout sur le site. Cherchez simplement les petites icônes <i class="fas fa-question-circle" style="color:#00cfb7;"></i> ou <i class="fas fa-info-circle" style="color:#00cfb7;"></i>. En cliquant dessus, une explication apparaîtra pour vous guider à l'endroit précis où vous vous trouvez.</p>
                </div>
            </section>

            <section id="section-compte" class="faq-section">
                <h2 class="faq-title"><i class="fas fa-user-circle"></i> 2. Mon Compte & Accès</h2>

                <div class="faq-card">
                    <h3>Est-il obligatoire de créer un compte ?</h3>
                    <p>La navigation sur le site est libre. Cependant, la création d'un compte est <strong>obligatoire</strong> pour :</p>
                    <ul>
                        <li>Valider une commande (nous avons besoin de vos coordonnées pour la livraison).</li>
                        <li>Participer aux votes (pour garantir l'unicité du vote).</li>
                        <li>Accéder à l'historique de vos achats.</li>
                    </ul>
                </div>

                <div class="faq-card">
                    <h3>Comment s'inscrire ?</h3>
                    <p>Cliquez sur l'icône <i class="far fa-user"></i> en haut à droite, puis sur "Créer un compte". Remplissez le formulaire avec des informations exactes (Nom, Prénom, Email). Un email de confirmation vous sera envoyé.</p>
                </div>

                <div class="faq-card">
                    <h3>J'ai oublié mon mot de passe</h3>
                    <p>Sur la page de connexion, cliquez sur le lien <em>"Mot de passe oublié ?"</em>. Entrez votre adresse email. Si un compte est associé à cette adresse, vous recevrez un lien sécurisé pour réinitialiser votre mot de passe.</p>
                </div>

                <div class="faq-card">
                    <h3>Comment modifier mes informations personnelles ?</h3>
                    <p>Une fois connecté, cliquez sur l'icône <i class="fas fa-user" style="color:#55e6c1;"></i> pour accéder à "Mon Espace". Dans la section "Mes Infos", cliquez sur le bouton "Modifier mon profil". Vous pourrez y mettre à jour votre adresse de livraison et vos préférences.</p>
                </div>
            </section>

            <section id="section-boutique" class="faq-section">
                <h2 class="faq-title"><i class="fas fa-tshirt"></i> 3. Boutique & Produits</h2>

                <div class="faq-card">
                    <h3>Comment trouver un produit spécifique ?</h3>
                    <p>Utilisez notre moteur de recherche intelligent situé en haut de page, ou naviguez via la page "Boutique". Sur cette page, un panneau latéral à gauche vous permet de <strong>filtrer</strong> les produits :</p>
                    <ul>
                        <li>Par <strong>Équipe</strong> (France, Brésil, Argentine...).</li>
                        <li>Par <strong>Catégorie</strong> (Maillots, Shorts, Ballons...).</li>
                        <li>Par <strong>Couleur</strong> ou par <strong>Taille</strong>.</li>
                    </ul>
                </div>

                <div class="faq-card">
                    <h3>Guide des Tailles</h3>
                    <p>Nos produits suivent les standards internationaux :</p>
                    <ul style="column-count: 2;">
                        <li><strong>S :</strong> Small (Petit)</li>
                        <li><strong>M :</strong> Medium (Moyen)</li>
                        <li><strong>L :</strong> Large (Grand)</li>
                        <li><strong>XL :</strong> Extra Large</li>
                        <li><strong>XXL :</strong> Double Extra Large</li>
                    </ul>
                    <p><em>Conseil : Pour les maillots "Authentic" (coupe près du corps), nous recommandons de prendre une taille au-dessus si vous préférez une coupe plus ample.</em></p>
                </div>

                <div class="faq-card">
                    <h3>Disponibilité des produits</h3>
                    <p>Si un produit est indiqué "En rupture de stock", vous ne pourrez pas l'ajouter au panier. Les stocks sont mis à jour en temps réel.</p>
                </div>
            </section>

            <section id="section-panier" class="faq-section">
                <h2 class="faq-title"><i class="fas fa-shopping-basket"></i> 4. Panier & Commande</h2>

                <div class="faq-card">
                    <h3>Comment passer commande ? (Pas à pas)</h3>
                    <ol>
                        <li>Sur la fiche produit, sélectionnez votre taille et cliquez sur <strong>"Ajouter au panier"</strong>.</li>
                        <li>Une fois vos achats terminés, cliquez sur l'icône du panier <i class="fas fa-shopping-bag"></i> en haut à droite.</li>
                        <li>Vérifiez les quantités (vous pouvez les ajuster avec les boutons + et -).</li>
                        <li>Cliquez sur <strong>"Commander"</strong>.</li>
                        <li>Si vous n'êtes pas connecté, il vous sera demandé de le faire.</li>
                        <li>Suivez les étapes : Adresse -> Livraison -> Paiement.</li>
                    </ol>
                </div>

                <div class="faq-card">
                    <h3>Puis-je modifier ma commande après validation ?</h3>
                    <p>Une fois le paiement effectué, la commande est transmise à notre entrepôt. Si le statut est encore "En préparation", contactez immédiatement le service client. Si le statut est "Expédié", il faudra attendre la réception du colis pour effectuer un retour.</p>
                </div>
            </section>

            <section id="section-paiement" class="faq-section">
                <h2 class="faq-title"><i class="fas fa-credit-card"></i> 5. Paiement Sécurisé</h2>

                <div class="faq-card">
                    <h3>Quels sont les moyens de paiement acceptés ?</h3>
                    <p>Nous acceptons la majorité des cartes bancaires : <strong>Visa, MasterCard, Carte Bleue</strong>. Nous n'acceptons pas les chèques ni les virements pour les particuliers.</p>
                </div>

                <div class="faq-card">
                    <h3>Le paiement est-il sécurisé ?</h3>
                    <p>Absolument. Notre site utilise le protocole de chiffrement SSL (le petit cadenas dans la barre d'adresse) pour protéger toutes vos données. Vos informations bancaires ne sont jamais stockées en clair sur nos serveurs.</p>
                </div>
            </section>

            <section id="section-livraison" class="faq-section">
                <h2 class="faq-title"><i class="fas fa-truck"></i> 6. Livraison & Suivi</h2>

                <div class="faq-card">
                    <h3>Quels sont les délais et tarifs ?</h3>
                    <p>Les frais sont calculés automatiquement en fonction du poids de votre commande et de votre adresse.</p>
                    <ul>
                        <li><strong>Livraison Standard (3-5 jours) :</strong> Via Colissimo ou Mondial Relay.</li>
                        <li><strong>Livraison Express (24-48h) :</strong> Via Chronopost ou DHL.</li>
                    </ul>
                </div>

                <div class="faq-card">
                    <h3>Comment suivre mon colis ?</h3>
                    <p>Dès l'expédition de votre commande, vous recevrez un email avec un numéro de suivi. Vous pouvez également retrouver ce suivi dans "Mon Espace" > "Mes Commandes". Le statut évoluera de "Validée" à "Expédiée" puis "Livrée".</p>
                </div>
            </section>

            <section id="section-sav" class="faq-section">
                <h2 class="faq-title"><i class="fas fa-undo-alt"></i> 7. Retours & SAV</h2>

                <div class="faq-card">
                    <h3>Droit de rétractation (Satisfait ou Remboursé)</h3>
                    <p>Conformément à la loi, vous disposez de <strong>14 jours</strong> après réception pour nous retourner un article qui ne vous conviendrait pas (taille, couleur...). L'article doit être neuf, non porté et dans son emballage d'origine.</p>
                </div>

                <div class="faq-card">
                    <h3>Produit défectueux ou erreur de livraison</h3>
                    <p>Si vous recevez un produit abîmé ou qui ne correspond pas à votre commande :
                    <ol>
                        <li>Allez dans "Mon Espace" > "Mes Commandes".</li>
                        <li>Sur la commande concernée, cliquez sur <strong>"Signaler un problème"</strong>.</li>
                        <li>Décrivez le souci. Notre service client prendra en charge les frais de retour.</li>
                    </ol>
                    </p>
                </div>
            </section>

            <section id="section-votes" class="faq-section">
                <h2 class="faq-title" style="border-color: #e67e22; color: #e67e22;"><i class="fas fa-poll"></i> 8. Votes "The Best"</h2>

                <div class="faq-card">
                    <h3>Comment fonctionne le système de vote ?</h3>
                    <p>Pour chaque catégorie (Meilleur Joueur, Meilleur Gardien, etc.), vous devez élire un <strong>Top 3</strong>. L'ordre est crucial pour le décompte des points :</p>
                    <ul>
                        <li><strong>1er choix :</strong> 5 points</li>
                        <li><strong>2ème choix :</strong> 3 points</li>
                        <li><strong>3ème choix :</strong> 1 point</li>
                    </ul>
                </div>

                <div class="faq-card">
                    <h3>Règles importantes</h3>
                    <ul>
                        <li>Vous devez sélectionner <strong>exactement 3 candidats</strong> par catégorie.</li>
                        <li>Un vote validé est <strong>définitif</strong>. Vous ne pourrez pas le modifier.</li>
                        <li>Une seule participation par compte est autorisée.</li>
                    </ul>
                </div>
            </section>

            <section id="section-securite" class="faq-section">
                <h2 class="faq-title" style="border-color: #00cfb7; color: #00695c;"><i class="fas fa-shield-alt"></i> 9. Sécurité & Données</h2>

                <div class="faq-card">
                    <h3>Qu'est-ce que la Double Authentification (2FA) ?</h3>
                    <p>C'est une option de sécurité renforcée que nous vous conseillons d'activer. En plus de votre mot de passe, un code unique vous sera envoyé par email ou SMS à chaque connexion. Cela empêche un pirate d'accéder à votre compte même s'il vole votre mot de passe.</p>
                    <p>Pour l'activer : Allez dans "Mon Espace" > "Sécurité".</p>
                </div>

                <div class="faq-card">
                    <h3>Protection des données (RGPD)</h3>
                    <p>Vos données personnelles (nom, adresse, historique) sont utilisées uniquement pour le traitement de vos commandes et l'amélioration de nos services. Elles ne sont jamais revendues à des tiers. Vous pouvez demander la suppression de votre compte à tout moment via le formulaire de contact.</p>
                </div>
            </section>

        </div>
    </div>
</div>

{{-- STYLE SPÉCIFIQUE À LA FAQ (Intégré ici pour garantir l'autonomie du fichier) --}}
<style>
    .faq-nav-link {
        display: block;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        color: #555;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.95rem;
    }
    .faq-nav-link:hover {
        background: #f0f4f8;
        padding-left: 25px;
        color: #326295;
        font-weight: 600;
    }
    .faq-section {
        margin-bottom: 60px;
        scroll-margin-top: 120px; /* Important pour le scroll depuis le menu */
    }
    .faq-title {
        color: #326295;
        border-bottom: 3px solid #326295;
        padding-bottom: 15px;
        margin-bottom: 30px;
        font-size: 2rem;
        font-weight: 700;
    }
    .faq-card {
        background: white;
        border: 1px solid #e1e8ed;
        border-left: 5px solid #326295; /* Accent visuel */
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .faq-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }
    .faq-card h3 {
        margin-top: 0;
        color: #2c3e50;
        font-size: 1.3rem;
        margin-bottom: 15px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .faq-card h3::before {
        content: '\f059'; /* Icône point d'interrogation FontAwesome */
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: #00cfb7;
        font-size: 0.8em;
    }
    .faq-card p {
        color: #666;
        line-height: 1.7;
        font-size: 1rem;
        margin-bottom: 15px;
    }
    .faq-card ul, .faq-card ol {
        color: #666;
        margin-left: 20px;
        line-height: 1.7;
    }
    .faq-card li {
        margin-bottom: 8px;
    }
</style>
@endsection