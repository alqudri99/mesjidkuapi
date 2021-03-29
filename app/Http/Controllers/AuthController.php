<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Str;
use \App\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function ll()
    {
        return route('ram');
    }
    public function register(Request $request)
    {
        // return $request;
        if ($request->filled(['name', 'email', 'no_hp', 'gender', 'password', 'role', 'status'])) {

            $filename = null;
            $email = $request->input('email');
            $nohp = $request->input('no_hp');

            $nohpcheck = User::where('no_hp', '=', $nohp)->first();
            $emailcheck = User::where('email', '=', $email)->first();
            if ($emailcheck == null && $nohpcheck == null) {
                if ($request->hasFile('photo')) {
                    $filename = Str::random(20) . '.jpg';
                    $file = $request->file('photo');
                    $file->move(\base_path('public/images/'), $filename);

                    $user = User::create([
                        'name' => $request->input('name'),
                        'email' => $request->input('email'),
                        'no_hp' => $request->input('no_hp'),
                        'gender' => $request->input('gender'),
                        'password' => app('hash')->make($request->password),
                        'photo' => $filename,
                        'role' => $request->input('role'),
                        'status' => $request->input('status'),
                    ]);

                    return response()->json([
                        'status' => true,
                        'messages' => 'Register Berhasil',
                        'data' => $user
                    ], 201);
                }
            } {
                return response()->json([
                    'status' => false,
                    'messages' => 'Email dan Nomor Hp telah terdaftar',
                    'data' => null
                ], 400);
            }
        } else {
            return  response()->json([
                'status' => false,
                'messages' => 'Semua Field Wajib Diisi!!!'
            ], 400);
        }
    }

    public function login(Request $request)
    {
        if ($request->filled(['email', 'password'])) {
            $user = User::where('email', $request->input('email'))->first();
            if ($user != null) {

                if (Hash::check($request->password, $user->password)) {
                    $user->update([
                        'api_token' => base64_encode(Str::random(40))
                    ]);

                    return response()->json([
                        'status' => true,
                        'messages' => 'Login Berhasil',
                        'data' => $user
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'messages' => 'Password Salah!!',
                        'data' => null
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'messages' => 'Email Salah',
                    'data' => null
                ], 400);
            }
        } else {
            return response()->json([
                'status' => false,
                'messages' => 'Field Tidak Boleh KOsong !!',
                'data' => null
            ], 400);
        }
    }

    public function login_post(Request $request)
    {
        // $email 	    = $this->post('email');
        // $password   = $this->post('password');
        $token_fcm  = $request->input('token_fcm');
        $token_google  = $request->input('token_google');

        $url_check_token = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $token_google;

        $client = curl_init($url_check_token);

        curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($client);
        $http_code = curl_getinfo($client, CURLINFO_HTTP_CODE);

        curl_close($client);

        $result = json_decode($response);

        if ($result) {
            $emailcheck = User::where('email', '=', $result->email)->first();
            if ($emailcheck == null) {
                $user = User::create([
                    'name' => $result->name,
                    'email' => $result->email,
                    'photo' => $result->picture,
                    'api_token' => Str::random(100),
                    'role' => '0',
                    'is_deleted'    => "0"
                ]);

                return response()->json([
                    'status' => true,
                    'messages' => 'create',
                    'data' => $user
                ]);
            } else {
                $emailcheck->update([
                    'name' => $result->name,
                    'email' => $result->email,
                    'photo' => $result->picture,
                    'api_token' => Str::random(100)
                ]);

                return response()->json([
                    'status' => true,
                    'messages' => 'update',
                    'data' => $emailcheck
                ]);
            }
        } else {
            return response()->json([
            'status'=> false,
            'messages'=> 'Gagal Autentikasi Token',
            'data'=> $result
            ]);
        }
    }

    
}
