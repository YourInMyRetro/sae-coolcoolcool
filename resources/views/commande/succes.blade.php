@extends('layout')

@section('content')
<div class="container section-spacer" style="text-align: center; padding: 100px 0;">
    <div style="margin-bottom: 30px;">
        <i class="fas fa-check-circle" style="font-size: 5rem; color: #27ae60;"></i>
    </div>
    
    <h1 style="text-transform: uppercase; font-weight: 900; color: #326295; margin-bottom: 20px;">Commande Confirmée !</h1>
    
    <p style="font-size: 1.2rem; margin-bottom: 40px; color: #555;">
        Merci pour votre achat. Votre commande a été validée avec succès.<br>
        Vous recevrez bientôt vos articles FIFA.
    </p>
    
    <a href="{{ route('home') }}" class="btn-fifa-cta" style="text-decoration: none; padding: 15px 30px; background-color: #326295; color: white; border-radius: 30px; font-weight: bold;">
        Retour à l'accueil
    </a>
</div>
@endsection