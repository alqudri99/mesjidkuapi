<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Str;
use \App\Event;
use App\JenisEvent;
use \App\Location;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Routing\UrlGenerator;
use Laravel\Lumen\Routing\UrlGenerator;

class LocationController extends Controller
{
    public function addLocation(Request $request)
    {
        if ($request->filled(['location_name', 'lat', 'lng', 'alamat'])) {
            $location = Location::create([
                'location_name' => $request->input('location_name'),
                'lat' => $request->input('lat'),
                'lng' => $request->input('lng'),
                'alamat' => $request->input('alamat')
            ]);

            return response()->json([
                'status' => true,
                'messages' => 'Berhasil menambahkan lokasi!!',
                'data' => $location
            ]);
        } else {
            return response()->json([
            'status'=> false,
            'messages'=> 'Field tidak boleh kosong',
            'data'=> null
            ]);
        }
    }

    public function updateLocation(Request $request, $id)
    {
        $location = Location::where('id', '=', $id)->first();
        if($location){
            if ($request->filled(['location_name', 'lat', 'lng', 'alamat'])) {
                $location->update([
                    'location_name' => $request->input('location_name'),
                    'lat' => $request->input('lat'),
                    'lng' => $request->input('lng'),
                    'alamat' => $request->input('alamat')
                ]);
    
                return response()->json([
                    'status' => true,
                    'messages' => 'Berhasil Update lokasi!!',
                    'data' => $location
                ]);
            } else {
                return response()->json([
                'status'=> false,
                'messages'=> 'Field tidak boleh kosong',
                'data'=> null
                ]);
            }
        }else{
            return response()->json([
            'status'=> false,
            'messages'=> 'Not Found',
            'data'=> null
            ]);
        }
       
    }

    public function getAllLocationData(){
    return Location::orderBy('created_at', 'desc')->get();
    }


}
