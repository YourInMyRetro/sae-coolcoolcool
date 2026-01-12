@extends('layout')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="text-white fw-bold">Gestion des Produits</h1>
        <a href="{{ route('vente.dashboard') }}" class="btn btn-outline-light">Retour Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}</div>
    @endif

    <div class="dashboard-card">
        <table class="fifa-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>État</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produits as $produit)
                    <tr>
                        <td>
                            <img src="{{ asset($produit->photos->first()->url_photo ?? 'img/placeholder.jpg') }}" 
                                 class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td class="text-white fw-bold">{{ $produit->nom_produit }}</td>
                        <td class="text-muted">{{ $produit->categorie->nom_categorie ?? 'N/A' }}</td>
                        <td class="text-warning fw-bold">{{ number_format($produit->prix_actuel(), 2) }} €</td>
                        <td>
                            <span class="badge {{ $produit->visibilite == 'visible' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($produit->visibilite) }}
                            </span>
                        </td>
                        <td class="text-end">
                            {{-- Bouton Gestion Photos --}}
                            <button onclick="openPhotoModal({{ $produit->id_produit }}, {{ json_encode($produit->photos) }}, '{{ addslashes($produit->nom_produit) }}')" 
                                    class="btn btn-sm btn-info me-2" title="Gérer les photos">
                                <i class="fas fa-images"></i>
                            </button>

                            {{-- Bouton Visibilité --}}
                            <form action="{{ route('vente.produit.toggle', $produit->id_produit) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-light" title="Changer visibilité">
                                    <i class="fas {{ $produit->visibilite == 'visible' ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div id="photoModal" class="modal-overlay" style="display: none;">
    <div class="modal-content bg-dark border border-secondary text-white p-4 rounded-3" style="max-width: 600px; margin: 10% auto; position: relative;">
        <button onclick="closePhotoModal()" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"></button>
        
        <h3 class="mb-4">Photos : <span id="modalProductName" class="text-info"></span></h3>

        <div class="row g-3 mb-4" id="photoListContainer">
            </div>

        <hr class="border-secondary">

        <form id="addPhotoForm" method="POST" enctype="multipart/form-data" class="mt-3">
            @csrf
            <label class="form-label text-muted small text-uppercase fw-bold">Ajouter une nouvelle image</label>
            <div class="input-group">
                <input type="file" name="photo" class="form-control bg-dark text-white border-secondary" accept="image/*" required>
                <button type="submit" class="btn btn-success fw-bold"><i class="fas fa-plus me-2"></i>Ajouter</button>
            </div>
        </form>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.8); z-index: 9999;
    }
    .photo-item { position: relative; }
    .btn-delete-photo {
        position: absolute; top: 5px; right: 5px;
        background: rgba(220, 53, 69, 0.9); color: white; border: none;
        width: 25px; height: 25px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: 0.2s;
    }
    .btn-delete-photo:hover { transform: scale(1.1); background: red; }
</style>

<script>
    function openPhotoModal(id, photos, name) {
        document.getElementById('modalProductName').innerText = name;
        document.getElementById('addPhotoForm').action = "/vente/produit/" + id + "/photo/add";
        
        const container = document.getElementById('photoListContainer');
        container.innerHTML = '';

        photos.forEach(photo => {
            const html = `
                <div class="col-4 photo-item">
                    <div class="card bg-transparent border-0">
                        <img src="/${photo.url_photo}" class="rounded border border-secondary" style="width: 100%; height: 100px; object-fit: cover;">
                        <form action="/vente/photo/${photo.id_photo_produit}/delete" method="POST" onsubmit="return confirm('Supprimer cette photo ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete-photo" title="Supprimer">
                                <i class="fas fa-times" style="font-size: 12px;"></i>
                            </button>
                        </form>
                    </div>
                </div>
            `;
            container.innerHTML += html;
        });

        document.getElementById('photoModal').style.display = 'block';
    }

    function closePhotoModal() {
        document.getElementById('photoModal').style.display = 'none';
    }

    // Fermer si on clique dehors
    window.onclick = function(event) {
        const modal = document.getElementById('photoModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
@endsection 