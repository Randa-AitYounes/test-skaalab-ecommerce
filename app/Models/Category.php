<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Category", 
 *     type="object", 
 *     title="Category", 
 *     description="Modèle représentant une catégorie de produits", 
 *     required={"name"}, 
 *     @OA\Property(
 *         property="name", 
 *         type="string", 
 *         description="Nom de la catégorie", 
 *         example="Alimentation" 
 *     ),
 *     @OA\Property(
 *         property="products", 
 *         type="array", 
 *         description="Liste des produits associés à la catégorie", 
 *         @OA\Items(
 *             type="integer", 
 *             description="ID du produit" 
 *         )
 *     )
 * )
 */

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function products(){
        return $this->belongsToMany(Product::class);

    }


}
