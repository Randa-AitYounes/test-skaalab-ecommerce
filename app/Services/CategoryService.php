<?php

namespace App\Services;

use App\Events\CreateUpdateProduct;
use App\Models\Category;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockNotification;

class CategoryService{

public function createCategory ($data){

    
  
        $category = Category::create($data);

      return $category;

}






}
?>