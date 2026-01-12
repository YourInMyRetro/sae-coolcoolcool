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
                            @php
                                $photoUrl = $produit->photos->first() ? asset($produit->photos->first()->url_photo) : asset('img/placeholder.jpg');
                            @endphp
                            <img src="{{ $photoUrl }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td class="text-white fw-bold">{{ $produit->nom_produit }}</td>
                        <td class="text-muted">{{ optional($produit->categorie)->nom_categorie ?? 'N/A' }}</td>
                        <td class="text-warning fw-bold">{{ number_format($produit->prix_actuel(), 2) }} €</td>
                        <td>
                            <span class="badge {{ $produit->visibilite == 'visible' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($produit->visibilite) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button onclick="openPhotoModal({{ $produit->id_produit }}, {{ json_encode($produit->photos) }}, '{{ addslashes($produit->nom_produit) }}')" 
                                    class="btn btn-sm btn-info me-2" title="Gérer les photos">
                                <i class="fas fa-images"></i>
                            </button>

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
    <div class="modal-content-premium">
        <div class="modal-header-custom">
            <h3 class="m-0">Photos : <span id="modalProductName" class="text-fifa-cyan"></span></h3>
            <button onclick="closePhotoModal()" class="btn-close-custom"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="modal-body-custom">
            <div class="row g-3" id="photoListContainer"></div>

            <div class="upload-area mt-4">
                <form id="addPhotoForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="file-upload" class="custom-file-upload">
                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                        <span>Cliquez pour choisir une photo</span>
                    </label>
                    <input id="file-upload" type="file" name="photo" accept="image/*" onchange="this.form.submit()"/>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --fifa-dark: #0f1623;
        --fifa-card: #1a202c;
        --fifa-cyan: #00cfb7;
        --fifa-red: #e74c3c;
    }

    .modal-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(5px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content-premium {
        background-color: var(--fifa-dark);
        border: 1px solid #2d3748;
        width: 90%;
        max-width: 700px;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        animation: slideIn 0.3s ease-out;
    }

    .modal-header-custom {
        padding: 20px 30px;
        background-color: #151c2a;
        border-bottom: 1px solid #2d3748;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header-custom h3 {
        color: white;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .text-fifa-cyan { color: var(--fifa-cyan); }

    .btn-close-custom {
        background: none;
        border: none;
        color: #a0aec0;
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.2s;
    }
    .btn-close-custom:hover { color: white; }

    .modal-body-custom { padding: 30px; }

    .photo-card {
        background: #1a202c;
        padding: 10px;
        border-radius: 12px;
        position: relative;
        border: 1px solid #2d3748;
        transition: transform 0.2s;
    }
    .photo-card:hover { border-color: var(--fifa-cyan); }

    .photo-img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        background-color: #000; 
    }

    .btn-delete-absolute {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: var(--fifa-red);
        color: white;
        border: 2px solid var(--fifa-dark);
        width: 30px; height: 30px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        transition: transform 0.2s;
    }
    .btn-delete-absolute:hover { transform: scale(1.1); background-color: #c0392b; }

    input[type="file"] { display: none; }

    .custom-file-upload {
        border: 2px dashed #2d3748;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        cursor: pointer;
        border-radius: 12px;
        color: #a0aec0;
        transition: all 0.3s;
        background-color: rgba(255,255,255,0.02);
    }

    .custom-file-upload:hover {
        border-color: var(--fifa-cyan);
        color: var(--fifa-cyan);
        background-color: rgba(0, 207, 183, 0.05);
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    function openPhotoModal(id, photos, name) {
        document.getElementById('modalProductName').innerText = name;
        document.getElementById('addPhotoForm').action = "/service-vente/produit/" + id + "/photo/add";
        
        const container = document.getElementById('photoListContainer');
        container.innerHTML = '';

        if (photos.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-3">Aucune photo pour ce produit.</div>';
        }

        photos.forEach(photo => {
            // Construction propre de l'URL
            let imgSrc = photo.url_photo;
            if (!imgSrc.startsWith('http') && !imgSrc.startsWith('/')) {
                imgSrc = '/' + imgSrc;
            }

            const html = `
                <div class="col-md-4 col-6">
                    <div class="photo-card">
                        <img src="${imgSrc}" class="photo-img" onerror="this.src='/img/placeholder.jpg'">
                        <form action="/service-vente/photo/${photo.id_photo_produit}/delete" method="POST" onsubmit="return confirm('Supprimer cette photo ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete-absolute" title="Supprimer">
                                <i class="fas fa-trash-alt" style="font-size: 14px;"></i>
                            </button>
                        </form>
                    </div>
                </div>
            `;
            container.innerHTML += html;
        });

        document.getElementById('photoModal').style.display = 'flex';
    }

    function closePhotoModal() {
        document.getElementById('photoModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('photoModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
@endsection