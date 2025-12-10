@extends('layout')

@section('content')
<div class="container mt-5">
    <h1 class="text-uppercase fw-bold text-danger mb-4">Produits en cours de création (US 40)</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($produitsSansPrix as $produit)
            <div class="col-md-6 mb-4">
                <div class="card border-danger h-100">
                    <div class="card-header bg-danger text-white d-flex justify-content-between">
                        <span>ID: {{ $produit->id_produit }}</span>
                        <span class="badge bg-white text-danger">Masqué</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $produit->nom_produit }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($produit->description_produit, 100) }}</p>
                        
                        <hr>
                        
                        <h6 class="fw-bold">Finaliser ce produit (US 54) :</h6>
                        <form action="{{ route('directeur.update_prix', $produit->id_produit) }}" method="POST" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-md-5">
                                <label class="form-label small">Couleur principale</label>
                                <select name="id_couleur" class="form-select form-select-sm" required>
                                    @foreach($couleurs as $couleur)
                                        <option value="{{ $couleur->id_couleur }}">{{ $couleur->type_couleur }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Prix de vente (€)</label>
                                <input type="number" step="0.01" name="prix_total" class="form-control form-control-sm" placeholder="Ex: 89.99" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-sm btn-success w-100">Valider</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Aucun produit incomplet trouvé. Tout est en ordre chef !
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection