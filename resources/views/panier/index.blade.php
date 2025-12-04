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
                        <td style="padding: 15px;">{{ $details['prix'] }} €</td>
                        
                        <td style="padding: 15px;">
                            <form action="{{ route('panier.update', $id) }}" method="POST" style="display: flex; gap: 5px; align-items: center;">
                                @csrf
                                @method('PATCH')
                                {{-- AVANT --}}

                                {{-- APRÈS --}}
                                <input type="number" name="quantite" value="{{ $details['quantite'] }}" min="1" max="{{ $details['stock_max'] }}" style="width: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 4px;">
                                    style="width: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 4px;">
                                <button type="submit" style="background: #326295; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <small style="color: #666; font-size: 0.8em;">Max: {{ $details['stock_max'] }}</small>
                            </form>
                        </td>
                        
                        <td style="padding: 15px;">{{ $details['prix'] * $details['quantite'] }} €</td>
                        <td style="padding: 15px;">
                            <a href="{{ route('panier.supprimer', $id) }}" style="color: red;">X</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="text-align: right; margin-top: 20px; display: flex; justify-content: flex-end; align-items: center; gap: 20px;">
    {{-- Bouton Vider (Existant, on le garde discret) --}}
    <a href="{{ route('panier.vider') }}" style="color: #e74c3c; text-decoration: underline; font-size: 0.9rem;">
        Vider le panier
    </a>
    <h3 style="margin: 0; font-size: 1.5rem; color: #326295;">Total : {{ $total }} €</h3>
    <a href="{{ route('commande.livraison') }}" class="btn-fifa-cta" style="padding: 12px 25px; text-decoration: none; background-color: #55e6c9; color: #0f2d4a; font-weight: 800; border-radius: 5px; text-transform: uppercase;">
        Commander <i class="fas fa-arrow-right"></i>
    </a>
</div>
    @else
        <div style="text-align: center; padding: 50px;">
            <h3>Votre panier est vide.</h3>
        </div>
    @endif
</div>
@endsection