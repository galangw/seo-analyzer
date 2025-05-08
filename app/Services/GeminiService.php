<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $modelId;
    
    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        $this->modelId = 'gemini-2.0-flash-lite';
        
        if (empty($this->apiKey)) {
            Log::warning('Gemini API key is not set in the configuration');
        }
    }
    
    public function generateTitleSuggestion($keyword, $content)
    {
        try {
            // Extract first 200 words from content
            $contentExcerpt = $this->extractFirstWords($content, 200);
            
            $prompt = "Berdasarkan informasi berikut:

Target Keyword: $keyword

Cuplikan Artikel: $contentExcerpt

Buatlah judul artikel yang sesuai dengan kaidah SEO dengan ketentuan berikut:

- Panjang judul harus antara 50 hingga 90 karakter (termasuk spasi)
- Hanya simbol berikut yang boleh digunakan dalam judul: ?, &, dan -
- Judul wajib mengandung target keyword secara alami
- Judul harus relevan dengan cuplikan artikel yang diberikan

Berikan judul tersebut dalam format:
<title>[judul]</title>

Pastikan judul yang diberikan memenuhi aturan panjang karakter secara tepat. Hitung kembali secara manual sebelum mengirim jawaban.";

            // Log API Key length for debugging (don't log the actual key)
            Log::info('API Key exists: ' . (empty($this->apiKey) ? 'No' : 'Yes') . ', Length: ' . strlen($this->apiKey));
            
            $response = Http::withBody( 
                json_encode([
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 1,
                        'topK' => 40,
                        'topP' => 0.9,
                        'maxOutputTokens' => 8192,
                        'responseMimeType' => 'text/plain'
                    ]
                ]), 'json'
            ) 
            ->withHeaders([ 
                'Content-Type' => 'application/json', 
            ]) 
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->modelId}:generateContent?key={$this->apiKey}");
            
            if ($response->successful()) {
                Log::info('Gemini API Success. Response size: ' . strlen($response->body()));
                return $this->parseTitleFromResponse($response->body());
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                Log::error('Gemini API Status: ' . $response->status());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Gemini Service Error: ' . $e->getMessage());
            Log::error('Gemini Service Stack Trace: ' . $e->getTraceAsString());
            return null;
        }
    }
    
    public function generateMetaDescriptionSuggestion($keyword, $content, $retryCount = 0, $previousResponse = null)
    {
        try {
            // Max retry attempts
            $maxRetries = 2;
            
            // Extract first 200 words from content
            $contentExcerpt = $this->extractFirstWords($content, 200);
            
            // Determine prompt based on retry count
            $prompt = "";
            
            if ($retryCount == 0) {
                // Initial prompt
                $prompt = "Berdasarkan target keyword berikut:
$keyword

dan cuplikan artikel berikut:
$contentExcerpt

PENTING: Berikan meta description yang HARUS memenuhi kriteria panjang karakter berikut:
- MINIMUM 130 karakter (termasuk spasi)
- MAKSIMUM 155 karakter (termasuk spasi)
- Deskripsi yang kurang dari 145 atau lebih dari 160 karakter akan DITOLAK

Aturan tambahan yang harus dipatuhi:
1. Deskripsi harus mengandung target keyword
2. Gunakan bahasa yang menarik, jelas dan SEO friendly
3. Simbol yang diperbolehkan hanya (?), (&), dan (-)
4. Hindari pemotongan kata di akhir deskripsi

Format jawaban yang diharapkan (tanpa tambahan teks apapun):
<desc>[meta description antara 145-160 karakter]</desc>

PERINGATAN: Pastikan untuk menghitung karakter dengan teliti. Deskripsi yang tidak memenuhi rentang 145-160 karakter akan ditolak sistem.";
            } else {
                // Retry prompt with previous response as context
                $promptPrefix = "";
                $descLength = strlen($previousResponse);
                
                if ($descLength > 160) {
                    $promptPrefix = "Ringkasan sebelumnya ini TERLALU PANJANG dan melebihi 160 karakter:";
                } else if ($descLength < 145) {
                    $promptPrefix = "Ringkasan sebelumnya ini TERLALU PENDEK dan kurang dari 145 karakter:";
                }
                
                $prompt = "$promptPrefix
\"$previousResponse\"

Tolong buat ulang meta description yang memenuhi kriteria berikut:
- TEPAT antara 145-160 karakter (termasuk spasi)
- Mengandung kata kunci: \"$keyword\"
- Relevan dengan cuplikan artikel yang diberikan
- Gunakan bahasa yang menarik dan SEO friendly

Berikan HANYA meta description dalam format:
<desc>[meta description tepat 145-160 karakter]</desc>

PERINGATAN: Hitung dengan teliti, pastikan meta description tepat antara 145-160 karakter.";
            }

            // Log API Key length for debugging (don't log the actual key)
            Log::info('API Key exists: ' . (empty($this->apiKey) ? 'No' : 'Yes') . ', Length: ' . strlen($this->apiKey));
            Log::info('Meta description generation attempt #' . ($retryCount + 1));
            
            // Build request body based on retry count
            $requestBody = [];
            
            if ($retryCount == 0) {
                // Initial request without history
                $requestBody = [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 1,
                        'topK' => 40,
                        'topP' => 0.9,
                        'maxOutputTokens' => 8192,
                        'responseMimeType' => 'text/plain'
                    ]
                ];
            } else {
                // Retry request with chat history
                $requestBody = [
                    'contents' => [
                        // First message - original request
                        [
                            'role' => 'user',
                            'parts' => [
                                [
                                    'text' => "Berdasarkan target keyword: $keyword dan cuplikan artikel, buatkan meta description sesuai pedoman SEO."
                                ]
                            ]
                        ],
                        // Model's previous response
                        [
                            'role' => 'model',
                            'parts' => [
                                [
                                    'text' => "<desc>$previousResponse</desc>"
                                ]
                            ]
                        ],
                        // New instruction for retry
                        [
                            'role' => 'user',
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 1,
                        'topK' => 40,
                        'topP' => 0.9,
                        'maxOutputTokens' => 8192,
                        'responseMimeType' => 'text/plain'
                    ]
                ];
            }
            
            $response = Http::withBody(json_encode($requestBody), 'json')
            ->withHeaders([ 
                'Content-Type' => 'application/json', 
            ]) 
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->modelId}:generateContent?key={$this->apiKey}");
            
            if ($response->successful()) {
                Log::info('Gemini API Success for Meta Description. Response size: ' . strlen($response->body()));
                $parsedDescription = $this->parseDescriptionFromResponse($response->body());
                
                if ($parsedDescription) {
                    $descLength = strlen($parsedDescription);
                    Log::info("Meta description parsed, length: $descLength characters");
                    
                    // Check if description meets our criteria (145-160 chars)
                    if ($descLength >= 145 && $descLength <= 160) {
                        Log::info("Meta description valid, returning result");
                        return $parsedDescription;
                    } else {
                        Log::info("Meta description invalid length: $descLength chars");
                        
                        // If we haven't reached max retries, try again
                        if ($retryCount < $maxRetries) {
                            Log::info("Retrying meta description generation (attempt " . ($retryCount + 2) . ")");
                            return $this->generateMetaDescriptionSuggestion($keyword, $content, $retryCount + 1, $parsedDescription);
                        } else {
                            Log::info("Max retries reached, returning best result");
                            return $parsedDescription;
                        }
                    }
                } else {
                    Log::error("Failed to parse description from response");
                    return null;
                }
            } else {
                Log::error('Gemini API Error for Meta Description: ' . $response->body());
                Log::error('Gemini API Status: ' . $response->status());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Gemini Service Error for Meta Description: ' . $e->getMessage());
            Log::error('Gemini Service Stack Trace: ' . $e->getTraceAsString());
            return null;
        }
    }
    
    protected function extractFirstWords($htmlContent, $wordCount)
    {
        // Remove HTML tags
        $text = strip_tags($htmlContent);
        
        // Split into words
        $words = preg_split('/\s+/', $text);
        
        // Get first n words
        $words = array_slice($words, 0, $wordCount);
        
        // Join words back together
        return implode(' ', $words);
    }
    
    protected function parseTitleFromResponse($response)
    {
        // Try to decode the JSON response
        $responseData = json_decode($response, true);
        
        // Check if we have a valid response structure as shown in the sample
        if ($responseData && isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $responseData['candidates'][0]['content']['parts'][0]['text'];
            
            // Extract text between <title> tags
            if (preg_match('/<title>(.*?)<\/title>/s', $text, $matches)) {
                return $matches[1];
            }
            
            // If no title tags but we have clear text, return it (but truncate if needed)
            return strlen($text) > 90 ? substr($text, 0, 90) : $text;
        }
        
        // Fallback to direct regex for backward compatibility
        if (preg_match('/<title>(.*?)<\/title>/s', $response, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    protected function parseDescriptionFromResponse($response)
    {
        // Try to decode the JSON response
        $responseData = json_decode($response, true);
        
        // Check if we have a valid response structure
        if ($responseData && isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $responseData['candidates'][0]['content']['parts'][0]['text'];
            
            // Extract text between <desc> tags
            if (preg_match('/<desc>(.*?)<\/desc>/s', $text, $matches)) {
                return $matches[1];
            }
            
            // If no desc tags but we have clear text, return it (but truncate if needed)
            return strlen($text) > 160 ? substr($text, 0, 160) : $text;
        }
        
        // Fallback to direct regex for backward compatibility
        if (preg_match('/<desc>(.*?)<\/desc>/s', $response, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
} 