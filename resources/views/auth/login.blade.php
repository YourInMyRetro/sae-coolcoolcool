@extends('layout')

@section('content')
<div class="container section-spacer" style="max-width: 500px; padding: 60px 20px;">
    <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2 class="section-title" style="text-align: center; margin-bottom: 30px;">Connexion</h2>

        <form action="{{ route('authenticate') }}" method="POST">
            @csrf
            
            {{-- Email --}}
            <div style="margin-bottom: 20px;">
                <label style="font-weight: bold; display: block; margin-bottom: 8px;">Adresse Email</label>
                <input type="email" name="mail" class="search-input" style="width: 100%; border: 1px solid #ccc; background: white; color: black;" value="{{ old('mail') }}" required autofocus>
                @error('mail')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Mot de passe --}}
            <div style="margin-bottom: 30px;">
                <label style="font-weight: bold; display: block; margin-bottom: 8px;">Mot de passe</label>
                <input type="password" name="password" class="search-input" style="width: 100%; border: 1px solid #ccc; background: white; color: black;" required>
            </div>

            <button type="submit" class="btn-fifa-cta" style="width: 100%; border: none; cursor: pointer; justify-content: center;">
                Se Connecter
            </button>
        </form>

        {{-- AJOUT : Section Création de Compte (Même DA) --}}
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center;">
            <p style="margin-bottom: 15px; font-weight: bold; color: #555;">Nouveau sur FIFA Store ?</p>
            
            <div style="display: flex; flex-direction: column; gap: 10px;">
                {{-- Bouton Créer Compte Particulier --}}
                <a href="{{ route('register.form') }}" class="btn-fifa-cta" style="width: 100%; text-decoration: none; justify-content: center; background-color: #326295; color: white;">
                    Créer un compte Particulier
                </a>

                {{-- Bouton Créer Compte Pro --}}
                <a href="{{ route('register.pro.form') }}" class="btn-fifa-cta" style="width: 100%; text-decoration: none; justify-content: center; background-color: #fff; color: #326295; border: 2px solid #326295;">
                    Créer un compte PRO
                </a>
            </div>
        </div>

    </div>
</div>
@endsection