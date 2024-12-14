<?php

namespace App\Http\Middleware;

use App\Action\CreateProductAction;
use App\Action\CreateProductPriceAction;
use App\Action\CreateSubscriptionAction;
use App\Contracts\LinkParserServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckProductUrl
{

    /**
     * @param CreateSubscriptionAction $subscriptionAction
     * @param CreateProductAction $productAction
     * @param CreateProductPriceAction $productPriceAction
     * @param LinkParserServiceInterface $linkParserService
     */
    public function __construct(
        protected CreateSubscriptionAction   $subscriptionAction,
        protected CreateProductAction        $productAction,
        protected CreateProductPriceAction   $productPriceAction,
        protected LinkParserServiceInterface $linkParserService
    )
    {
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $link = $request->input('link');
        $email = $request->input('email');

        if (!$link || !$email) {
            return response()->json(['error' => 'Link and email are required.'], 400);
        }

        $product = $this->ensureProductExists($link);
        $data = $this->linkParserService->getLinkData($link);

        if (!$product) {
            if (!$data['isSucceed']) {
                return $data;
            }
            ($this->productAction)($link, $data['name']);
        }
        ($this->productPriceAction)($link, $data['price']);

        return $next($request);
    }

    /**
     * @param string $link
     * @return bool
     */
    protected function ensureProductExists(string $link): bool
    {
        return DB::table('products')->where('link', $link)->exists();
    }

}
