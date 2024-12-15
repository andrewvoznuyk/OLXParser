<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class MessageHandlerService
{
    /**
     * @param $email
     * @param $link
     * @return false|string
     */
    public static function encodeMessage($email, $link): false|string
    {
        $productPrice = self::getTableData('prices', $link);
        $product = self::getTableData('products', $link);

        return json_encode([
            'link' => $link,
            'price'   => $productPrice->value('price'),
            'name'    => $product->value('name'),
            'email'   => $email
        ]);
    }

    /**
     * @param string $table
     * @param string $link
     * @return Builder
     */
    protected static function getTableData(string $table, string $link): Builder
    {
        return DB::table($table)->where('link', $link)->latest();
    }
}
