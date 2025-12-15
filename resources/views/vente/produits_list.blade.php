@extends('layout')

@section('content')
<div class="container" style="padding: 50px 0;">
    <h1>Gestion des Produits</h1>
    <a href="{{ route('vente.dashboard') }}">Retour Dashboard</a>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; margin: 10px 0;">{{ session('success') }}</div>
    @endif

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #ddd;">
                <th style="padding: 10px; text-align: left;">ID</th>
                <th style="padding: 10px; text-align: left;">Nom</th>
                <th style="padding: 10px; text-align: left;">Catégorie</th>
                <th style="padding: 10px; text-align: left;">État Actuel</th>
                <th style="padding: 10px; text-align: left;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produits as $p)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;">{{ $p->id_produit }}</td>
                    <td style="padding: 10px;">{{ $p->nom_produit }}</td>
                    <td style="padding: 10px;">{{ $p->categorie->nom_categorie ?? '-' }}</td>
                    <td style="padding: 10px;">
                        @if($p->visibilite === 'visible')
                            <span style="color: green; font-weight: bold;">VISIBLE</span>
                        @else
                            <span style="color: gray;">CACHÉ</span>
                        @endif
                    </td>
                    <td style="padding: 10px;">
                        <form action="{{ route('vente.produit.visibilite', $p->id_produit) }}" method="POST">
                            @csrf
                            <button type="submit" style="cursor: pointer; padding: 5px 10px;">
                                @if($p->visibilite === 'visible')
                                    Cacher
                                @else
                                    Rendre Visible
                                @endif
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection