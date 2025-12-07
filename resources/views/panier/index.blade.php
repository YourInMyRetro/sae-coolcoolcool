@extends('layout')

@section('content')
<div class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    
    <h1 style="color: #003366; border-bottom: 2px solid #ddd; padding-bottom: 15px; margin-bottom: 30px;">
        <i class="fas fa-shopping-cart"></i> Mon Panier
    </h1>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    @if(count($panier) > 0)
        <table style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <thead style="background: #326295; color: white;">
                <tr>
                    <th style="padding: 15px; text-align: left;">Produit</th>
                    <th style="padding: 15px; text-align: right;">Prix</th>
                    <th style="padding: 15px; text-align: center;">Quantité</th>
                    <th style="padding: 15px; text-align: right;">Total</th>
                    <th style="padding: 15px; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($panier as $id => $details)
                    <tr style="border-bottom: 1px solid #eee;">
                        {{-- Image et Nom --}}
                        <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                            <img src="{{ $details['photo'] }}" width="50" style="border-radius: 4px;">
                            <strong style="color: #003366;">{{ $details['nom'] }}</strong>
                        </td>
                        
                        {{-- Prix Unitaire --}}
                        <td style="padding: 15px; text-align: right;">
                            {{ number_format($details['prix'], 2) }} €
                        </td>
                        
                        {{-- Quantité (Correction de l'erreur ici) --}}
                        <td style="padding: 15px; text-align: center;">
                            <form action="{{ route('panier.update', $id) }}" method="POST" style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantite" value="{{ $details['quantite'] }}" min="1" max="{{ $details['stock_max'] }}" 
                                       style="width: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 4px; text-align: center;">
                                
                                <button type="submit" title="Mettre à jour" style="background: #326295; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer;">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                            <small style="color: #999; display: block; margin-top: 5px;">Stock max: {{ $details['stock_max'] }}</small>
                        </td>
                        
                        {{-- Total Ligne --}}
                        <td style="padding: 15px; text-align: right; font-weight: bold; color: #326295;">
                            {{ number_format($details['prix'] * $details['quantite'], 2) }} €
                        </td>

                        {{-- Suppression --}}
                        <td style="padding: 15px; text-align: center;">
                            <a href="{{ route('panier.supprimer', $id) }}" style="color: #e74c3c; font-size: 1.2rem;" title="Supprimer">
                                <i class="fas fa-times"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        {{-- Résumé et Boutons --}}
        <div style="margin-top: 30px; display: flex; justify-content: flex-end; align-items: center; flex-wrap: wrap; gap: 20px;">
            <a href="{{ route('panier.vider') }}" style="color: #e74c3c; text-decoration: underline;">
                Vider le panier
            </a>
            
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: right;">
                <h3 style="margin: 0 0 15px 0; color: #326295; font-size: 1.5rem;">
                    Total : {{ number_format($total, 2) }} €
                </h3>
                <a href="{{ route('commande.livraison') }}" class="btn-fifa-primary" style="padding: 12px 30px; background-color: #326295; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; text-transform: uppercase;">
                    Commander <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                </a>
            </div>
        </div>

    @else
        {{-- Panier Vide --}}
        <div style="text-align: center; padding: 60px; background: #f9f9f9; border-radius: 8px;">
            <i class="fas fa-shopping-basket" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #666;">Votre panier est vide.</h3>
            <a href="{{ route('produits.index') }}" class="btn-fifa-primary" style="margin-top: 20px; display: inline-block; padding: 10px 20px; background-color: #326295; color: white; text-decoration: none; border-radius: 5px;">
                Retourner à la boutique
            </a>
        </div>
    @endif
</div>
@endsection