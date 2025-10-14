<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIChatController extends Controller
{
    public function chat(Request $request)
    {
        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['reply' => '⚠️ Missing Gemini API key.']);
        }

        // ✅ Use the new working model (from your list)
        $model = 'models/gemini-2.5-flash';
        $url = "https://generativelanguage.googleapis.com/v1beta/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [[
                'parts' => [[
                    'text' => "You are FarmSmart AI — a friendly Filipino agriculture assistant.
                               Keep your answers short and practical.\n\nUser: {$userMessage}\nFarmSmart:"
                ]]
            ]]
        ];

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            $data = $response->json();
            Log::info('Gemini raw response from '.$url.': '.json_encode($data));

            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return response()->json([
                    'reply' => $data['candidates'][0]['content']['parts'][0]['text']
                ]);
            }

            if (isset($data['error']['message'])) {
                return response()->json([
                    'reply' => '⚠️ Gemini Error: ' . $data['error']['message']
                ]);
            }

            return response()->json(['reply' => '⚠️ No response from Gemini.']);
        } catch (\Exception $e) {
            Log::error('Gemini Chat Error: ' . $e->getMessage());
            return response()->json(['reply' => '⚠️ Server error: ' . $e->getMessage()]);
        }
    }
}
