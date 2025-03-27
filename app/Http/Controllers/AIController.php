<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;

class AIController extends Controller
{
    public function askQuestion(Request $request, $productId)
    {
        $request->validate([
            'question' => 'required|string',
        ]);

        $product = Product::findOrFail($productId);
        $context = "Product: {$product->name}. Description: {$product->description}. Price: {$product->price}.";

        $client = new Client();
        $response = $client->post(env('HUGGINGFACE_API_URL'), [
            'headers' => [
                'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'inputs' => [
                    'text' => $context . ' ' . $request->question,
                ],
            ],
        ]);

        $responseData = json_decode($response->getBody(), true);

        return response()->json([
            'answer' => $responseData['generated_text'] ?? 'Sorry, I could not understand your question.',
        ]);
    }
}

// Après avoir reçu la réponse de l'API
\App\Models\Conversation::create([
    'product_id' => $productId,
    'question' => $request->question,
    'answer' => $responseData['generated_text'],
]);