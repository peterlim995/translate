<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{  

    public function translate(){
        return view('testpages.translate');
    }

    public function deepl(Request $request)
    {
        $apiKey = env('DEEPL');
        $client = new Client();
        $response = $client->request('POST', 'https://api-free.deepl.com/v2/translate', [
            'form_params' => [
                'auth_key' => $apiKey,
                'text' => $request->input('text'),
                'target_lang' => 'KO',
            ]
        ]);

        $body = $response->getBody();
        $content = json_decode($body, true);

        return response()->json([
            'translatedText' => $content['translations'][0]['text']
        ]);
    }

    public function gpt(Request $request){
        $text = $request->input('text');

        // Log::info($text);

        $system_script = "You are an advanced AI assisting with translation.";
        $ai_script = "Translate the following text to Korean.";       
        $prompt = $text;        

        $post_fields = [
            "model" => "gpt-4",
            // "model" => "gpt-3.5-turbo",
            "messages" => [
                ["role" => "system", "content" => $system_script],
                ["role" => "assistant", "content" => $ai_script],
                ["role" => "user", "content" => $prompt],
            ]
        ];

        $result = AIController::postToAzure($post_fields);
        // $result = AIController::postToOpenai($post_fields);

        // Log::info($result);      
        

        return response()->json([
            'translatedText' => $result['message']
        ]);
        
        

    }

    // public function gpt(Request $request){
    //     $text = $request->input('text');
    
    //     $prompt = "You are an advanced AI assisting with translation. Translate the following text to Korean: " . $text;        
    
    //     $post_fields = [
    //         "model" => "text-davinci-003",
    //         "prompt" => $prompt,
    //         "max_tokens" => 60, // 적절한 토큰 수 설정
    //         "temperature" => 0.5, // 생성의 창의성을 결정
    //     ];
    
    //     $result = AIController::postToOpenai($post_fields);

    //     Log::info($result);
    
    //     return response()->json([
    //         // 'translatedText' => $result['choices'][0]['text'] // 응답에서 번역된 텍스트 추출
    //         'translatedText' => "test"
    //     ]);
    // }
    

    
}
