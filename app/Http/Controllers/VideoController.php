<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $query = $request->query('query', ''); // TODO: Waiting for interviewer 

        $pageSize = 20;

        $startIndex = ($page - 1) * $pageSize;

        $url = env('VRP_URL', '');

        $response = Http::withoutVerifying()->get($url);

        if ($response->successful()) {

            $data = $response->json()['data'];

            $videos = array_slice($data, $startIndex, $pageSize);

            $xmlData = [
                'total_videos' => count($data),
                'videos' => $videos,
                'current_page' => $page,
                'last_page' => ceil(count($data) / $pageSize),
                'per_page' => $pageSize
            ];

            $xml = array2xml($xmlData, "video", false);

            return response($xml)->header('Content-Type', 'application/xml');
        }

        return response()->json(['error' => 'The videos could not be obtained'], 500);
    }


    public function show($id)
    {
        $url = env('VRP_URL', '');
        $response = Http::get($url . $id);

        if ($response->successful()) {
            return $response->json();
        }
        return response()->json(['error' => 'Video could not be found'], 404);
    }
}



function array2xml($array, $parentkey = "", $xml = false)
{

    if ($xml === false) {
        $xml = new SimpleXMLElement('<result/>');
    }

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            array2xml($value, is_numeric((string) $key) ? ("n" . $key) : $key, $xml->addChild(is_numeric((string) $key) ? $parentkey : $key));
        } else {
            $xml->addAttribute(is_numeric((string) $key) ? ("n" . $key) : $key, $value);
        }
    }

    return $xml->asXML();
}
