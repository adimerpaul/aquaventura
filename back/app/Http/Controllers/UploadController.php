<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class UploadController extends Controller{
    public function upload(Request $request,$id){
        $fileName = substr(json_encode($request->all()), 2, -5);
        $file = $request->file($fileName);
        $name = time().$file->getClientOriginalName();
        $ruta=public_path('/images/'.$name);
        Image::make($file->getRealPath())
            ->resize(300,300
                ,function ($constraint){
                    $constraint->aspectRatio();
                }
            )
            ->save($ruta,72);
        $card = Card::find($id);
        $card->photo = $name;
        $card->save();
        return $name;
    }
    public function base64($photo){
        $path = public_path('/images/'.$photo);
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}
