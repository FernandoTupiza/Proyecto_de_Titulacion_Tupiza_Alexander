<?php

namespace App\Http\Controllers\Semestres;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Semestres;
use App\Http\Resources\Resource;
use App\Models\Carreras;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Response;


class SemestresAdminController extends Controller
{
    //---------FUNCION PARA CREAR LOS SEMESTRES-------------//
    public function store_admin (Request $request, Carreras $carreras)
    {
        $response = Gate::inspect('gestion-semestres-admin');

        if($response->allowed())
        {
            
        $doc = $request -> validate([
            'nombre' => ['required', 'string', 'min:4', 'max:100'] ,
            'descripcion' => ['required', 'string', 'min:4', 'max:250'],
            'path' => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:1000'],

        ]);

        $doc1 = $doc [ 'path'];
        $uploadedFileUrl = Cloudinary::upload($doc1->getRealPath())->getSecurePath();
        $semestres = new Semestres($request->all());
        $semestres ->carreras_id = $carreras->id;
        $semestres->path= $uploadedFileUrl;


        $semestres->nombre= $request->nombre;
        $semestres->descripcion = $request->descripcion;
        $semestres->save();
  
        return $this->sendResponse('Semestres subido  satisfactoriamente');

        }else{
            echo $response->message();
        }

        
    }

        //-----------------------------------------------------//

        //-------FUNCION PARA VER LOS SEMESTRES ACTIVOS--------//
        public function index_admin (){

            $semestres = Semestres::where('estado',1)->get();

            return response()->json([
                'data'=> $semestres

            ]);
            

    }
    //-----------------------------------------------------//


    //---FUNCION PARA VER TODOS LOS SEMESTRES CREADOS-----//
    public function index_adminE (){
        $response = Gate::inspect('gestion-semestres-admin');

        if($response->allowed())
        {
            $semestres = Semestres::all();

            return response()->json([
                'data'=> $semestres

            ]);
        }else{
            echo $response->message();
        }
    }
    //-----------------------------------------------------//


    //FUNCION PARA VER UN SEMESTRE
    public function show_admin ( $id){
        $semestres = Semestres::find($id);
        if($semestres){
            return response()->json([
                'data'=> $semestres

            ]);
        }else{
            return response()->json([
                'message' => 'No existe ningun semestre con ese id.',

    
            ], 404);
        }
    }


    //FUNCION PARA ACTUALIZAR LOS SEMESTRES

    public function update_admin(Request $request, Semestres $semestres)
    {
        $response = Gate::inspect('gestion-documentos-admin');

        if($response->allowed())
        {
            $doc=$request -> validate([
            'nombre' => ['string', 'min:4', 'max:100'] ,
            'descripcion' => ['string', 'min:4', 'max:250'],
            'path' => ['nullable','image', 'mimes:jpg,png,jpeg', 'max:1000'],

            ]);
            if($request->has('path')){

                $doc1 = $doc [ 'path'];

                $uploadedFileUrl = Cloudinary::upload($doc1->getRealPath())->getSecurePath();
                $semestres -> update( [
                    'path'  => $uploadedFileUrl,
                ]);
            }
            $semestres -> update( [
                'nombre'  =>  $request->nombre,
                'descripcion'  =>  $request->descripcion,
            ]);
            return $this->sendResponse('Documento se ha actualizado satisfactoriamente');
        }else{
            echo $response->message();
        }
    }

    //ACTIVAR E INACTIVAR SEMESTRE

    
    public function destroy_admin (Semestres $semestres){


        // $carreras = Carreras::find($id);
        $response = Gate::inspect('gestion-carreras-admin');

        if($response->allowed())
        {
            $semestres_estado = $semestres->estado;
            $mensaje = $semestres_estado ? "Inactiva":"Activa";
            $semestres->estado = !$semestres_estado;
            $semestres->save();

            return $this->sendResponse(message: "El semestres esta $mensaje ");
        }else{
            echo $response->message();
        }
    

    }





}
