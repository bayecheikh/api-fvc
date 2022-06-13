<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
 
use Illuminate\Http\Request;
 
use App\Models\User;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;

use Mail;
 
use App\Mail\NotifyMail;
 
class AuthController extends Controller
{
    /**
     * Registration Req
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
  
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
  
        $token = $user->createToken('Laravel9PassportAuth')->accessToken;
  
        return response()->json(['token' => $token], 200);
    }
  
    /**
     * Login Req
     */
    public function login(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => "required|email",'password' => "required"
        ]);

        if ($validator->fails()) {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $user = User::where('email',$request->email)->first();
        if($user){
            if($user->status=='inactif')
            return response()->json(['message' => 'Votre compte n\'est pas activé'], 401);
        }

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
  
        if (auth()->attempt($data)) {
            $user = auth()->user();
            $user->load('roles.permissions');
            $token = auth()->user()->createToken('Laravel9PassportAuth')->accessToken;
            return response()->json(['token' => $token,'user' => $user], 200);
        } else {
            return response()->json(['message' => 'Utilisateur ou mot de passe incorrect'], 401);
        }
    }

    /**
     * Login Req
     */
    public function forget_password(Request $request)
    {
        $input = $request->only('email');
        $validator = Validator::make($input, [
            'email' => "required|email"
        ]);
        if ($validator->fails()) {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $user = User::where('email',$request->email)->first();
        if($user){
            if($user->status=='inactif')
            return response()->json(['message' => 'Votre compte n\'est pas activé. Veuillez contacter l\'administrateur'], 401);
            else{
                $email = $user->email;
                $token = $user->createToken($email)->accessToken;
                $link = env('FORGET_PW_FRONT_ENDPOINT').'/'.$token.'/'.$email;
                $messages = 'Cliquez sur le lien ci-dessous pour créer un nouveau mot de passe : ';
                $mailData = ['data' => $link, 'messages' => $messages];

                Mail::to($email)->send(new NotifyMail($mailData));

                return response()->json(['message' => 'Veuillez vérifier votre boite de réception ('.$email.')'], 200);
            }
        }
        else {
            return response()->json(['message' => 'Email invalide'], 401);
        }
    }

    /**
     * Login Req
     */
    public function update_password(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => "required|email",'password' => "required",'password_confirmation' => "required"
        ]);
        
        if ($validator->fails()) {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $user = User::where('email',$request->email)->first();
        if($user){
            $user ->update([
                'password' => bcrypt($request->password)
            ]);
            return response()->json(['message' => 'Mot de passe modifié avec succés'], 200);
        }
        else {
            return response()->json(['message' => 'Echec de la modification. Veuillez contacter l\'administrateur.'], 401);
        }
    }
 
 
    public function userInfo() 
    {
 
     $user = auth()->user();
      
     return response()->json(['user' => $user], 200);
 
    }

    public function logout() 
    {
 
     $user = auth()->user()->token()->revoke();
      
     return response()->json(['message' => 'Utilisateur déconnecté'], 200);
 
    }
}