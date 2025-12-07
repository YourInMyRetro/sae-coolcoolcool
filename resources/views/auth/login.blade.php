@extends('layout')

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-card-premium">
        
        <div class="auth-header">
            <h1>Connexion</h1>
            <p>Accédez à votre espace membre FIFA.</p>
        </div>

        @if ($errors->any())
            <div style="background: #ffe6e6; color: #d8000c; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 0.9em;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('authenticate') }}" method="POST">
            @csrf
            
            <div class="fifa-form-group">
                <label for="mail">Adresse Email</label>
                <input type="email" name="mail" id="mail" class="fifa-input" value="{{ old('mail') }}" required autofocus placeholder="exemple@email.com">
            </div>

            <div class="fifa-form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" class="fifa-input" required placeholder="********">
            </div>

            <div class="fifa-form-group" style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <input type="checkbox" name="remember" id="remember" style="width: 18px; height: 18px; accent-color: #326295; cursor: pointer;">
                <label for="remember" style="margin: 0; cursor: pointer; color: #555; text-transform: none; font-weight: normal;">
                    Se souvenir de moi
                </label>
            </div>

            <button type="submit" class="btn-fifa-submit">
                Se connecter <i class="fas fa-sign-in-alt" style="margin-left: 8px;"></i>
            </button>
        </form>

        {{-- Section Création de Compte --}}
        <div class="auth-footer-links">
            <p style="margin-bottom: 10px; font-weight: bold;">Nouveau sur FIFA Store ?</p>
            <a href="{{ route('register.form') }}">Créer un compte Particulier</a>
            <br>
            <span style="color: #ccc;">|</span>
            <br>
            <a href="{{ route('register.pro.form') }}" style="color: #008eb3;">Créer un compte PRO</a>
        </div>

    </div>
</div>
@endsection