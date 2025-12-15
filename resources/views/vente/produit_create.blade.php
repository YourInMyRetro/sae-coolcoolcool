@extends('layout')

@section('content')
<div class="container" style="padding: 50px 0; max-width: 800px; margin: 0 auto;">
    <h1>Créer un nouveau Produit</h1>
    <a href="{{ route('vente.dashboard') }}">← Retour Dashboard</a>

    @if(session('error'))
        <div style="color: red; margin: 10px 0; padding: 10px; background: #ffe6e6; border: 1px solid red;">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('vente.produit.store') }}" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
        @csrf
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold;">Nom du produit :</label>
            <input type="text" name="nom_produit" class="form-control" required style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold;">Description :</label>
            <textarea name="description_produit" class="form-control" style="width: 100%; padding: 8px; height: 100px;"></textarea>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold;">Catégorie :</label>
            <select name="id_categorie" class="form-control" style="width: 100%; padding: 8px;">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id_categorie }}">{{ $cat->nom_categorie }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 15px; border: 1px solid #ddd; padding: 15px; background: #f9f9f9; border-radius: 5px;">
            <h4 style="margin-top: 0;">Configuration Initiale (Prix & Stock)</h4>
            
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Couleur principale :</label>
                    <select name="id_couleur" style="width: 100%; padding: 5px;">
                        @foreach($couleurs as $c)
                            <option value="{{ $c->id_couleur }}">{{ $c->type_couleur }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1;">
                    <label>Taille par défaut :</label>
                    <select name="id_taille" style="width: 100%; padding: 5px;">
                        @foreach($tailles as $t)
                            <option value="{{ $t->id_taille }}">{{ $t->type_taille }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <div style="flex: 1;">
                    <label>Prix (€) :</label>
                    <input type="number" step="0.01" name="prix" required style="width: 100%; padding: 5px;">
                </div>
                <div style="flex: 1;">
                    <label>Stock initial :</label>
                    <input type="number" name="stock" value="10" style="width: 100%; padding: 5px;">
                </div>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold;">Photo du produit :</label>
            
            <input type="file" name="photo" id="photoInput" accept="image/*" style="margin-bottom: 10px;">
            
            <div style="width: 200px; height: 200px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; background: #eee; overflow: hidden;">
                <img id="previewImage" src="" alt="Aperçu de l'image" style="max-width: 100%; max-height: 100%; display: none;">
                <span id="previewText" style="color: #888;">Aucune image</span>
            </div>
        </div>

        <button type="submit" class="btn-fifa-cta" style="padding: 10px 20px; font-size: 1.1em; cursor: pointer;">
            Créer le produit
        </button>
    </form>
</div>

<script>
    // Script simple pour afficher l'image dès qu'on la sélectionne
    document.getElementById('photoInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewImage = document.getElementById('previewImage');
        const previewText = document.getElementById('previewText');

        if (file) {
            // Crée une URL temporaire pour l'image
            previewImage.src = URL.createObjectURL(file);
            previewImage.style.display = 'block';
            previewText.style.display = 'none';
        } else {
            previewImage.style.display = 'none';
            previewText.style.display = 'inline';
        }
    });
</script>
@endsection