<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Professionel;
use App\Models\Panier;
use App\Models\StockArticle; 
use Carbon\Carbon;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    
    public function login() {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'mail' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 1. On vérifie les identifiants SANS connecter l'utilisateur
        // Note: On adapte 'password' vers 'mot_de_passe_chiffre' si votre AuthProvider n'est pas standard,
        // mais ici on fait une vérification manuelle pour être sûr.
        $user = User::where('mail', $credentials['mail'])->first();

        if ($user && Hash::check($credentials['password'], $user->mot_de_passe_chiffre)) {
            
            // 2. Vérification 2FA
            if ($user->double_auth_active) {
                // L'option est active : on génère un code et on redirige vers la page 2FA
                $this->send2FACode($user);
                
                // On stocke l'ID de l'utilisateur temporairement en session
                Session::put('2fa:user_id', $user->id_utilisateur);
                
                return redirect()->route('login.2fa.form');
            }

            // 3. Pas de 2FA : Connexion classique
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('/compte');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('mail');
    }

    public function show2FAForm()
    {
        if (!Session::has('2fa:user_id')) {
            return redirect()->route('login');
        }
        return view('auth.login_2fa');
    }

    public function verify2FA(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        if (!Session::has('2fa:user_id')) {
            return redirect()->route('login');
        }

        $userId = Session::get('2fa:user_id');
        $user = User::find($userId);

        // Vérification du code
        if ($user && $user->code_auth_temporaire == $request->code) {
            
            // Vérification expiration (10 min)
            if (Carbon::now()->greaterThan($user->code_auth_expiration)) {
                return back()->withErrors(['code' => 'Le code a expiré. Veuillez vous reconnecter.']);
            }

            // Succès : On connecte réellement l'utilisateur
            Auth::login($user);
            $request->session()->regenerate();
            
            // Nettoyage session et DB
            Session::forget('2fa:user_id');
            $user->code_auth_temporaire = null;
            $user->code_auth_expiration = null;
            $user->save();

            return redirect()->intended('/compte');
        }

        return back()->withErrors(['code' => 'Code incorrect.']);
    }

    private function send2FACode($user)
    {
        $code = rand(100000, 999999);
        $user->code_auth_temporaire = $code;
        $user->code_auth_expiration = Carbon::now()->addMinutes(10);
        $user->save();

        try {
            $telClean = preg_replace('/[^0-9]/', '', $user->telephone);
            if (str_starts_with($telClean, '0')) $telClean = '+33' . substr($telClean, 1);
            if (!str_starts_with($telClean, '+')) $telClean = '+' . $telClean;

            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $messagingServiceSid = env('TWILIO_MESSAGING_SERVICE_SID');

            $client = new Client($sid, $token);
            $client->messages->create($telClean, [
                "messagingServiceSid" => $messagingServiceSid,
                "body" => "FIFA LOGIN : Votre code de connexion est $code."
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur SMS Login : " . $e->getMessage());
            // En dev, on continue même si le SMS échoue (on regardera la BDD ou les logs)
        }
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['mail' => 'required|email|exists:utilisateur,mail']);

        // Création du token
        $token = Str::random(64);

        // Insertion dans la table standard de Laravel (password_reset_tokens)
        // Attention : vérifiez que cette table existe (créée par la migration par défaut)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->mail],
            [
                'email' => $request->mail,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Lien de réinitialisation
        $link = route('password.reset', ['token' => $token, 'email' => $request->mail]);

        // Envoi du Mail (Simulé ou Réel)
        try {
            Mail::send([], [], function ($message) use ($request, $link) {
                $message->to($request->mail)
                        ->subject('FIFA - Réinitialisation de votre mot de passe')
                        ->html("
                            <h3>Bonjour,</h3>
                            <p>Une demande de réinitialisation de mot de passe a été effectuée pour votre compte FIFA.</p>
                            <p>Cliquez sur le lien ci-dessous pour changer votre mot de passe :</p>
                            <a href='$link' style='background: #00ff87; color: #000; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Réinitialiser mon mot de passe</a>
                            <p><small>Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.</small></p>
                        ");
            });
            return back()->with('success', 'Lien de réinitialisation envoyé par email ! (Vérifiez vos spams ou les logs si en local)');
        } catch (\Exception $e) {
            // En cas d'erreur mail (souvent en local), on logue le lien pour que vous puissiez tester quand même
            Log::info("LIEN RESET PASSWORD : " . $link);
            return back()->with('success', 'Le lien de réinitialisation a été envoyé avec succès !');
        }
    }

    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => request()->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:utilisateur,mail',
            'password' => 'required|min:8|confirmed', // Le champ de confirmation doit s'appeler 'password_confirmation'
            'token' => 'required'
        ]);

        // Vérification du token en base
        $resetRecord = DB::table('password_reset_tokens')
                            ->where('email', $request->email)
                            ->where('token', $request->token)
                            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Lien de réinitialisation invalide ou expiré.']);
        }

        // Mise à jour du mot de passe utilisateur
        User::where('mail', $request->email)
            ->update(['mot_de_passe_chiffre' => Hash::make($request->password)]);

        // Suppression du token utilisé
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Mot de passe modifié avec succès ! Vous pouvez vous connecter.');
    }

    // 1. Redirige l'utilisateur vers Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Google renvoie l'utilisateur ici
    public function handleGoogleCallback()
    {
        try {
            // Récupération des infos Google
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // On cherche si l'utilisateur existe déjà (par ID Google ou email)
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('mail', $googleUser->email)
                        ->first();

            if ($user) {
                // IL EXISTE : On le connecte
                
                // Petit fix : si c'est la 1ère fois qu'il se connecte via Google mais qu'il avait déjà un compte mail
                if (!$user->google_id) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                }

                Auth::login($user);
                return redirect()->intended('/compte');

            } else {
                // IL N'EXISTE PAS : On crée le compte
                // Attention : Il faudra peut-être remplir des champs obligatoires avec des valeurs par défaut
                
                // On sépare le nom/prénom si possible
                $parts = explode(' ', $googleUser->name, 2);
                $prenom = $parts[0];
                $nom = $parts[1] ?? '';

                $newUser = User::create([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'mail' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'mot_de_passe_chiffre' => Hash::make(Str::random(16)), // MDP aléatoire sécurisé
                    'role' => 'client',
                    'pays_naissance' => 'Inconnu', // Valeur par défaut requise par ta BDD
                    'date_naissance' => '2000-01-01', // Valeur par défaut
                    'langue' => 'Français'
                ]);

                Auth::login($newUser);
                return redirect()->intended('/compte');
            }

        } catch (\Exception $e) {
            Log::error("Erreur Google Login : " . $e->getMessage());
            return redirect()->route('login')->withErrors(['email' => 'Erreur de connexion avec Google.']);
        }
    }



    // deco
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // creation compte
    public function showRegisterForm() { return view('auth.register'); }

    public function register(Request $request) {
       
        $request->validate([
            'nom' => 'required|max:50', 
            'prenom' => 'required|max:50',
            'mail' => 'required|email|unique:utilisateur', 
   
            'telephone' => ['nullable', 'string', 'min:10', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'date_naissance' => 'required|date|before:today|after:-120 years',
            'pays_naissance' => 'required|max:50', 
            'langue' => 'required|max:50',
            'password' => 'required|min:8|confirmed',
            'cgu_consent' => 'accepted',
            'newsletter_optin' => 'nullable',
        ], [
            'cgu_consent.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
            'password.min' => 'Le mot de passe doit faire au moins 8 caractères.',
            'date_naissance.after' => 'Date de naissance invalide.',
            'telephone.regex' => 'Le format du numéro de téléphone est invalide.',
            'telephone.min' => 'Le numéro de téléphone est trop court.'
        ]);


        $user = new User();
        $user->nom = $request->input('nom');
        $user->prenom = $request->input('prenom');
        $user->mail = $request->input('mail');

        $user->telephone = $request->input('telephone');
        
        $user->date_naissance = $request->input('date_naissance');
        $user->pays_naissance = $request->input('pays_naissance');
        $user->langue = $request->input('langue');
        $user->surnom = $request->input('surnom') ?? $request->input('prenom');
        $user->mot_de_passe_chiffre = Hash::make($request->input('password'));
        
        $user->newsletter_optin = $request->has('newsletter_optin'); 

        $user->save();

        Auth::login($user);
        return redirect()->route('home')->with('success', 'Bienvenue sur FIFA Store !');
    }

    // creation compte pto
    public function showRegisterProForm() { return view('auth.register_pro'); }

    public function registerPro(Request $request) {
        $request->validate([
            'nom' => 'required|max:50', 
            'prenom' => 'required|max:50',
            'mail' => 'required|email|unique:utilisateur', 
            'password' => 'required|min:8|confirmed',
            'nom_societe' => 'required|max:100', 
            'numero_tva' => 'required|unique:professionel,numero_tva_intracommunautaire',
            'activite' => 'required|max:100', 

            'date_naissance' => 'required|date|before:today|after:-120 years', 
            'pays_naissance' => 'required|max:50', 
            'langue' => 'required|max:50',
            'cgu_consent' => 'accepted',
        ], [
            'date_naissance.after' => 'Veuillez saisir une date de naissance valide (moins de 120 ans).'
        ]);

        $user = new User();
        $user->nom = $request->input('nom'); 
        $user->prenom = $request->input('prenom');
        $user->mail = $request->input('mail'); 
        $user->date_naissance = $request->input('date_naissance');
        $user->pays_naissance = $request->input('pays_naissance'); 
        $user->langue = $request->input('langue');
        $user->mot_de_passe_chiffre = Hash::make($request->input('password'));
        
        $user->newsletter_optin = $request->has('newsletter_optin');
        
        $user->save();

        $pro = new Professionel();
        $pro->id_utilisateur = $user->id_utilisateur;
        $pro->nom_societe = $request->input('nom_societe');
        $pro->activite = $request->input('activite');
        $pro->numero_tva_intracommunautaire = $request->input('numero_tva');
        $pro->save();

        Auth::login($user);
        return redirect()->route('home')->with('success', 'Compte Pro créé !');
    }

    private function restoreCartFromDatabase($user) {
        $dbPanier = Panier::with(['lignes'])->where('id_utilisateur', $user->id_utilisateur)->first();
        if (!$dbPanier) return;
        $sessionPanier = session('panier', []);
        foreach ($dbPanier->lignes as $ligne) {
            $stock = StockArticle::with(['produitCouleur.produit.photos', 'produitCouleur.couleur', 'taille'])->find($ligne->id_stock_article);
            if ($stock) {
                $p = $stock->produitCouleur->produit;
                $key = $p->id_produit . '-' . $stock->produitCouleur->couleur->id_couleur . '-' . $stock->taille->id_taille;
                if (!isset($sessionPanier[$key])) {
                    $sessionPanier[$key] = [
                        "nom" => $p->nom_produit . " (" . ucfirst($stock->produitCouleur->couleur->type_couleur) . " - " . strtoupper($stock->taille->type_taille) . ")",
                        "quantite" => $ligne->quantite, "prix" => $stock->produitCouleur->prix_total,
                        "photo" => $p->photos->first()->url_photo ?? 'img/placeholder.jpg', "id_stock" => $stock->id_stock_article
                    ];
                } else { $sessionPanier[$key]['quantite'] = max($sessionPanier[$key]['quantite'], $ligne->quantite); }
            }
        }
        session()->put('panier', $sessionPanier);
    }
}