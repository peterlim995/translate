<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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

    // public function gpt(Request $request)
    // {
   
    //     $text = $request->input('text');
    //     $open_api_key = env('OPENAI_API_KEY');
    //     // The parameters for the request
    //     $post_fields = array (
    //         // "model" => "gpt-3.5-turbo",  //"model" => "gpt-4",
    //         "model" => "gpt-4",
    //         "messages" => array (
    //             array ( "role" => "system",
    //                 "content" => "You are an advanced AI assisting with translation."
    //             ),
    //         )
    //     );
    //     $message = array ( "role" => "user",
    //         "content" => "Translate the following text to Korean: " . $text
    //     );
    //     $post_fields['messages'][] = $message;
    //     $url = "https://api.openai.com/v1/chat/completions";

    //     // 원하는 초 단위의 timeout 값을 설정
    //     $timeoutInSeconds = 120;
    //     try {
    //         // Use Laravel's HTTP client to send a POST request with custom headers and timeout
    //         $response = Http::withHeaders([
    //             'Authorization' => 'Bearer ' . $open_api_key,
    //             'Content-Type' => 'application/json'
    //         ])->timeout($timeoutInSeconds)->post($url, $post_fields);
    //         if ($response) {
    //             $result = $response['choices'][0]['message']['content'];
    //             $result = trim($result, "\n");
    //         } else {
    //             $result = "NA";
    //         }
            
    //         // return $result;
    //     } catch (ConnectionException $e) {
    //         // Handle exceptions
    //         $result = "NA";
    //         return $result;
    //     } catch (Exception $e) {
    //         $result = "NA";
    //         return $result;
    //     }

    //     Log::info($result);
    
    //     return response()->json([
    //         // 'translatedText' => $result['choices'][0]['text'] // 응답에서 번역된 텍스트 추출
    //         'translatedText' => $result
    //     ]);
    // }
    
    // public function gpt(Request $request)
    // {
    //     $text = $request->input('text'); // 자막 파일의 내용
    //     $open_api_key = env('OPENAI_API_KEY');
    
    //     // 번역 작업에 대한 상세한 지시사항을 포함하는 메시지 배열 구성
    //     // $system_message = "You are an advanced AI specializing in subtitle translation. Maintain the subtitle numbering and timestamp format. Translate only the text content from English to Korean and insert the translation below each original text line.";
    //     $system_message = "You are a highly skilled translator. Translate the following subtitles from Korean to English, maintaining the original format and including the original Korean text followed by the English translation directly underneath each subtitle entry.";
        
    //     $user_message = "Here are the subtitles to translate:\n" . $text;
    
    //     // API 요청 파라미터 설정
    //     $post_fields = [
    //         "model" => "gpt-4", // GPT-4 모델 사용
    //         "messages" => [
    //             [
    //                 "role" => "system",
    //                 "content" => $system_message
    //             ],
    //             [
    //                 "role" => "user",
    //                 "content" => $user_message
    //             ]
    //         ]
    //     ];
    
    //     $url = "https://api.openai.com/v1/chat/completions";
    
    //     // 원하는 초 단위의 timeout 값을 설정
    //     $timeoutInSeconds = 120;
    
    //     try {
    //         // Laravel의 HTTP 클라이언트를 사용하여 POST 요청 전송
    //         $response = Http::withHeaders([
    //             'Authorization' => 'Bearer ' . $open_api_key,
    //             'Content-Type' => 'application/json'
    //         ])->timeout($timeoutInSeconds)->post($url, $post_fields);
    
    //         if ($response->successful()) {
    //             $result = $response->json()['choices'][0]['message']['content'];
    //             $result = trim($result, "\n");
    //         } else {
    //             $result = "Translation failed or not available";
    //         }
    //     } catch (\Throwable $e) {
    //         // 예외 처리
    //         $result = "Error: " . $e->getMessage();
    //     }
    
    //     Log::info($result);
    
    //     // JSON 응답 반환
    //     return response()->json([
    //         'translatedText' => $result
    //     ]);
    // }
    
    
}
