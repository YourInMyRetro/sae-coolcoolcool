@extends('layout')

@section('content')
<div class="container" style="padding: 50px 0;">
    <h1>Créer une Catégorie</h1>
    <a href="{{ route('vente.dashboard') }}">Retour Dashboard</a>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; margin: 10px 0;">{{ session('success') }}</div>
    @endif

    <form action="{{ route('vente.categorie.store') }}" method="POST" style="margin-top: 20px; max-width: 500px;">
        @csrf
        <div style="margin-bottom: 15px;">
            <label>Nom de la catégorie :</label>
            <input type="text" name="nom_categorie" class="form-control" required style="width: 100%; padding: 8px;">
        </div>
        <button type="submit" class="btn-fifa-cta">Enregistrer</button>
    </form>

    <h3 style="margin-top: 40px;">Catégories existantes</h3>
    <ul>
        @foreach($categories as $cat)
            <li>{{ $cat->nom_categorie }}</li>
        @endforeach
    </ul>
</div>
@endsection