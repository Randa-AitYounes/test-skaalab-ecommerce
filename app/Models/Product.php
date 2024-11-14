<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Product", 
 *     type="object", 
 *     title="Product", 
 *     description="Modèle représentant un produit", 
 *     required={"name", "price"}, 
 *     @OA\Property(
 *         property="name", 
 *         type="string", 
 *         description="Nom du produit", 
 *         example="Télévision" 
 *     ),
 *     @OA\Property(
 *         property="description", 
 *         type="string", 
 *         description="Description du produit", 
 *         example="Télévision écran HD" 
 *     ),
 *     @OA\Property(
 *         property="price", 
 *         type="number", 
 *         format="float", 
 *         description="Prix du produit", 
 *         example=199.99 
 *     ),
 *     @OA\Property(
 *         property="stock", 
 *         type="integer", 
 *         description="Quantité en stock du produit", 
 *         example=50 
 *     ),
 *     @OA\Property(
 *         property="categories", 
 *         type="array", 
 *         description="Liste des catégories auxquelles le produit appartient", 
 *         @OA\Items(
 *             type="integer", 
 *             description="ID de la catégorie" 
 *         )
 *     )
 * )
 */

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'price', 'stock'];

    public function categories(){
        return $this->belongsToMany(Category::class);
    }
}
