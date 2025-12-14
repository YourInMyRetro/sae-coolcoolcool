@extends('layout')

@section('content')
<div class="container py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h6 class="text-uppercase text-danger fw-bold mb-1">Action Requise</h6>
            <h1 class="text-white fw-bold">Produits en Attente de Prix</h1>
        </div>
        <a href="{{ route('directeur.dashboard') }}" class="btn btn-outline-light rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Retour Dashboard
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4 rounded-3 shadow-lg">
            <i class="fas fa-check-circle fa-2x me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="row g-4">
        @forelse($produitsSansPrix as $produit)
            <div class="col-md-6 col-lg-4">
                <div class="dashboard-card position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100 h-1 bg-danger"></div>
                    
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger">
                            ID #{{ $produit->id_produit }}
                        </span>
                        <i class="fas fa-box-open text-muted fa-2x opacity-25"></i>
                    </div>

                    <h4 class="text-white mb-2">{{ $produit->nom_produit }}</h4>
                    <p class="text-muted small mb-4" style="min-height: 40px;">
                        {{ Str::limit($produit->description_produit, 60) }}
                    </p>
                    
                    <form action="{{ route('directeur.update_prix', $produit->id_produit) }}" method="POST">
                        @csrf
                        <div class="bg-dark bg-opacity-50 p-3 rounded-3 border border-secondary border-opacity-25">
                            
                            <label class="text-muted small fw-bold text-uppercase mb-2">Fixer le Prix (€)</label>
                            
                            <div class="input-group">
                                <input type="number" step="0.01" name="prix_total" class="form-control input-dark fw-bold text-white" placeholder="0.00" required>
                                <button type="submit" class="btn btn-fifa-cyan fw-bold shadow-sm">
                                    OK
                                </button>
                            </div>
                            
                        </div>
                    </form>

                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="dashboard-card text-center py-5">
                    <div class="mb-4 text-success opacity-75">
                        <i class="fas fa-clipboard-check fa-5x"></i>
                    </div>
                    <h2 class="text-white">Tout est en ordre !</h2>
                    <p class="text-muted">Aucun produit en attente de validation.</p>
                    <a href="{{ route('directeur.dashboard') }}" class="btn btn-outline-light mt-3 rounded-pill">
                        Retourner au Dashboard
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection