<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class ArticleController extends Controller
{
    public function index()
    {
      // User::select('*');
    }
    public function store(Request $request)
    {
        if (auth()->check()) {
            $Validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required|string|max:255',
                    'content' => 'required|string',
                ]
            );
            if ($Validator->fails()) {
                return response()->json([
                    'message' => $Validator->errors(),
                    'data' => [],
                    'status' => 'false',
                ], 401);
            }

            $data = [
                'user_id' => Auth::user()->id,
                'title' => $request->get('title'),
                'content' => $request->get('content'),
            ];
            $encrypted_data = $this->encrypt($data);
            $decrypted_data = $this->decrypt($encrypted_data);
            $dataArray = json_decode($decrypted_data, true);

            $article = article::insert($data);
            if ($article) {
                return response()->json([
                    'message' => 'Article data Inserted',
                    'data' => $encrypted_data,
                    'data1' => $dataArray,
                    'status' => 'true',
                ], 200);

            } else {
                return response()->json([
                    'message' => 'Article data not inserted',
                    'data' => [],
                    'status' => 'false',
                ], 401);
            }

        } else {
            return response()->json([
                'message' => 'user not authorised',
                'data' => [],
                'status' => 'false',
            ], 401);
        }
    }
    public function getDataById($id)
    {
        if (Auth::check()) {
            $user_id = Auth::id();
            $article1 = Article::where("user_id", $user_id)->select('*')->get();
            $encrypted_data = $this->encrypt($article1);
            $decrypted_data = $this->decrypt($encrypted_data);
            $dataArray = json_decode($decrypted_data, true);
            if ($article1) {
                return response()->json([
                    'message' => 'Get single record by Id',
                    'data' => $encrypted_data,
                    'data1' => $dataArray,
                    'status' => 'true',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Record not found',
                    'data' => [],
                    'status' => 'false',
                ], 401);
            }
            } else {
                return response()->json([
                    'message' => 'user not authorised',
                    'data' => [],
                    'status' => 'false',
                ], 401);
            }
    }
    public function show()
    {
        if (Auth::check()) {

            $user_id = Auth::id();
            $article1 = Article::where("user_id", $user_id)->get();
            $encrypted_data = $this->encrypt($article1);
            $decrypted_data = $this->decrypt($encrypted_data);
            $dataArray = json_decode($decrypted_data, true);
            return response()->json([
                'message' => count($article1) . ' article found',
                'data' => $encrypted_data,
                'data1' => $dataArray,
                'status' => 'true',
            ], 200);
            } else {
                return response()->json([
                    'message' => 'user not authorised',
                    'data' => [],
                    'status' => 'false',
                ], 401);
            }
    
    }
    public function update(Request $request)
    {
        if (Auth::check()) {
            $user_id = Auth::id();
    
            $Validator = Validator::make($request->all(), [
                'id' => 'required',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);
    
            if ($Validator->fails()) {
                return response()->json([
                    'message' => $Validator->errors(),
                    'data' => [],
                    'status' => 'false',
                ], 401);
            }
    
            $data = [
                'id' => $request->get('id'),
                'title' => $request->get('title'),
                'content' => $request->get('content'),
            ];
            // $user_id = Auth::id();
            $encrypted_data = $this->encrypt($data);
            $decrypted_data = $this->decrypt($encrypted_data);
            $dataArray = json_decode($decrypted_data, true);
    
            $article1 = Article::where("user_id", $user_id)
                ->where('id', $request->id)
                ->update($data);
            if ($article1) {
                return response()->json([
                    'message' => 'Article data updated',
                    'data' => $encrypted_data,
                    'data1' => $dataArray,
                    'status' => 'true',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Unauthorized or article not found',
                    'data' => [],
                    'status' => 'false',
                ], 401);
            }
    
            } else {
                return response()->json([
                    'message' => 'user not authorised',
                    'data' => [],
                    'status' => 'false',
                ], 401);
            }
    }
    public function destroy(Request $request)
    {
        if (Auth::check()) {
            $user_id = Auth::id();
    
            $Validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($Validator->fails()) {
                return response()->json([
                    'message' => $Validator->errors(),
                    'data' => [],
                    'status' => 'false',
                ], 401);
            }
            //$art = article::find($request->id)->delete();
            $art = Article::where("user_id", $user_id)
                ->where('id', $request->id)
                ->delete();
            $encrypted_data = $this->encrypt($art);
            $decrypted_data = $this->decrypt($encrypted_data);
            $dataArray = json_decode($decrypted_data, true);
            if ($art) {
                return response()->json([
                    'message' => 'Article data deleted',
                    'data' => $encrypted_data,
                    'data1' => $dataArray,
                    'status' => 'true',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Unauthorized or record not found',
                    'data' => [],
                    'status' => 'false',
                ], 401);
            }
            } else {
                return response()->json([
                    'message' => 'user not authorised',
                    'data' => [],
                    'status' => 'false',
                ], 401);
            }
        }
    function encrypt($text)
    {
        $text = json_encode($text);

        $AES_METHOD = 'AES-256-CBC';
        $cipherKey = "3452ffddssssfgesedseabgshaesrftgd"; //encryption key

        $iv = openssl_random_pseudo_bytes(16); //default length

        $ciphertext = openssl_encrypt($text, $AES_METHOD, $cipherKey, OPENSSL_RAW_DATA, $iv);
        $encrypted_data = base64_encode($ciphertext);

        $ivKEY = base64_encode(string: $iv);

        return "$ivKEY:$encrypted_data";
    }
    function decrypt($text)
    {
        $AES_METHOD = 'AES-256-CBC';
        $cipherKey = "3452ffddssssfgesedseabgshaesrftgd";
        $parts = explode(':', $text);
        $iv = base64_decode($parts[0]);
        $ciphertext = base64_decode($parts[1]);
        return openssl_decrypt($ciphertext, $AES_METHOD, $cipherKey, OPENSSL_RAW_DATA, $iv);
    }
}

