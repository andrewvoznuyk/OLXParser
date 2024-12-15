<?php

namespace App\Action;

use App\Models\Product;

class CreateProductAction
{

    /**
     * @param string $link
     * @param string $name
     * @return Product
     */
    public function __invoke(string $link, string $name): Product
    {
        $product = new Product();
        $product->fill([
            'name' => $name,
            'link' => $link
        ]);
        $product->save();

        return $product;
    }

}
