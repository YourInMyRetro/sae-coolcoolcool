@extends('layout')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="card shadow-lg border-0" style="width: 100%; max-width: 400px; border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-dark text-white text-center py-4">
            <h4 class="fw-bold mb-0" style="color: #00ff87;">Double Authentification</h4>
            <p class="small text-muted mb-0">Sécurité FIFA</p>
        </div>
        
        <div class="card-body p-4 bg-light">
            <div class="text-center mb-4">
                <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                <p class="text-dark">Un code de vérification a été envoyé par SMS à votre numéro.</p>
            </div>

            <form action="{{ route('login.2fa.verify') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="code" class="form-label fw-bold small text-uppercase">Code de sécurité</label>
                    <input type="text" 
                           class="form-control form-control-lg text-center fw-bold @error('code') is-invalid @enderror" 
                           id="code" 
                           name="code" 
                           placeholder="123456" 
                           maxlength="6" 
                           autofocus
                           style="letter-spacing: 5px; font-size: 1.5rem;">
                    
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary fw-bold py-2 text-uppercase" style="border-radius: 50px;">
                        Vérifier et se connecter
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="text-muted small text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la connexion
                </a>
            </div>
        </div>
    </div>
</div>
@endsection