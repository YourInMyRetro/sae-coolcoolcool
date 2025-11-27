@extends('layout')

@section('content')
<div class="container">
    <h2 class="section-title">Votre Panier</h2>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(count($panier) > 0)
        <table style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;">
            <thead style="background: var(--fifa-dark-blue); color: white;">
                <tr>
                    <th style="padding: 15px; text-align: left;">Produit</th>
                    <th style="padding: 15px;">Prix</th>
                    <th style="padding: 15px;">Quantité</th>
                    <th style="padding: 15px;">Total</th>
                    <th style="padding: 15px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($panier as $id => $details)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                            <img src="{{ asset($details['photo']) }}" width="60" height="60" style="object-fit: contain; background: #f4f4f4; border-radius: 4px;">
                            <span style="font-weight: 600; color: #333;">{{ $details['nom'] }}</span>
                        </td>
                        <td style="padding: 15px; text-align: center;">{{ number_format($details['prix'], 2) }} €</td>
                        <td style="padding: 15px; text-align: center;">
                            <span style="background: #eee; padding: 5px 10px; border-radius: 4px;">{{ $details['quantite'] }}</span>
                        </td>
                        <td style="padding: 15px; text-align: center; font-weight: bold; color: var(--fifa-blue);">
                            {{ number_format($details['prix'] * $details['quantite'], 2) }} €
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="{{ route('panier.supprimer', $id) }}" style="color: #e3342f; text-decoration: none; font-weight: bold; font-size: 1.2rem;">
                                <i class="fas fa-trash-alt"></i> &times;
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="display: flex; justify-content: flex-end; align-items: center; margin-top: 30px; gap: 20px;">
            <h3 style="margin: 0; font-size: 1.5rem;">Total : <span style="color: var(--fifa-blue); font-weight: 800;">{{ number_format($total, 2) }} €</span></h3>
            
            <a href="{{ route('panier.vider') }}" style="color: #666; text-decoration: underline;">Vider le panier</a>
            
            <button class="prod-btn" style="display: inline-block; width: auto; padding: 15px 40px; background-color: var(--fifa-cyan); color: var(--fifa-dark-blue); border: none; font-size: 1.1rem;">
                PASSER COMMANDE
            </button>
        </div>
    @else
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 8px;">
            <i class="fas fa-shopping-basket" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #555;">Votre panier est vide</h3>
            <p style="color: #888; margin-bottom: 30px;">Découvrez nos nouveaux maillots et accessoires.</p>
            <a href="{{ route('produits.index') }}" class="prod-btn" style="display: inline-block; width: auto; padding: 12px 30px;">
                Retourner à la boutique
            </a>
        </div>
    @endif
</div>
@endsection