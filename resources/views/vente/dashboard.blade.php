@extends('layout')

@section('content')
<div class="container" style="padding: 50px 0;">
    <h1>Espace Service Vente</h1>
    <div style="display: flex; gap: 20px; margin-top: 30px;">
        <a href="{{ route('vente.produit.create') }}" class="btn-fifa-cta" style="padding: 20px;">
            + Créer un Produit
        </a>
        <a href="{{ route('vente.categorie.create') }}" class="btn-fifa-cta" style="padding: 20px;">
            + Nouvelle Catégorie
        </a>
        <a href="{{ route('vente.produits.list') }}" class="btn-fifa-cta" style="padding: 20px;">
            Gérer Visibilité Produits
        </a>
    </div>
</div>
@endsection