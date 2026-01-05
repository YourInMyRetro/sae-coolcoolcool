@extends('layout')

@section('content')
<div class="container py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h6 class="text-uppercase text-muted fw-bold mb-1">Service Vente</h6>
            <h1 class="text-white fw-bold">Nouveau Produit</h1>
        </div>
        <a href="{{ route('vente.dashboard') }}" class="btn-fifa-action" style="background: #333; box-shadow: none; border: 1px solid #555;">
            <i class="fas fa-arrow-left me-2"></i> Retour
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger mb-4">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('vente.produit.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="dashboard-card mb-4">
                    <h3 class="border-bottom border-secondary pb-2 mb-4">
                        <i class="fas fa-info-circle me-2"></i> Informations Principales
                    </h3>

                    <div class="mb-3">
                        <label class="text-muted small fw-bold text-uppercase mb-2">Nom du produit</label>
                        <input type="text" name="nom_produit" class="form-control input-dark" required placeholder="Ex: Maillot Domicile France 2026">
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small fw-bold text-uppercase mb-2">Description</label>
                        <textarea name="description_produit" class="form-control input-dark" style="height: 120px;" placeholder="Description détaillée du produit..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase mb-2">Catégorie</label>
                            <select name="id_categorie" class="form-control input-dark">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id_categorie }}">{{ $cat->nom_categorie }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase mb-2">Photos du produit</label>
                            <input type="file" name="photos[]" id="photoInput" accept="image/*" class="form-control input-dark" multiple>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h3 class="border-bottom border-secondary pb-2 mb-4">
                        <i class="fas fa-boxes me-2"></i> Stocks & Variantes
                    </h3>
                    
                    <div class="table-responsive">
                        <table class="fifa-table">
                            <thead>
                                <tr>
                                    <th style="color: #a0aec0;">Couleur</th>
                                    <th style="color: #a0aec0;">Taille</th>
                                    <th style="color: #a0aec0;">Quantité</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="variantContainer">
                                <tr>
                                    <td>
                                        <select name="variantes[0][id_couleur]" class="form-control input-dark border-0">
                                            @foreach($couleurs as $c)
                                                <option value="{{ $c->id_couleur }}">{{ $c->type_couleur }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="variantes[0][id_taille]" class="form-control input-dark border-0">
                                            @foreach($tailles as $t)
                                                <option value="{{ $t->id_taille }}">{{ $t->type_taille }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="variantes[0][quantite]" class="form-control input-dark border-0" value="0" min="0">
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <button type="button" class="btn btn-outline-light btn-sm mt-3" id="addRowBtn">
                        <i class="fas fa-plus"></i> Ajouter une variante
                    </button>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="dashboard-card mb-4 text-center">
                    <h3 class="mb-3">Aperçu Photos</h3>
                    <div id="previewContainer" style="width: 100%; min-height: 250px; background: #0f1623; border: 2px dashed #2d3b55; display: flex; flex-wrap: wrap; align-items: center; justify-content: center; padding: 10px; gap: 10px; border-radius: 8px;">
                        <span id="previewText" class="text-muted"><i class="fas fa-images fa-2x mb-2"></i><br>Aucune image sélectionnée</span>
                    </div>
                </div>

                <div class="dashboard-card mb-4" style="border-color: #f39c12;">
                    <h3 class="text-warning mb-3"><i class="fas fa-tag me-2"></i> Prix de vente</h3>
                    <p class="small text-muted mb-3">
                        Si laissé vide, le produit sera envoyé au <strong>Directeur</strong> pour validation du prix.
                    </p>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-white">€</span>
                        <input type="number" step="0.01" name="prix" class="form-control input-dark fw-bold text-warning" placeholder="0.00">
                    </div>
                </div>

                <button type="submit" class="btn-fifa-cyan w-100 py-3 fw-bold text-uppercase" style="font-size: 1.1em;">
                    <i class="fas fa-save me-2"></i> Enregistrer le produit
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('photoInput').addEventListener('change', function(event) {
        const files = event.target.files;
        const previewContainer = document.getElementById('previewContainer');
        const previewText = document.getElementById('previewText');

        previewContainer.innerHTML = '';

        if (files && files.length > 0) {
            Array.from(files).forEach(file => {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '4px';
                img.style.border = '1px solid #555';
                previewContainer.appendChild(img);
            });
        } else {
            previewContainer.appendChild(previewText);
            previewText.style.display = 'inline';
        }
    });

    let rowIdx = 1;
    const couleursOptions = `@foreach($couleurs as $c)<option value="{{ $c->id_couleur }}">{{ $c->type_couleur }}</option>@endforeach`;
    const taillesOptions = `@foreach($tailles as $t)<option value="{{ $t->id_taille }}">{{ $t->type_taille }}</option>@endforeach`;

    document.getElementById('addRowBtn').addEventListener('click', function() {
        const tableBody = document.getElementById('variantContainer');
        const newRow = document.createElement('tr');
        
        newRow.innerHTML = `
            <td>
                <select name="variantes[${rowIdx}][id_couleur]" class="form-control input-dark border-0">
                    ${couleursOptions}
                </select>
            </td>
            <td>
                <select name="variantes[${rowIdx}][id_taille]" class="form-control input-dark border-0">
                    ${taillesOptions}
                </select>
            </td>
            <td>
                <input type="number" name="variantes[${rowIdx}][quantite]" class="form-control input-dark border-0" value="0" min="0">
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button>
            </td>
        `;
        
        tableBody.appendChild(newRow);
        rowIdx++;
    });

    document.getElementById('variantContainer').addEventListener('click', function(e) {
        if(e.target.closest('.remove-row')) {
            if(document.querySelectorAll('#variantContainer tr').length > 1) {
                e.target.closest('tr').remove();
            } else {
                alert("Il faut au moins une variante.");
            }
        }
    });
</script>
@endsection