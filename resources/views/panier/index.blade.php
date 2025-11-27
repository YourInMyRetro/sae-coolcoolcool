@extends('layout')

@section('content')
<div class="container">
    <h2 class="section-title">Votre Panier</h2>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif

    @if(count($panier) > 0)
        <table style="width: 100%; border-collapse: collapse; background: white; margin-top: 20px;">
            <thead style="background: var(--fifa-dark-blue); color: white;">
                <tr>
                    <th style="padding: 15px;">Produit</th>
                    <th style="padding: 15px;">Prix</th>
                    <th style="padding: 15px;">Quantité</th>
                    <th style="padding: 15px;">Total</th>
                    <th style="padding: 15px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($panier as $id => $details)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px; display: flex; align-items: center; gap: 10px;">
                            <img src="{{ asset($details['photo']) }}" width="50">
                            {{ $details['nom'] }}
                        </td>
                        <td style="padding: 15px; text-align: center;">{{ $details['prix'] }} €</td>
                        <td style="padding: 15px; text-align: center;">{{ $details['quantite'] }}</td>
                        <td style="padding: 15px; text-align: center; font-weight: bold;">{{ $details['prix'] * $details['quantite'] }} €</td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="{{ route('panier.supprimer', $id) }}" style="color: red; font-weight: bold;">X</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="text-align: right; margin-top: 20px;">
            <h3>Total : {{ $total }} €</h3>
            <a href="{{ route('panier.vider') }}" style="color: #666; margin-right: 15px;">Vider le panier</a>
            <button class="prod-btn" style="width: auto; padding: 10px 30px;">Commander</button>
        </div>
    @else
        <div style="text-align: center; padding: 50px;">
            <h3>Votre panier est vide.</h3>
            <a href="{{ route('produits.index') }}" class="prod-btn" style="width: auto; padding: 10px 20px;">Retourner à la boutique</a>
        </div>
    @endif
</div>
@endsection