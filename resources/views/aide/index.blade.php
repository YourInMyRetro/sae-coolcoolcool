@extends('layout')

@section('content')
<div class="container" style="padding: 40px 20px; max-width: 1000px;">
    <h1 style="color: #003366; text-align: center; margin-bottom: 40px;">Guide de l'Utilisateur & FAQ</h1>

    {{-- Menu de navigation rapide --}}
    <div style="display: flex; gap: 10px; justify-content: center; margin-bottom: 30px; flex-wrap: wrap;">
        <a href="#pro" class="btn-fifa-primary" style="background:#326295; padding: 10px 20px; color: white; text-decoration: none; border-radius: 20px;">Compte Pro</a>
        <a href="#boutique" class="btn-fifa-primary" style="background:#326295; padding: 10px 20px; color: white; text-decoration: none; border-radius: 20px;">Boutique</a>
        <a href="#panier" class="btn-fifa-primary" style="background:#326295; padding: 10px 20px; color: white; text-decoration: none; border-radius: 20px;">Panier</a>
        <a href="#sav" class="btn-fifa-primary" style="background:#e74c3c; padding: 10px 20px; color: white; text-decoration: none; border-radius: 20px;">Litiges / SAV</a>
    </div>

    {{-- Contenu Accordéon (Style maison pour s'adapter à fifa-style) --}}
    <div class="faq-section">
        
        <div id="pro" style="margin-bottom: 30px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
            <div style="background: #f8faff; padding: 15px; border-bottom: 1px solid #eee;">
                <h3 style="margin:0; color:#003366;">2.1.2 Compte Professionnel</h3>
            </div>
            <div style="padding: 20px;">
                <p><strong>Pourquoi un compte Pro ?</strong> Il permet aux clubs et associations d'accéder aux tarifs de gros.</p>
                <p><strong>Validation :</strong> Renseignez votre SIRET/TVA. Le compte doit être validé par un administrateur avant d'être actif.</p>
            </div>
        </div>

        <div id="boutique" style="margin-bottom: 30px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
            <div style="background: #f8faff; padding: 15px; border-bottom: 1px solid #eee;">
                <h3 style="margin:0; color:#003366;">3. Recherche et Filtres</h3>
            </div>
            <div style="padding: 20px;">
                <p>Utilisez la barre latérale gauche pour filtrer les produits par <strong>Nation</strong>, <strong>Catégorie</strong> ou <strong>Taille</strong>.</p>
                <p><em>Astuce :</em> Vous pouvez cumuler les filtres (ex: "Maillots" + "France" + "Taille M").</p>
            </div>
        </div>

        <div id="panier" style="margin-bottom: 30px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
            <div style="background: #f8faff; padding: 15px; border-bottom: 1px solid #eee;">
                <h3 style="margin:0; color:#003366;">4. Gestion du Panier</h3>
            </div>
            <div style="padding: 20px;">
                <p>Vous pouvez modifier les quantités directement dans le tableau. Le montant total se met à jour automatiquement.</p>
                <p>Les frais de port sont calculés à l'étape suivante (Livraison).</p>
            </div>
        </div>

        <div id="sav" style="margin-bottom: 30px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
            <div style="background: #fff0f0; padding: 15px; border-bottom: 1px solid #ffdcdc;">
                <h3 style="margin:0; color:#c0392b;">5.2 Gestion des Réserves (SAV)</h3>
            </div>
            <div style="padding: 20px;">
                <p>Si votre colis arrive endommagé :</p>
                <ol>
                    <li>Allez dans "Mon Compte" > "Mes Commandes".</li>
                    <li>Si la commande est "Livrée", un bouton <strong>"Émettre une réserve"</strong> apparaît.</li>
                    <li>Remplissez le formulaire. Le service client traitera votre demande sous 15 jours.</li>
                </ol>
            </div>
        </div>

        <div id="votes" style="margin-bottom: 30px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
            <div style="background: #f8faff; padding: 15px; border-bottom: 1px solid #eee;">
                <h3 style="margin:0; color:#003366;">6. Système de Vote</h3>
            </div>
            <div style="padding: 20px;">
                <p>Pour "The Best", vous devez sélectionner 3 candidats. Le vote est définitif une fois validé.</p>
            </div>
        </div>

    </div>
</div>
@endsection