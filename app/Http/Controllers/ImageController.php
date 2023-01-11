<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;

class ImageController extends Controller
{
    function uploadImage(ImageRequest $request) {
        $data = $request->validated();
        if(isset($data['image'])) {
            $imageName = md5(time());
            $ext = strtolower($data['image']->getClientOriginalExtension());
            $imageFullName = $imageName . '.' . $ext;
            $uploadPath = 'uploads/tmp/';

            $imageUrl = $uploadPath . $imageFullName;
            $success = $request->image->move('media/' . $uploadPath, $imageFullName);

            if($success) $response['image'] = '/' . $imageUrl;
            else $response['image'] = '';

            return response()->json(
                getResponseStructure($response)
            );
        }
    }
}
