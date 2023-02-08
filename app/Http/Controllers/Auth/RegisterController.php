<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Validation\Rules\Password as PasswordValidator;
use Illuminate\Support\Facades\Auth;

// use App\Http\Controllers\Auth\Validator;

class RegisterController extends Controller
{

    public function registro(Request $request)
    {
        $fields = $request->validate([
            'role_id' => 'required|numeric',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => [
                'required', 'string', 'confirmed',
                PasswordValidator::defaults()->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $user = User::create([
            'role_id' => $fields['role_id'],
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        //$token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'message' => 'Usuario registrado satisfactoriamente'
            //'token' => $token
        ];

        return response($response, 201);
    }


    //FUNCION PARA VER LOS USUARIOS
         public function index_usuario (){

            $users= User::all();
            // $user = Auth::user();
            return $this->sendResponse(message: 'lista',result:[
                'users' => UserResource::collection($users)
            ]);
 
        
        }

    //FUNCION PARA VER UN USUARIO
        public function show_usuario ($id){

            $usuarios = User::find($id);
            $user = Auth::user();
            if($usuarios){
                return response()->json([
                    'mesagge' => 'Usuario a vizualizarse',
                    'data'=> $usuarios,
                    'avatar' => $user->getAvatarPath()
        
                ]);
            }else{
                return response()->json([
                    'message' => 'No existe ninguna carrera con ese id.',

        
                ], 404);

            }
        }

}
