@extends('layout')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="card shadow-lg border-0 table-card" style="width: 100%; max-width: 450px; border-radius: 12px;">
        <div class="card-header bg-dark text-white text-center py-4">
            <h4 class="fw-bold mb-0" style="color: #00ff87;">Mot de passe oublié ?</h4>
        </div>
        <div class="card-body p-4 bg-light">
            <p class="text-muted small text-center mb-4">Entrez votre adresse email. Nous vous enverrons un lien pour définir un nouveau mot de passe.</p>

            @if(session('success'))
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success text-center mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase">Email associé au compte</label>
                    <input type="email" name="mail" class="form-control" placeholder="exemple@fifa.com" required>
                    @error('mail') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary fw-bold rounded-pill">Envoyer le lien</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="text-muted small text-decoration-none">Retour à la connexion</a>
            </div>
        </div>
    </div>
</div>
@endsection