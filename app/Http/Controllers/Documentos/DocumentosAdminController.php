<?php

namespace App\Http\Controllers\Documentos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Documentos;
use App\Models\Materias;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class DocumentosAdminController extends Controller
{
    //FUNCION PARA SUBIR UN DOCUMENTO.
    public function store_admin (Request $request, Materias $materias)
    {
        $response = Gate::inspect('gestion-documentos-admin');

        if($response->allowed())
        {
            $doc=$request -> validate([

                'nombre_doc'  => ['required', 'string', 'min:5', 'max:30'],
                'documentos' => ['required', 'file', 'mimes:pdf', 'max:20000'],

            ]);
            $doc1 = $doc [ 'documentos'];
            $uploadedFileUrl = Cloudinary::uploadFile($doc1->getRealPath())->getSecurePath();
            $documentos = new Documentos($request->all());
            $documentos->materias_id = $materias->id;
        
            $documentos->path= $uploadedFileUrl;
            $documentos->save();
            
            return $this->sendResponse('Documento subido  satisfactoriamente');
        }else{
            echo $response->message();
        }
    }

    // FUNCION PARA ACTUALIZAR EL DOCUMENTO
    public function update_admin(Request $request, Documentos $documentos)
    {
        $response = Gate::inspect('gestion-documentos-admin');

        if($response->allowed())
        {
            $doc=$request -> validate([
                'nombre_doc'  => ['nullable','string', 'min:5', 'max:30'],
                'documentos' => ['nullable','file', 'mimes:pdf', 'max:200000'],
            ]);
            if($request->has('documentos')){

                $doc1 = $doc [ 'documentos'];

                $uploadedFileUrl = Cloudinary::uploadFile($doc1->getRealPath())->getSecurePath();
                $documentos -> update( [
                    'path'  => $uploadedFileUrl,
                ]);
            }
            $documentos -> update( [
                'nombre_doc'  =>  $request->nombre_doc,
            ]);
            return $this->sendResponse('Documento se ha actualizado satisfactoriamente');
        }else{
            echo $response->message();
        }
    }

    //FUNCION PARA VER UN DOCUMENTO MEDIANTE EL ID
    public function show_admin ($id){
        $response = Gate::inspect('gestion-documentos-admin');

        if($response->allowed())
        {
            $documentos = Documentos::find($id);
            if($documentos){
            return response()->json([
                'mesagge' => 'Path del documento a vizualizarse',
                'data'=> $documentos

            ]);
            }else{
                return response()->json([
                    'message' => 'No existe ninguna path con ese id.',


                ], 404);

            }
        }else{
            echo $response->message();
        }
    }
    
    //FUNCION PARA VER TODOS LOS DOCUMENTOS CREADOS

    public function index_admin (Request $request){

        $documentos = Documentos::all();
        return response()->json([
            'data'=> $documentos

        ]);

    }

    //FUNCION PARA ELIMINAR DOCUMENTOS

    public function delete_admin ($id){

        $response = Gate::inspect('gestion-documentos-admin');

        if($response->allowed())
        {
            $documentos = Documentos::find($id);


            if($documentos){
                $documentos->delete();

                return response()->json([
                    'message'=> 'El documento se ha eliminado satisfactoriamente'
                ]);
            }
            else{
                return response()->json([
                    'message'=> 'No existe ninguna documento con ese id.'
        
                ], 404);

            }

        }else{
            echo $response->message();
        }

    }

}
