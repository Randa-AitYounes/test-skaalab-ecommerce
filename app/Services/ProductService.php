<?php

namespace App\Services;

use App\Events\CreateUpdateProduct;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockNotification;

class ProductService{

public function createProduct ($data){

    
    DB::beginTransaction();

    try {
        // Créer le produit
        $product = Product::create($data);

        // Attacher les catégories
        if (!empty($data['categories'])) {
            $product->categories()->attach($data['categories']);
        }

// Déclencher l'événement
event(new CreateUpdateProduct($product));

        $this->checkStock($product);
        
        DB::commit();

        return $product;

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }

}

public function getAllProducts($perPage = 5){

    $products = Product::with('categories')->paginate($perPage);

    foreach ($products as $product) {
        $this->checkStock($product);
    }

    return $products;
    

}



public function getProductsFiltre($filtres, $perPage = 5){

    $query = Product::query();

    //1- filtre par prix
    if (isset($filtres['price_min'])) {
        $query->where('price', '>=', $filtres['price_min']);
    }

    if (isset($filters['price_max'])) {
        $query->where('price', '<=', $filters['price_max']);
    }

    //2- filtre par nom
    if (isset($filters['name'])) {
        $query->where('name', 'like', '%' . $filters['name'] . '%');
    }

    //3- filtre par categorie
    if (isset($filters['category_id'])) {
        $query->where('category_id', $filters['category_id']);
    }

   

    return $query->paginate($perPage);
}

public function getProductsSorted($sortBy = 'name', $sortOrder = 'asc', $perPage = 5)
    {
        return Product::orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    public function updateProduct($id, $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);

        // Attacher les catégories si elles existent
        if (!empty($data['categories'])) {
            $product->categories()->sync($data['categories']);
        }

        event(new CreateUpdateProduct($product));

        $this->checkStock($product);

        return $product;
    }

    public function rechercheProduit(string $query)
    {
        return Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->with('categories') 
            ->get();
    }

    private function checkStock(Product $product)
    {
        if ($product->stock < 10) {
            
            Notification::route('mail', 'admin@example.com')->notify(new StockNotification($product));
        }
    }
   

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return $product;
    }


}
?>