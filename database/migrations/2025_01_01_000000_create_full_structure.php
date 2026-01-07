<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // ===============================================================
        // 1. UTILISATEURS & AUTHENTIFICATION
        // ===============================================================
        Schema::create('utilisateur', function (Blueprint $table) {
            $table->increments('id_utilisateur');
            $table->date('date_naissance');
            $table->string('nom', 50);
            $table->string('prenom', 50);
            $table->string('mail', 100)->unique();
            $table->string('surnom', 50)->nullable();
            $table->string('pays_naissance', 50);
            $table->string('langue', 50);
            $table->string('mot_de_passe_chiffre', 255)->nullable(); // Nullable pour Google Auth
            
            // --- AJOUTS MANQUANTS (CORRECTION) ---
            $table->string('role')->default('client'); 
            $table->boolean('newsletter_optin')->default(false);
            $table->string('telephone', 20)->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->boolean('double_auth_active')->default(false);
            $table->string('code_auth_temporaire', 10)->nullable();
            $table->timestamp('code_auth_expiration')->nullable();
            
            $table->rememberToken();
            $table->timestamps(); 
        });

        Schema::create('acheteur', function (Blueprint $table) {
            $table->integer('id_utilisateur')->primary();
            $table->foreign('id_utilisateur')->references('id_utilisateur')->on('utilisateur')->onDelete('cascade');
        });

        Schema::create('professionel', function (Blueprint $table) {
            $table->integer('id_utilisateur')->primary();
            $table->string('nom_societe', 100);
            $table->string('numero_tva_intracommunautaire', 30)->nullable()->unique();
            $table->string('activite', 100);
            $table->foreign('id_utilisateur')->references('id_utilisateur')->on('utilisateur')->onDelete('cascade');
        });

        // ===============================================================
        // 2. ADRESSES & PAIEMENT
        // ===============================================================
        Schema::create('adresse', function (Blueprint $table) {
            $table->increments('id_adresse');
            $table->string('rue', 255);
            $table->string('code_postal_adresse', 10);
            $table->string('ville_adresse', 100);
            $table->string('pays_adresse', 50);
            $table->enum('type_adresse', ['Livraison', 'Facturation', 'Principale']);
        });

        Schema::create('possedeadresse', function (Blueprint $table) {
            $table->integer('id_adresse');
            $table->integer('id_utilisateur');
            $table->primary(['id_adresse', 'id_utilisateur']);
            $table->foreign('id_adresse')->references('id_adresse')->on('adresse')->onDelete('cascade');
            $table->foreign('id_utilisateur')->references('id_utilisateur')->on('utilisateur')->onDelete('cascade');
        });

        Schema::create('carte_bancaire', function (Blueprint $table) {
            $table->increments('id_cb');
            $table->integer('id_utilisateur');
            $table->string('numero_chiffre', 255);
            $table->string('ccv_chiffre', 255);
            $table->date('expiration');
            $table->foreign('id_utilisateur')->references('id_utilisateur')->on('acheteur');
        });

        // ===============================================================
        // 3. CONTENU (PUBLICATION, BLOG, ETC.)
        // ===============================================================
        Schema::create('publication', function (Blueprint $table) {
            $table->increments('id_publication');
            $table->timestamp('date_publication')->useCurrent();
            $table->string('titre_publication', 255);
            $table->text('resume_publication')->nullable();
            $table->string('photo_presentation', 1024)->nullable();
        });

        foreach (['album', 'article', 'blog', 'document'] as $type) {
            Schema::create($type, function (Blueprint $table) use ($type) {
                $table->integer('id_publication')->primary();
                $table->foreign('id_publication')->references('id_publication')->on('publication')->onDelete('cascade');
                
                if ($type === 'article') $table->text('texte_article')->nullable();
                if ($type === 'blog') $table->text('texte_blog')->nullable();
                if ($type === 'document') $table->string('url_pdf', 1024);
            });
        }

        Schema::create('commentaire', function (Blueprint $table) {
            $table->increments('id_commentaire');
            $table->integer('id_publication');
            $table->integer('com_id_commentaire')->nullable();
            $table->integer('id_utilisateur');
            $table->text('texte_commentaire');
            $table->timestamp('date_depot')->useCurrent();

            $table->foreign('id_publication')->references('id_publication')->on('publication')->onDelete('cascade');
            $table->foreign('com_id_commentaire')->references('id_commentaire')->on('commentaire')->onDelete('cascade');
            $table->foreign('id_utilisateur')->references('id_utilisateur')->on('utilisateur')->onDelete('set null');
        });

        // ===============================================================
        // 4. FOOTBALL & VOTES
        // ===============================================================
        Schema::create('club', function (Blueprint $table) {
            $table->increments('idclub');
            $table->string('nomclub', 100)->unique();
            $table->text('description')->nullable();
        });

        Schema::create('candidat', function (Blueprint $table) {
            $table->increments('idjoueur');
            $table->integer('idclub')->nullable();
            $table->string('nom_joueur', 50);
            $table->string('prenom_joueur', 50)->nullable();
            $table->date('date_naissance_joueur');
            $table->enum('pied_prefere', ['Droit', 'Gauche', 'Ambidextre'])->nullable();
            $table->decimal('taille_joueur', 5, 2);
            $table->decimal('poids_joueur', 5, 2);
            $table->integer('nombre_selection')->nullable();

            $table->foreign('idclub')->references('idclub')->on('club')->onDelete('set null');
        });

        Schema::create('trophee_joueur', function (Blueprint $table) {
            $table->increments('id_trophee_joueur');
            $table->integer('idjoueur');
            $table->string('saison_trophee', 20);
            $table->string('nom_trophee', 100);
            $table->string('resume_trophee', 255)->nullable();
            $table->foreign('idjoueur')->references('idjoueur')->on('candidat')->onDelete('cascade');
        });

        Schema::create('vote_theme', function (Blueprint $table) {
            $table->increments('idtheme');
            $table->string('nom_theme', 255)->unique();
            $table->timestamp('date_ouverture')->nullable();
            $table->timestamp('date_fermeture')->nullable();
        });

        Schema::create('vote_candidat', function (Blueprint $table) {
            $table->increments('id_vote_candidat');
            $table->integer('idtheme');
            $table->string('nom_affichage', 1024)->nullable();
            $table->string('type_affichage', 50)->nullable();
            $table->foreign('idtheme')->references('idtheme')->on('vote_theme')->onDelete('cascade');
        });

        Schema::create('concernecandidat', function (Blueprint $table) {
            $table->integer('id_vote_candidat');
            $table->integer('idjoueur');
            $table->primary(['id_vote_candidat', 'idjoueur']);
            $table->foreign('id_vote_candidat')->references('id_vote_candidat')->on('vote_candidat')->onDelete('cascade');
            $table->foreign('idjoueur')->references('idjoueur')->on('candidat')->onDelete('cascade');
        });

        Schema::create('vote', function (Blueprint $table) {
            $table->increments('id_vote');
            $table->integer('idtheme');
            $table->timestamp('date_vote')->useCurrent();
            $table->foreign('idtheme')->references('idtheme')->on('vote_theme');
        });

        Schema::create('faitvote', function (Blueprint $table) {
            $table->integer('id_vote');
            $table->integer('id_utilisateur');
            $table->primary(['id_vote', 'id_utilisateur']);
            $table->foreign('id_vote')->references('id_vote')->on('vote')->onDelete('cascade');
            $table->foreign('id_utilisateur')->references('id_utilisateur')->on('utilisateur')->onDelete('cascade');
        });

        Schema::create('concernevote', function (Blueprint $table) {
            $table->integer('id_vote_candidat');
            $table->integer('id_vote');
            $table->integer('classement');
            $table->primary(['id_vote', 'id_vote_candidat']);
            $table->foreign('id_vote_candidat')->references('id_vote_candidat')->on('vote_candidat')->onDelete('cascade');
            $table->foreign('id_vote')->references('id_vote')->on('vote')->onDelete('cascade');
        });

        // ===============================================================
        // 5. PRODUITS, BOUTIQUE & NATION
        // ===============================================================
        Schema::create('categorie', function (Blueprint $table) {
            $table->increments('id_categorie');
            $table->string('nom_categorie', 50)->unique();
        });

        Schema::create('couleur', function (Blueprint $table) {
            $table->increments('id_couleur');
            $table->string('type_couleur', 50)->unique();
        });

        Schema::create('taille', function (Blueprint $table) {
            $table->increments('id_taille');
            $table->string('type_taille', 50)->unique();
        });

        // --- AJOUT TABLE NATION ICI ---
        Schema::create('nation', function (Blueprint $table) {
            $table->increments('id_nation');
            $table->string('nom_nation', 50)->unique();
        });

        // Insertion des nations par défaut directement à la création
        DB::table('nation')->insertOrIgnore([
            ['nom_nation' => 'France'], ['nom_nation' => 'Brésil'],
            ['nom_nation' => 'Allemagne'], ['nom_nation' => 'Argentine'],
            ['nom_nation' => 'Espagne'], ['nom_nation' => 'Italie'],
            ['nom_nation' => 'Angleterre'], ['nom_nation' => 'Portugal'],
        ]);

        Schema::create('produit', function (Blueprint $table) {
            $table->increments('id_produit');
            $table->integer('id_categorie');
            $table->integer('id_fiche_fabrication')->nullable();
            $table->string('nom_produit', 255);
            $table->text('description_produit')->nullable();
            $table->enum('visibilite', ['visible', 'cache'])->default('cache');
            $table->date('date_creation')->nullable(); 
            $table->foreign('id_categorie')->references('id_categorie')->on('categorie');
        });

        // Liaison Produit <-> Nation
        Schema::create('produit_nation', function (Blueprint $table) {
            $table->integer('id_produit');
            $table->integer('id_nation');
            $table->primary(['id_produit', 'id_nation']);
            $table->foreign('id_produit')->references('id_produit')->on('produit')->onDelete('cascade');
            $table->foreign('id_nation')->references('id_nation')->on('nation')->onDelete('cascade');
        });

        Schema::create('produit_couleur', function (Blueprint $table) {
            $table->increments('id_produit_couleur');
            $table->integer('id_produit');
            $table->integer('id_couleur');
            $table->decimal('prix_total', 10, 2);
            $table->decimal('prix_promotion', 10, 2)->nullable();
            $table->unique(['id_produit', 'id_couleur']);
            $table->foreign('id_produit')->references('id_produit')->on('produit')->onDelete('cascade');
            $table->foreign('id_couleur')->references('id_couleur')->on('couleur');
        });

        Schema::create('stock_article', function (Blueprint $table) {
            $table->increments('id_stock_article');
            $table->integer('id_produit_couleur');
            $table->integer('id_taille');
            $table->integer('stock');
            $table->foreign('id_produit_couleur')->references('id_produit_couleur')->on('produit_couleur')->onDelete('cascade');
            $table->foreign('id_taille')->references('id_taille')->on('taille');
        });

        Schema::create('photo_produit', function (Blueprint $table) {
            $table->increments('id_photo_produit');
            $table->integer('id_produit');
            $table->string('url_photo', 1024);
            $table->foreign('id_produit')->references('id_produit')->on('produit')->onDelete('cascade');
        });

        // ===============================================================
        // 6. COMMANDES & PANIER
        // ===============================================================
        Schema::create('panier', function (Blueprint $table) {
            $table->increments('id_panier');
            $table->integer('id_utilisateur')->unique();
            $table->timestamps();
            $table->foreign('id_utilisateur')->references('id_utilisateur')->on('utilisateur')->onDelete('cascade');
        });

        Schema::create('ligne_panier', function (Blueprint $table) {
            $table->increments('id_ligne_panier');
            $table->integer('id_panier');
            $table->integer('id_stock_article');
            $table->integer('quantite');
            $table->foreign('id_panier')->references('id_panier')->on('panier')->onDelete('cascade');
            $table->foreign('id_stock_article')->references('id_stock_article')->on('stock_article');
        });

        Schema::create('commande', function (Blueprint $table) {
            $table->increments('id_commande');
            $table->integer('id_adresse');
            $table->integer('id_utilisateur');
            $table->timestamp('date_commande')->useCurrent();
            $table->decimal('montant_total', 10, 2)->nullable();
            $table->decimal('frais_livraison', 10, 2)->default(0);
            $table->decimal('taxes_livraison', 10, 2)->default(0);
            $table->enum('statut_livraison', ['En préparation', 'Expédiée', 'Livrée', 'Validée', 'Annulée', 'Contentieux', 'Réserve'])->default('En préparation');
            $table->enum('type_livraison', ['Standard', 'Express']);
            
            $table->foreign('id_adresse')->references('id_adresse')->on('adresse');
            $table->foreign('id_utilisateur')->references('id_utilisateur')->on('acheteur');
        });

        Schema::create('ligne_commande', function (Blueprint $table) {
            $table->increments('id_ligne_commande');
            $table->integer('id_commande');
            $table->integer('quantite_commande');
            $table->decimal('prix_unitaire_negocie', 10, 2)->nullable();
            $table->foreign('id_commande')->references('id_commande')->on('commande')->onDelete('cascade');
        });

        Schema::create('estplacee', function (Blueprint $table) {
            $table->integer('id_stock_article');
            $table->integer('id_ligne_commande');
            $table->primary(['id_stock_article', 'id_ligne_commande']);
            $table->foreign('id_stock_article')->references('id_stock_article')->on('stock_article');
            $table->foreign('id_ligne_commande')->references('id_ligne_commande')->on('ligne_commande')->onDelete('cascade');
        });

        // ===============================================================
        // 7. AUTRES (Photos Publi, Fiches)
        // ===============================================================
        Schema::create('photo_publication', function (Blueprint $table) {
            $table->increments('id_photo_publication');
            $table->string('url_photo', 1024);
        });
        
        Schema::create('photo_candidat', function (Blueprint $table) {
             $table->integer('id_photo_publication');
             $table->integer('idjoueur');
             $table->primary(['id_photo_publication', 'idjoueur']);
             $table->foreign('id_photo_publication')->references('id_photo_publication')->on('photo_publication')->onDelete('cascade');
             $table->foreign('idjoueur')->references('idjoueur')->on('candidat')->onDelete('cascade');
        });

        Schema::create('fiche_fabrication', function (Blueprint $table) {
            $table->increments('id_fiche_fabrication');
            $table->integer('id_produit');
            $table->integer('id_utilisateur');
            $table->integer('id_professionel_demandeur')->nullable();
            $table->date('date_creation')->useCurrent();
            $table->text('details_fabrication')->nullable();
            
            $table->foreign('id_utilisateur')->references('id_utilisateur')->on('utilisateur');
            $table->foreign('id_professionel_demandeur')->references('id_utilisateur')->on('professionel');
        });
        
        // Transporteur pour l'expédition (Bonus pour éviter bugs)
        Schema::create('transporteur', function (Blueprint $table) {
            $table->increments('id_transporteur');
            $table->string('nom', 50);
            $table->integer('delai_min_transporteur')->nullable();
            $table->integer('delai_max_transporteur')->nullable();
        });
    }

    public function down()
    {
        // Nettoyage complet
        Schema::dropIfExists('produit_nation');
        Schema::dropIfExists('nation');
        Schema::dropIfExists('transporteur');
        Schema::dropIfExists('estplacee');
        Schema::dropIfExists('ligne_commande');
        Schema::dropIfExists('commande');
        Schema::dropIfExists('ligne_panier');
        Schema::dropIfExists('panier');
        Schema::dropIfExists('stock_article');
        Schema::dropIfExists('produit_couleur');
        Schema::dropIfExists('photo_produit');
        Schema::dropIfExists('fiche_fabrication');
        Schema::dropIfExists('produit');
        Schema::dropIfExists('taille');
        Schema::dropIfExists('couleur');
        Schema::dropIfExists('categorie');
        Schema::dropIfExists('concernevote');
        Schema::dropIfExists('faitvote');
        Schema::dropIfExists('vote');
        Schema::dropIfExists('concernecandidat');
        Schema::dropIfExists('vote_candidat');
        Schema::dropIfExists('vote_theme');
        Schema::dropIfExists('trophee_joueur');
        Schema::dropIfExists('photo_candidat');
        Schema::dropIfExists('candidat');
        Schema::dropIfExists('club');
        Schema::dropIfExists('commentaire');
        Schema::dropIfExists('blog');
        Schema::dropIfExists('document');
        Schema::dropIfExists('article');
        Schema::dropIfExists('album');
        Schema::dropIfExists('photo_publication');
        Schema::dropIfExists('publication');
        Schema::dropIfExists('carte_bancaire');
        Schema::dropIfExists('possedeadresse');
        Schema::dropIfExists('adresse');
        Schema::dropIfExists('professionel');
        Schema::dropIfExists('acheteur');
        Schema::dropIfExists('utilisateur');
    }
};