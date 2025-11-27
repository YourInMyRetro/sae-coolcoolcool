<form action="{{ route('produits.index') }}" method="GET" class="mb-5" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        
        <div class="search-container" style="margin-bottom: 20px;">
            <input type="text" name="search" class="search-input" placeholder="Rechercher..." value="{{ request('search') }}">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </div>

        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            
            <div style="flex: 1; min-width: 150px;">
                <label style="font-weight: 600;">Couleur :</label>
                <select name="couleur" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="">Toutes</option>
                    @foreach($allColors as $c)
                        <option value="{{ $c }}" {{ request('couleur') == $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label style="font-weight: 600;">Taille :</label>
                <select name="taille" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="">Toutes</option>
                    @foreach($allSizes as $t)
                        <option value="{{ $t }}" {{ request('taille') == $t ? 'selected' : '' }}>{{ strtoupper($t) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label style="font-weight: 600;">Trier par :</label>
                <select name="sort" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="">Pertinence</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                </select>
            </div>

            <div style="width: 100%; text-align: right;">
                <a href="{{ route('produits.index') }}" style="color: #666; text-decoration: underline;">Réinitialiser les filtres</a>
            </div>
        </div>
    </form>