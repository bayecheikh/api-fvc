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
    public function reset_password(Request $request)
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
                $link = 'https://admin-msas.vercel.app/?token='.$token;

                $mailData = ['data' => $link];

                /* Mail::send('mail',  ['data' => $link] , function($message) use($email)
                {   
                    $message->to($email)->subject('Réinitialisation mot de passe | MSAS');
                }); */
                Mail::to($email)->send(new NotifyMail($mailData));
                return response()->json(['message' => 'Veuillez vérifier votre boite de réception ('.$email.')'], 200);
            }
        }
        else {
            return response()->json(['message' => 'Email invalide'], 401);
        }
    }

    public function sendResetLinkResponse(Request $request)
    {
        $input = $request->only('email');
        $validator = Validator::make($input, [
            'email' => "required|email"
        ]);
        if ($validator->fails()) {
        return response(['errors'=>$validator->errors()->all()], 422);
        }
        $response =  Password::sendResetLink($input);
        if($response == Password::RESET_LINK_SENT){
        $message = "Mail send successfully";
        }else{
        $message = "Email could not be sent to this email address";
        }
        //$message = $response == Password::RESET_LINK_SENT ? 'Mail send successfully' : GLOBAL_SOMETHING_WANTS_TO_WRONG;
        $response = ['data'=>'','message' => $message];
        return response($response, 200);
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