@extends('layout')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="text-white fw-bold text-uppercase">Catalogue Produits</h1>
            <p class="text-muted">Gérez la visibilité et les visuels de la boutique</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('vente.produit.create') }}" class="btn btn-fifa-cyan fw-bold">
                <i class="fas fa-plus-circle me-2"></i>Nouveau Produit
            </a>
            <a href="{{ route('vente.dashboard') }}" class="btn btn-outline-light">Retour Dashboard</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4"><i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}</div>
    @endif

    <div class="dashboard-card p-0 overflow-hidden">
        <table class="fifa-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Aperçu</th>
                    <th>Nom du Produit</th>
                    <th>Catégorie</th>
                    <th>Prix Actuel</th>
                    <th>État</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produits as $produit)
                    <tr>
                        <td class="ps-4">
                            <div class="product-thumb">
                                @if($produit->photos->count() > 0)
                                    <img src="{{ asset($produit->photos->first()->url_photo) }}" alt="Img">
                                @else
                                    <div class="no-img"><i class="fas fa-camera"></i></div>
                                @endif
                                <span class="photo-count badge bg-dark">{{ $produit->photos->count() }}</span>
                            </div>
                        </td>
                        <td class="text-white fw-bold">{{ $produit->nom_produit }}</td>
                        <td class="text-muted">{{ optional($produit->categorie)->nom_categorie ?? 'N/A' }}</td>
                        <td class="text-fifa-cyan fw-bold">{{ number_format($produit->premierPrix?->prix_total ?? 0, 2) }} €</td>
                        <td>
                            @if($produit->visibilite == 'visible')
                                <span class="badge bg-success">EN LIGNE</span>
                            @else
                                <span class="badge bg-secondary">MASQUÉ</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                {{-- BOUTON PHOTOS --}}
                                <button onclick="openPhotoModal({{ $produit->id_produit }}, {{ json_encode($produit->photos) }}, '{{ addslashes($produit->nom_produit) }}')"
                                        class="btn btn-sm btn-outline-info" title="Gérer les photos">
                                    <i class="fas fa-images"></i>
                                </button>

                                {{-- BOUTON VISIBILITÉ (CORRIGÉ) --}}
                                <form action="{{ route('vente.produit.visibilite', $produit->id_produit) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $produit->visibilite == 'visible' ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                            title="{{ $produit->visibilite == 'visible' ? 'Masquer' : 'Publier' }}">
                                        <i class="fas {{ $produit->visibilite == 'visible' ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Aucun produit dans le catalogue.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL PHOTOS (Overlay) --}}
<div id="photoModal" class="modal-overlay" style="display: none;">
    <div class="modal-glass">
        <div class="modal-header">
            <h3 id="modalTitle">Gestion Photos</h3>
            <button onclick="closePhotoModal()" class="btn-close-custom"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="modal-body">
            {{-- Zone d'ajout --}}
            <form id="addPhotoForm" action="" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <div class="upload-area">
                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                    <p class="mb-2">Glissez une image ou cliquez</p>
                    <input type="file" name="photo" required onchange="this.form.submit()">
                </div>
            </form>

            {{-- Grille des photos existantes --}}
            <div id="photosGrid" class="photos-grid">
                </div>
        </div>
    </div>
</div>

<style>
    .dashboard-card { background-color: #1a202c; border: 1px solid #2d3748; border-radius: 12px; }
    .fifa-table { width: 100%; color: white; }
    .fifa-table thead th { background-color: #151c2a; padding: 15px; border-bottom: 2px solid #2d3748; color: #a0aec0; text-transform: uppercase; font-size: 0.85rem;}
    .fifa-table tbody td { padding: 15px; border-bottom: 1px solid #2d3748; vertical-align: middle; }
    
    .product-thumb { width: 50px; height: 50px; border-radius: 8px; overflow: hidden; position: relative; background: #000; border: 1px solid #444; }
    .product-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .no-img { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #555; }
    .photo-count { position: absolute; bottom: 0; right: 0; font-size: 0.6rem; opacity: 0.8; }

    .text-fifa-cyan { color: #00cfb7; }
    .btn-fifa-cyan { background-color: #00cfb7; color: #0f1623; border: none; padding: 10px 20px; border-radius: 6px; }
    .btn-fifa-cyan:hover { background-color: #00b39d; }

    /* MODAL STYLE */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.8); backdrop-filter: blur(5px);
        z-index: 1000; display: flex; justify-content: center; align-items: center;
    }
    .modal-glass {
        background: #1a202c; border: 1px solid #2d3748; border-radius: 16px;
        width: 600px; max-width: 90%; padding: 30px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; color: white; }
    .btn-close-custom { background: none; border: none; color: #a0aec0; font-size: 1.5rem; cursor: pointer; transition: 0.2s; }
    .btn-close-custom:hover { color: white; transform: rotate(90deg); }

    .upload-area {
        border: 2px dashed #4a5568; border-radius: 10px; padding: 30px; text-align: center;
        color: #a0aec0; position: relative; cursor: pointer; transition: 0.3s;
    }
    .upload-area:hover { border-color: #00cfb7; color: #00cfb7; background: rgba(0, 207, 183, 0.05); }
    .upload-area input { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }

    .photos-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; max-height: 300px; overflow-y: auto; }
    .photo-item { position: relative; aspect-ratio: 1; border-radius: 8px; overflow: hidden; border: 1px solid #444; }
    .photo-item img { width: 100%; height: 100%; object-fit: cover; }
    .btn-delete-photo {
        position: absolute; top: 5px; right: 5px; background: rgba(255,0,0,0.8); color: white;
        border: none; width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: 0.2s;
    }
    .btn-delete-photo:hover { transform: scale(1.1); }
</style>

<script>
    function openPhotoModal(id, photos, name) {
        document.getElementById('modalTitle').innerText = 'Photos : ' + name;
        document.getElementById('photoModal').style.display = 'flex';
        
        // Configurer l'action du formulaire d'ajout
        // Note: La route doit être définie dans web.php
        const addRoute = "{{ route('vente.produit.photo.add', ':id') }}".replace(':id', id);
        document.getElementById('addPhotoForm').action = addRoute;

        // Remplir la grille
        const grid = document.getElementById('photosGrid');
        grid.innerHTML = '';
        
        photos.forEach(photo => {
            const deleteRoute = "{{ route('vente.produit.photo.delete', ':id') }}".replace(':id', photo.id_photo_produit);
            
            const div = document.createElement('div');
            div.className = 'photo-item';
            div.innerHTML = `
                <img src="/${photo.url_photo}" onerror="this.src='/img/placeholder.jpg'">
                <form action="${deleteRoute}" method="POST" onsubmit="return confirm('Supprimer cette photo ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete-photo"><i class="fas fa-trash"></i></button>
                </form>
            `;
            grid.appendChild(div);
        });
    }

    function closePhotoModal() {
        document.getElementById('photoModal').style.display = 'none';
    }

    // Fermer si on clique dehors
    document.getElementById('photoModal').addEventListener('click', function(e) {
        if (e.target === this) closePhotoModal();
    });
</script>
@endsection