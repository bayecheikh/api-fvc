<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Structure;

use Mail;

use App\Mail\NotifyMail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('role:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->hasRole('super_admin') || $request->user()->hasRole('admin_dprs')) {
            $users = User::with('roles')->with('structures')->paginate(10);
        } else {
            $structure_id = User::find($request->user()->id)->structures[0]->id;
            $users = User::with('roles')->with('structures')->whereHas('structures', function ($q) use ($structure_id) {
                $q->where('id', $structure_id);
            })->paginate(10);
        }

        $total = $users->total();

        return response()->json(["success" => true, "message" => "Liste des utilisateurs", "data" => $users, "total" => $total]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userMultipleSearch($term, Request $request)
    {
        if ($request->user()->hasRole('super_admin') || $request->user()->hasRole('admin_dprs')) {
            $users = User::where('id', 'like', '%' . $term . '%')->orWhere('email', 'like', '%' . $term . '%')->orWhere('name', 'like', '%' . $term . '%')->with('roles')->paginate(5);
        } else {
            $structure_id = User::find($request->user()->id)->structures[0]->id;
            $users = User::where('id', 'like', '%' . $term . '%')->orWhere('email', 'like', '%' . $term . '%')->orWhere('name', 'like', '%' . $term . '%')->with('roles')->whereHas('structures', function ($q) use ($structure_id) {
                $q->where('id', $structure_id);
            })->paginate(5);
        }

        return response()->json(["success" => true, "message" => "Liste des utilisateurs", "data" => $users]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function activeUser($id)
    {
        $user = User::find($id);

        $message = '';

        if ($user->status == 'actif') {
            $message = 'Utilisateur desactivé';
            $user->update([
                'status' => 'inactif'
            ]);
            //trouver et supprimer tout les token de l'utilisateur
            $userTokens = $user->tokens;
            foreach ($userTokens as $token) {
                $token->revoke();
            }
        } else {
            $message = 'Utilisateur activé';
            $user->update([
                'status' => 'actif'
            ]);
        }

        return response()->json(["success" => true, "message" => $message, "data" => $user]);
    }
    /**
     * Store a newly created resource in storagrolee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Génération du mot de passe
        $pwd = bin2hex(openssl_random_pseudo_bytes(4));

        try {

            // Création utilisateur
            $user = User::create([
                'name'       => $input['firstname'] . ' ' . $input['lastname'],
                'firstname'  => $input['firstname'],
                'lastname'   => $input['lastname'],
                'email'      => $input['email'],
                'telephone'  => $input['telephone'] ?? null,
                'status'     => 'actif',
                'password'   => bcrypt($pwd),
            ]);

            // Attacher structure
            if (!empty($input['structure_id'])) {
                $user->structures()->attach($input['structure_id']);
            }

            // Attacher rôles
            if (!empty($request->roles) && is_array($request->roles)) {
                $user->roles()->sync($request->roles);
            }

            // Envoi mail (sécurisé)
            try {
                $mailData = [
                    'data'     => $pwd,
                    'messages' => 'Votre mot de passe par défaut est :'
                ];

                Mail::to($user->email)->send(new NotifyMail($mailData));
            } catch (\Exception $e) {

                // On ne bloque PAS la création
            }

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès.',
                'data'    => $user
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l’utilisateur.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('roles')->with('structures')->get()->find($id);
        if (is_null($user)) {
            /*          return $this->sendError('Product not found.'); */
            return response()
                ->json(["success" => true, "message" => "Utilisateur introuvable."]);
        }
        return response()
            ->json(["success" => true, "message" => "Utilisateur trouvé avec succès.", "data" => $user]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
{
    $input = $request->all();

    $validator = Validator::make($input, [
        'firstname' => 'required',
        'lastname'  => 'required',
        'email'     => 'required|email|unique:users,email,' . $user->id,
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors'  => $validator->errors()
        ], 422);
    }

    try {
        // Mise à jour des informations de base
        $user->update([
            'name'       => $input['firstname'] . ' ' . $input['lastname'],
            'firstname'  => $input['firstname'],
            'lastname'   => $input['lastname'],
            'email'      => $input['email'],
            'telephone'  => $input['telephone'] ?? null,
            'fonction'   => $input['fonction'] ?? null,
        ]);

        // Gestion de la structure
        if (isset($input['structure_id'])) {
            // Synchroniser la structure (remplace toutes les structures existantes)
            // Si vous voulez permettre plusieurs structures, utilisez sync
            // Si vous voulez une seule structure, utilisez ceci :
            $user->structures()->sync([$input['structure_id']]);
        } else {
            // Si vous voulez retirer toutes les structures quand structure_id est absent
            // $user->structures()->detach();
            // Ou ne rien faire pour garder les structures existantes
        }

        // Gestion des rôles - méthode simplifiée et optimisée
        if (!empty($request->roles) && is_array($request->roles)) {
            $user->roles()->sync($request->roles);
        } else {
            // Si aucun rôle n'est envoyé, vous pouvez choisir de :
            // 1. Ne rien faire (garder les rôles existants) - commentez la ligne suivante
            // 2. Retirer tous les rôles - décommentez la ligne suivante
            // $user->roles()->detach();
        }

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur modifié avec succès.',
            'data'    => $user->load(['roles', 'structures']) // Charger les relations pour la réponse
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la modification de l\'utilisateur.',
            'error'   => env('APP_DEBUG') ? $e->getMessage() : null
        ], 500);
    }
}
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()
            ->json(["success" => true, "message" => "Utilisateur supprimé avec succès.", "data" => $user]);
    }
}
