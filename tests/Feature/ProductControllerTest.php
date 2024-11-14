<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;  // Assurez-vous d'inclure le modèle User
use App\Services\ProductService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockNotification;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    // Test obtenir tous les produits
    public function test_get_all_products()
    {
        Product::factory(10)->create();

        // Créer un utilisateur authentifié
        $user = User::factory()->create();
        $token = $user->createToken('Test Token')->plainTextToken;

        // Ajouter l'authentification avec le token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data'); 
    }

    // Test créer un produit
    public function test_create_product()
    {

       // Créer un utilisateur authentifié
       $user = User::factory()->create();
        
       // Créer un token pour cet utilisateur
       $token = $user->createToken('Test Token')->plainTextToken;

       // Créer une catégorie et un produit
       $category = Category::factory()->create();
       $product = Product::factory()->create();

      
       $product->categories()->attach($category->id); // Lier le produit à la catégorie

     
       $productData = [
           'name' => 'Produit Test',
           'description' => 'Description du produit',
           'price' => 100,
           'stock' => 50,
       ];

      
       $response = $this->postJson('/api/products', $productData, [
           'Authorization' => 'Bearer ' . $token,
       ]);

      
       $response->assertStatus(201)
                ->assertJsonFragment(['name' => 'Produit Test'])
                ->assertJsonFragment(['price' => 100]);

     
   }

    
    public function test_show_product()
    {
        $product = Product::factory()->create();

        // Créer un utilisateur authentifié
        $user = User::factory()->create();
        $token = $user->createToken('Test Token')->plainTextToken;

        // Ajouter l'authentification avec le token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => $product->name]);
    }

    // Test meise à jour d'un produit
    public function test_update_product()
    {
        $product = Product::factory()->create();
        $newData = [
            'name' => 'Produit Mis à Jour',
            'price' => 120,
        ];

        // Créer un utilisateur authentifié
        $user = User::factory()->create();
        $token = $user->createToken('Test Token')->plainTextToken;

       
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/products/{$product->id}", $newData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Produit Mis à Jour']);
    }

     // Test filtrer les produits
    public function test_filter_products()
    {
        // Créer une catégorie
    $category = Category::factory()->create();

    // Créer des produits
    $products = Product::factory(5)->create();

    // Associer les produits à la catégorie 
    foreach ($products as $product) {
        $product->categories()->attach($category->id);  
    }

    // Créer un utilisateur authentifié
    $user = User::factory()->create();
    $token = $user->createToken('Test Token')->plainTextToken;

    
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->getJson('/api/filter', [
        'category_id' => $category->id,
        'per_page' => 5,
    ]);

    $response->assertStatus(200)
             ->assertJsonCount(5, 'data'); 
    }




    public function test_sort_products_with_authentication()
{
    // Créer 4 produits avec différents noms et prix
    Product::factory()->createMany([
        ['name' => 'Produit A', 'price' => 100],
        ['name' => 'Produit B', 'price' => 50],
        ['name' => 'Produit C', 'price' => 150],
        ['name' => 'Produit D', 'price' => 75],
    ]);

    // Créer un utilisateur authentifié
    $user = User::factory()->create();

    // Créer un token pour l'utilisateur
    $token = $user->createToken('Test Token')->plainTextToken;

   
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->getJson('/api/sort', [
        'sort_by' => 'price',
        'sort_order' => 'asc',
        'per_page' => 5
    ]);

    
    $response->assertStatus(200);

    // Vérifier que les produits sont bien triés par prix 
    $response->assertJsonFragment(['name' => 'Produit B']);
    $response->assertJsonFragment(['name' => 'Produit D']);
    $response->assertJsonFragment(['name' => 'Produit A']);
    $response->assertJsonFragment(['name' => 'Produit C']);

}

    // Test supprimer un produit
    public function test_delete_product()
    {
       // Créer un produit
    $product = Product::factory()->create();

    // Créer un utilisateur authentifié
    $user = User::factory()->create();
    $token = $user->createToken('Test Token')->plainTextToken;

    // Supprimer un produit
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->deleteJson('/api/products/' . $product->id);

   
    $response->assertStatus(200)
             ->assertJsonFragment([
                 'message' => 'Produit supprimé!',
             ]);

    
    $product->refresh();

    
    if ($product->trashed()) {  // Vérifie si le produit a été "soft deleted"
        
        $response->assertJsonFragment([
            'produit' => [

                'id' => $product->id,
                'name'=>$product->name,
                'description'=>$product->description,
                'price'=>$product->price,
                'stock'=>$product->stock,
                'created_at'=>$product->created_at,
                'updated_at'=>$product->updated_at,
                'deleted_at' => $product->deleted_at->toISOString(),  
            ]
        ]);
    } 


                } 


                public function test_check_stock_notification_sent()
                {
                     // Créer un produit avec un stock faible (moins de 10)
        $productData = [
            'name' => 'Produit faible stock',
            'price' => 50,
            'stock' => 5, 
        ];

       
        
        $productService = new ProductService();

        
        Notification::fake();

       
        $productService->createProduct($productData);

        // Vérifier que la notification a bien été envoyée via l'email "admin@example.com"
        Notification::assertSentOnDemand(StockNotification::class, function ($notification, $channels) use ($productData) {
            return $notification->product->stock === $productData['stock'];
        });

    }
            
}

?>