@extends('layout')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="card shadow-lg border-0 table-card" style="width: 100%; max-width: 450px; border-radius: 12px;">
        <div class="card-header bg-dark text-white text-center py-4">
            <h4 class="fw-bold mb-0" style="color: #00d4ff;">Nouveau mot de passe</h4>
        </div>
        <div class="card-body p-4 bg-light">
            
            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase">Nouveau mot de passe</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                    @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary fw-bold rounded-pill">Valider le changement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection