<?php

namespace App\Action;

use App\Models\Price;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class CreateProductPriceAction
{

    /**
     * @param string $link
     * @param string $price
     * @return Price
     */
    public function __invoke(string $link, string $price): Price
    {
        $priceDB = $this->getPrice($link);
        $product = new Price();
        $product->fill([
            'price' => $price,
            'link'  => $link,
        ]);
        if ((float)$priceDB->value('price') === (float)$price) {
            return $product;
        }

        $product->save();

        return $product;
    }

    /**
     * @param string $link
     * @return Builder
     */
    protected function getPrice(string $link): Builder
    {
        return DB::table('prices')->where('link', $link)->latest('created_at');
    }

}
