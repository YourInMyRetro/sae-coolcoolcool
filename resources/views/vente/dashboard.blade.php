@extends('layout')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="text-white fw-bold text-uppercase display-4">Espace Service Vente</h1>
        <p class="text-muted">Gérez les produits, les catégories et les campagnes de vote</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-3">
            <a href="{{ route('vente.produit.create') }}" class="dashboard-btn-card">
                <i class="fas fa-box-open fa-2x mb-3 text-info"></i>
                <h3>Créer Produit</h3>
                <p>Ajouter un nouvel article</p>
            </a>
        </div>
        
        <div class="col-md-3">
            <a href="{{ route('vente.categorie.create') }}" class="dashboard-btn-card">
                <i class="fas fa-tags fa-2x mb-3 text-warning"></i>
                <h3>Nouvelle Catégorie</h3>
                <p>Créer un rayon</p>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('vente.produits.list') }}" class="dashboard-btn-card">
                <i class="fas fa-eye fa-2x mb-3 text-success"></i>
                <h3>Visibilité</h3>
                <p>Gérer produits & photos</p>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('vente.votation.list') }}" class="dashboard-btn-card highlight">
                <i class="fas fa-vote-yea fa-2x mb-3 text-dark"></i>
                <h3>Votations</h3>
                <p>Gérer les sondages</p>
            </a>
        </div>
    </div>
</div>

<style>
    .dashboard-btn-card {
        display: block;
        background-color: #1a202c;
        border: 1px solid #2d3748;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        text-decoration: none;
        color: white;
        height: 100%;
        transition: all 0.3s ease;
    }
    .dashboard-btn-card:hover {
        transform: translateY(-5px);
        background-color: #2d3748;
        color: white;
        border-color: #4a5568;
    }
    .dashboard-btn-card h3 {
        font-size: 1.25rem;
        font-weight: 800;
        margin-bottom: 10px;
        text-transform: uppercase;
    }
    .dashboard-btn-card p {
        font-size: 0.9rem;
        color: #a0aec0;
        margin: 0;
    }
    .dashboard-btn-card.highlight {
        background-color: #00cfb7;
        border-color: #00cfb7;
        color: #0f1623;
    }
    .dashboard-btn-card.highlight p { color: #1a202c; opacity: 0.8; }
    .dashboard-btn-card.highlight:hover { background-color: #00b39d; }
</style>
@endsection