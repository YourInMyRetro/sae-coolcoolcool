@extends('layout')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-uppercase fw-bold text-danger mb-0">Produits en cours de création (US 40)</h1>
        <a href="{{ route('directeur.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour Dashboard
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($produitsSansPrix as $produit)
            <div class="col-md-6 mb-4">
                <div class="card border-danger h-100 shadow-sm">
                    <div class="card-header bg-danger text-white d-flex justify-content-between">
                        <span class="fw-bold">Réf: #{{ $produit->id_produit }}</span>
                        <span class="badge bg-white text-danger">Prix manquant</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $produit->nom_produit }}</h5>
                        <p class="card-text text-muted small">{{ Str::limit($produit->description_produit, 80) }}</p>
                        
                        <hr>
                        
                        <h6 class="fw-bold small text-uppercase text-secondary mb-2">Définir le prix de vente :</h6>
                        
                        <form action="{{ route('directeur.update_prix', $produit->id_produit) }}" method="POST" class="row g-2 align-items-end">
                            @csrf
                            
                            <div class="col-5">
                                <label class="form-label small fw-bold">Couleur principale</label>
                                <select name="id_couleur" class="form-select form-select-sm" required>
                                    @foreach($couleurs as $couleur)
                                        <option value="{{ $couleur->id_couleur }}">{{ $couleur->type_couleur }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-4">
                                <label class="form-label small fw-bold">Prix (€)</label>
                                <input type="number" step="0.01" name="prix_total" class="form-control form-control-sm" placeholder="0.00" required>
                            </div>

                            <div class="col-3">
                                <button type="submit" class="btn btn-sm btn-success w-100 fw-bold">
                                    <i class="fas fa-check"></i> Valider
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light text-center py-5 border">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <h4>Tout est à jour !</h4>
                    <p class="text-muted">Aucun produit en attente de prix.</p>
                    <a href="{{ route('directeur.dashboard') }}" class="btn btn-primary mt-2">Retour au Dashboard</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection