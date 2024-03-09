<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class AIController extends Controller
{

    public static function postToAzure($post_fields)
    {
        $apiKey = env('AZURE_OPENAI_35_KEY');
        
        // Log::info('$apiKey' . $apiKey);

        $url = 'https://resume-gpt.openai.azure.com/openai/deployments/gpt-35-large/chat/completions?api-version=2024-02-15-preview';


        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'api-key' => $apiKey,
            ])->post($url, $post_fields);

            if ($response->successful()) {
                $messageContent = data_get($response, 'choices.0.message.content', 'NA');
                $result = [
                    'code' => 'success',
                    'message' => trim(str_replace(["Server: ", "(As a server)"], '', $messageContent))
                ];
                $decodedResponse = json_decode($response, true);
                $content = $decodedResponse['choices'][0]['message']['content'];
                $contentDecoded = json_decode($content, true);
                $feedback = $contentDecoded['answer'] ?? 'No feedback available';
            } else {
                $result = ['code' => 'fail', 'message' => "Unexpected response format"];
            }
        } catch (RequestException $e) {
            Log::error('RequestException: ' . $e->getMessage());
            $result = ['code' => 'error', 'message' => 'RequestException occurred'];
        } catch (ConnectException $e) {
            Log::error('ConnectException: ' . $e->getMessage());
            $result = ['code' => 'error', 'message' => 'Connection problem occurred'];
        } catch (\Exception $e) {
            Log::error('General Exception: ' . $e->getMessage());
            $result = ['code' => 'error', 'message' => 'An error occurred'];
        }
        return $result;
    }

    // AIController 클래스 내부에 추가
    public static function postToOpenai($post_fields)
    {
        $client = new \GuzzleHttp\Client();
        $apiKey = env('OPENAI_API_KEY'); // 환경 변수에서 API 키를 가져옴
        $apiUrl = 'https://api.openai.com/v1/completions'; // OpenAI API 엔드포인트

        try {
            $response = $client->request('POST', $apiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'body' => json_encode($post_fields),
            ]);

            $body = $response->getBody();
            $content = json_decode($body, true);

            // API 응답에서 필요한 정보를 추출하여 반환
            // 여기서는 응답의 형태나 처리 방법이 예제와 다를 수 있으니, API 문서를 참고하여 적절히 조정하세요.
            return $content;
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // 예외 처리: API 호출 실패 시 로그를 남기고, 적절한 에러 메시지 반환
            Log::error("OpenAI API 호출 실패: " . $e->getMessage());
            return ['error' => true, 'message' => 'API 호출 중 오류 발생'];
        }
    }
}
