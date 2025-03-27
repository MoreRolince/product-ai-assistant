<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Électronique', 'Informatique', 'Maison', 'Cuisine', 
            'Bureau', 'Jardin', 'Sport', 'Loisirs', 'Vêtements', 'Accessoires'
        ];

        $products = [
            // Électronique
            ['Smartphone haut de gamme', 799.99],
            ['Écouteurs sans fil', 129.99],
            ['Montre connectée', 249.99],
            ['Enceinte Bluetooth', 89.99],
            
            // Informatique
            ['Ordinateur portable', 999.99],
            ['Souris gaming', 59.99],
            ['Clavier mécanique', 79.99],
            ['Disque SSD 1To', 109.99],
            
            // Maison
            ['Aspirateur robot', 299.99],
            ['Machine à café', 149.99],
            ['Mixeur professionnel', 79.99],
            ['Lave-vaisselle compact', 399.99],
            
            // Bureau
            ['Chaise ergonomique', 199.99],
            ['Lampe de bureau LED', 39.99],
            ['Set de stylos premium', 24.99],
            ['Classeur en cuir', 49.99]
        ];

        foreach ($products as $product) {
            $category = $categories[array_rand($categories)];
            
            Product::create([
                'name' => $product[0],
                'description' => $this->generateDescription($product[0]),
                'price' => $product[1],
                'category' => $category,
            ]);
        }
    }

    protected function generateDescription($productName)
    {
        $adjectives = ['Superbe', 'Exceptionnel', 'Innovant', 'Élégant', 'Performant'];
        $features = [
            'avec technologie de pointe',
            'conçu pour durer',
            'écologique et durable',
            'haute performance',
            'design ergonomique'
        ];
        $benefits = [
            'pour une expérience utilisateur inégalée',
            'qui répondra à tous vos besoins',
            'pour un confort optimal',
            'avec des résultats impressionnants',
            'pour un usage quotidien'
        ];

        return sprintf(
            "%s %s %s %s. %s",
            $adjectives[array_rand($adjectives)],
            $productName,
            $features[array_rand($features)],
            $benefits[array_rand($benefits)],
            $this->generateRandomSpecs()
        );
    }

    protected function generateRandomSpecs()
    {
        $specs = [
            'Garantie 2 ans incluse.',
            'Livraison gratuite disponible.',
            'Compatibilité universelle assurée.',
            'Batterie longue durée.',
            'Certifié sans danger pour les enfants.',
            'Matériaux premium utilisés.',
            'Fabriqué avec des matériaux recyclés.',
            'Design primé plusieurs fois.'
        ];
    
        shuffle($specs);
        return implode(' ', array_slice($specs, 0, rand(1, 3)));
    }
}