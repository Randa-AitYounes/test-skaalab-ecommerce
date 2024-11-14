<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CategoryService;
use Illuminate\Http\Request;


/**
 * @OA\Tag(name="Catégories", description="Gestion des catégories")
 */
class CategoryController extends Controller
{

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    

/**
 * @OA\Post(
 *     path="/category",
 *     tags={"Catégories"},
 *     summary="Créer une catégorie",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/Category")
 *     ),
 *     @OA\Response(
 *         response="201", 
 *         description="Catégorie créée avec succès", 
 *         @OA\JsonContent(ref="#/components/schemas/Category")
 *     ),
 *     @OA\Response(
 *         response="422", 
 *         description="Erreur de validation"
 *     )
 * )
 */

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
           
        ]);

        $category = $this->categoryService->createCategory($data);

        return response()->json($category, 201);
    }

    
}
