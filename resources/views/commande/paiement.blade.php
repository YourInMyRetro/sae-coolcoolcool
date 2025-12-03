@extends('layout')

@section('content')
<div class="container" style="max-width: 600px; padding: 40px 20px;">
    <h2 class="section-title">Paiement Sécurisé</h2>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
        <span style="font-size: 1.2rem; color: #666;">Montant à payer :</span>
        <div style="font-size: 2.5rem; font-weight: 800; color: #326295;">{{ number_format($total + 5, 2) }} €</div>
        <small style="color: #999;">(dont 5.00 € de frais de port)</small>
    </div>

    <form action="{{ route('commande.processPaiement') }}" method="POST">
        @csrf
        <input type="hidden" name="total_amount" value="{{ $total }}">

        {{-- Cartes Sauvegardées --}}
        @if(count($cartes) > 0)
            <div style="background: white; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; margin-bottom: 20px;">
                <div style="padding: 10px 15px; background: #eee; font-weight: bold;">Mes cartes</div>
                @foreach($cartes as $carte)
                    <label style="display: flex; align-items: center; padding: 15px; border-bottom: 1px solid #eee; cursor: pointer;">
                        <input type="radio" name="use_saved_card" value="{{ $carte->id_cb }}" style="margin-right: 15px;">
                        <span style="flex-grow: 1;">Carte **** **** **** {{ substr($carte->numero_chiffre, -4) }}</span>
                        <i class="fab fa-cc-visa" style="font-size: 1.5rem; color: #326295;"></i>
                    </label>
                @endforeach
                <label style="display: flex; align-items: center; padding: 15px; cursor: pointer;">
                    <input type="radio" name="use_saved_card" value="" checked style="margin-right: 15px;">
                    <span>Utiliser une autre carte</span>
                </label>
            </div>
        @endif

        {{-- Formulaire Nouvelle Carte --}}
        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 20px;">
            <div style="margin-bottom: 15px;">
                <label>Numéro de carte</label>
                <div style="position: relative;">
                    <input type="text" name="card_number" placeholder="0000 0000 0000 0000" style="width: 100%; padding: 12px; padding-left: 40px; border: 1px solid #ccc; border-radius: 4px;">
                    <i class="fas fa-credit-card" style="position: absolute; left: 12px; top: 14px; color: #999;"></i>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <label>Expiration</label>
                    {{-- Ajout de name="expiration" --}}
                    <input type="text" name="expiration" placeholder="MM/YY" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div style="flex: 1;">
                    <label>CVC / CVV</label>
                    {{-- Ajout de name="ccv" --}}
                    <input type="text" name="ccv" placeholder="123" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
            </div>

            {{-- Checkbox Sauvegarder la carte [ID 24] --}}
            <div style="display: flex; align-items: center; margin-top: 15px;">
                <input type="checkbox" id="save_card" name="save_card" value="1" style="width: 18px; height: 18px; margin-right: 10px;">
                <label for="save_card" style="font-size: 0.9rem; color: #555;">Sauvegarder cette carte pour mes prochains achats</label>
            </div>
        </div>

        {{-- BOUTON PAYER --}}
        <button type="submit" style="width: 100%; padding: 18px; background-color: #27ae60; color: white; border: none; font-weight: bold; font-size: 1.2rem; border-radius: 5px; cursor: pointer; transition: background 0.3s;">
            Payer et terminer <i class="fas fa-check"></i>
        </button>
    </form>
</div>
@endsection