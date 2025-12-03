<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tinder des Maillots - Mode Guerre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white p-4">

    <div class="container">
        <h2 class="text-center mb-4">CLASSIFICATION DES ASSETS</h2>
        
        @if(session('success')) <div class="alert alert-success text-center">{{ session('success') }}</div> @endif
        @if(session('warning')) <div class="alert alert-warning text-center">{{ session('warning') }}</div> @endif
        @if(session('error'))   <div class="alert alert-danger text-center">{{ session('error') }}</div> @endif

        <div class="row">
            <div class="col-md-7">
                <div class="card bg-secondary border-0">
                    <div class="card-body text-center">
                        <img src="{{ asset('temp_images/' . $currentFile) }}" class="img-fluid rounded" style="max-height: 500px; object-fit: contain;">
                        <p class="mt-2 text-white-50 monospace">{{ $currentFile }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card bg-light text-dark p-4 h-100">
                    
                    <form action="{{ route('matcher.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="old_filename" value="{{ $currentFile }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sélectionner le Produit :</label>
                            <select name="product_id" class="form-select" size="12" required>
                                @foreach($produits as $p)
                                    <option value="{{ $p->id_produit }}">{{ $p->nom_produit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100 mb-3 fw-bold">✅ VALIDER LE MATCH</button>
                    </form>

                    <hr>

                    <div class="row g-2">
                        <div class="col-6">
                            <form action="{{ route('matcher.skip') }}" method="POST">
                                @csrf
                                <input type="hidden" name="old_filename" value="{{ $currentFile }}">
                                <button type="submit" class="btn btn-warning w-100 fw-bold">⏭️ PASSER</button>
                            </form>
                        </div>

                        <div class="col-6">
                            <form action="{{ route('matcher.delete') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="old_filename" value="{{ $currentFile }}">
                                <button type="submit" class="btn btn-danger w-100 fw-bold">🗑️ POUBELLE</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>
</html>