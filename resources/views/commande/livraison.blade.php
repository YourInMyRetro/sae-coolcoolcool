@extends('layout')

@section('content')
<div class="container" style="max-width: 800px; padding: 40px 20px;">
    <h2 class="section-title">Mode de Livraison</h2>

    <form action="{{ route('commande.validerLivraison') }}" method="POST" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        @csrf
        
        {{-- Choix Adresse Existante --}}
        @if($adresses->count() > 0)
            <h4 style="color: #326295; margin-bottom: 15px;">Vos adresses enregistrées :</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px;">
                @foreach($adresses as $adr)
                    <label style="border: 2px solid #eee; padding: 15px; border-radius: 8px; cursor: pointer; transition: 0.2s;" onclick="document.querySelectorAll('label').forEach(l => l.style.borderColor='#eee'); this.style.borderColor='#326295';">
                        <input type="radio" name="id_adresse_existante" value="{{ $adr->id_adresse }}">
                        <strong>{{ $adr->rue }}</strong><br>
                        {{ $adr->code_postal_adresse }} {{ $adr->ville_adresse }}
                    </label>
                @endforeach
            </div>
            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">
        @endif

        {{-- Formulaire Nouvelle Adresse --}}
        <h4 style="color: #326295; margin-bottom: 15px;">Ou nouvelle adresse :</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <input type="text" name="rue" placeholder="Numéro et Rue" class="form-control" style="grid-column: span 2; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            <input type="text" name="code_postal_adresse" placeholder="Code Postal" class="form-control" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            <input type="text" name="ville_adresse" placeholder="Ville" class="form-control" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            <input type="text" name="pays_adresse" placeholder="Pays" class="form-control" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        {{-- BOUTON VALIDATION --}}
        <button type="submit" style="width: 100%; padding: 15px; background-color: #326295; color: white; border: none; font-weight: bold; font-size: 1.1rem; border-radius: 5px; cursor: pointer; margin-top: 10px;">
            Continuer vers le paiement <i class="fas fa-credit-card"></i>
        </button>
    </form>
</div>
@endsection