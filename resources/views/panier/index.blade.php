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
                                <input type="number" name="quantite" value="{{ $details['quantite'] }}" min="1" max="10" 
                                    style="width: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 4px;">
                                <button type="submit" style="background: #326295; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
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
        
        <div style="text-align: right; margin-top: 20px;">
            <h3>Total : {{ $total }} €</h3>
            <a href="{{ route('panier.vider') }}">Vider</a>
        </div>
    @else
        <div style="text-align: center; padding: 50px;">
            <h3>Votre panier est vide.</h3>
        </div>
    @endif
</div>
@endsection