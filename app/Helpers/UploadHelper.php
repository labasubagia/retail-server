<?php

namespace App\Helpers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class UploadHelper
{

    public static function imgBB($image)
    {
        $image = base64_encode(file_get_contents($image));
        $url = env('UPLOAD_IMAGE_URL') . '?key=' . env('UPLOAD_IMAGE_TOKEN');
        $response = Http::attach('image', $image)->post($url);
        $data = json_decode($response->body());
        $imageUrl = $data->data->url ?? null;
        if ($response->failed() || !$imageUrl) throw new Exception('Upload image failed');
        return $imageUrl;
    }
}
