<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\Conversation;

class AIController extends Controller
{
    public function askQuestion(Request $request, $productId)
    {
        // Validation de la requête
        $request->validate([
            'question' => 'required|string',
        ]);

        // Récupération du produit
        $product = Product::findOrFail($productId);
        $context = "Product: {$product->name}. Description: {$product->description}. Price: {$product->price}.";

        // Initialisation du client Guzzle
        $client = new Client();

        try {
            // Envoi de la requête à l'API Hugging Face
            $response = $client->post(env('HUGGINGFACE_API_URL'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'inputs' => $context . ' ' . $request->question,
                ],
            ]);

            // Décodage de la réponse
            $responseData = json_decode($response->getBody(), true);
            $answer = $responseData[0]['generated_text'] ?? 'Désolé, je n’ai pas compris votre question.';

            // Sauvegarde de la conversation en base de données
            Conversation::create([
                'product_id' => $productId,
                'question' => $request->question,
                'answer' => $answer,
            ]);

            // Retourne la réponse en JSON
            return response()->json(['answer' => $answer]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue lors de la requête à l\'IA.'], 500);
        }
    }
}
