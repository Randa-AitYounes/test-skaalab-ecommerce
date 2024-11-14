<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="API de Gestion de Produits", version="1.0")
 * @OA\Server(url="http://localhost:8000/api")
 * @OA\Tag(name="Produits", description="Gestion des produits")
 */

class ProductController extends Controller
{

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    
/**
     * @OA\Get(
     *     path="/products",
     *     tags={"Produits"},
     *     summary="Obtenir tous les produits avec pagination",
     *     @OA\Response(response="200", description="Liste des produits", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product")))
     * )
     */

    public function index(Request $request)
    {

        $perPage = $request->input('per_page', 5); // Valeur par défaut de 10

        // Appeler le service pour obtenir les produits paginés
        $products = $this->productService->getAllProducts($perPage);

        // Retourner la réponse JSON
        return response()->json($products);
        
        
        
        
       
    }

  /**
     * @OA\Post(
     *     path="/products",
     *     tags={"Produits"},
     *     summary="Créer un produit",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response="201", description="Produit créé avec succès", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response="422", description="Erreur de validation")
     * )
     */

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'categories' => 'array|exists:categories,id'
        ]);

        $product = $this->productService->createProduct($data);

        return response()->json($product, 201);
    }

     /**
     * @OA\Get(
     *     path="/products/{id}",
     *     tags={"Produits"},
     *     summary="Obtenir un produit par son ID",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Produit récupéré", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response="404", description="Produit non trouvé")
     * )
     */

    public function show($id)
    {
        $product = Product::with('categories')->findOrFail($id);

        return response()->json($product);
    }

     /**
     * @OA\Put(
     *     path="/products/{id}",
     *     tags={"Produits"},
     *     summary="Mettre à jour un produit",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(response="200", description="Produit mis à jour", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response="404", description="Produit non trouvé"),
     *     @OA\Response(response="422", description="Erreur de validation")
     * )
     */

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'stock' => 'sometimes|required|integer',
            'categories' => 'array',
        ]);

        $product = $this->productService->updateProduct($id, $data);

        return response()->json($product);
    }

    /**
 * @OA\Delete(
 *     path="/products/{id}",
 *     tags={"Produits"},
 *     summary="Supprimer un produit",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response="200", description="Produit supprimé"),
 *     @OA\Response(response="404", description="Produit non trouvé")
 * )
 */

    public function destroy($id)
    {
        $product = $this->productService->deleteProduct($id);

        return response()->json(['message' => 'Produit supprimé!', 'produit' => $product]);
    }

    /**
 * @OA\Post(
 *     path="/filter",
 *     tags={"Produits"},
 *     summary="Filtrer les produits",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="per_page", type="integer", example=5),
 *             @OA\Property(property="name", type="string", example="Nom du produit"),
 *             @OA\Property(property="price_min", type="number", format="float", example=10.00),
 *             @OA\Property(property="price_max", type="number", format="float", example=100.00),
 *             @OA\Property(property="category_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(response="200", description="Liste des produits filtrés", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))),
 *     @OA\Response(response="422", description="Erreur de validation")
 * )
 */

    public function filter(Request $request){
        $perPage = $request->input('per_page', 5);
        $filters = $request->only(['name', 'price_min', 'price_max', 'category_id']);
        $products = $this->productService->getProductsFiltre($filters, $perPage);

        return response()->json($products);
    }

    /**
 * @OA\Post(
 *     path="/sort",
 *     tags={"Produits"},
 *     summary="Trier les produits",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="per_page", type="integer", example=5),
 *             @OA\Property(property="sort_by", type="string", example="name"),
 *             @OA\Property(property="sort_order", type="string", enum={"asc", "desc"}, example="asc")
 *         )
 *     ),
 *     @OA\Response(response="200", description="Liste des produits triés", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))),
 *     @OA\Response(response="422", description="Erreur de validation")
 * )
 */

    public function sort(Request $request)
    {
        $perPage = $request->input('per_page', 5);
        $sortBy = $request->input('sort_by', 'name'); 
        $sortOrder = $request->input('sort_order', 'asc'); 

        $products = $this->productService->getProductsSorted($sortBy, $sortOrder, $perPage);

     

        return response()->json($products);
    }

    /**
 * @OA\Post(
 *     path="/search",
 *     tags={"Produits"},
 *     summary="Rechercher des produits",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="query", type="string", example="produit")
 *         )
 *     ),
 *     @OA\Response(response="200", description="Liste des produits trouvés", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))),
 *     @OA\Response(response="422", description="Erreur de validation")
 * )
 */

    public function search(Request $request)
    {
        $query = $request->input('query');
        $products = $this->productService->rechercheProduit($query);
        return response()->json($products);
    }

}
