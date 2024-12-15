<?php

namespace App\Http\Middleware;

use App\Action\CreateProductAction;
use App\Action\CreateProductPriceAction;
use App\Action\CreateSubscriptionAction;
use App\Contracts\LinkParserServiceInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Closure;

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

    public function handle(Request $request, Closure $next): JsonResponse
    {
        $link = $request->input('link');
        $email = $request->input('email');

        if (!$this->validateInputs($link, $email)) {
            return new JsonResponse(['error' => 'Link and email are required.'], Response::HTTP_BAD_REQUEST);
        }

        $product = $this->ensureProductExists($link);
        if ($product->exists()) {
            return $next($request);
        }

        if (!$this->handleNewProduct($link)['isSucceed']) {
            return new JsonResponse(['error' => 'Invalid link.'], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }

    /**
     * @param string|null $link
     * @param string|null $email
     * @return bool
     */
    protected function validateInputs(?string $link, ?string $email): bool
    {
        return $link && $email;
    }

    /**
     * @param string $link
     * @return array
     */
    protected function handleNewProduct(string $link): array
    {
        $data = $this->linkParserService->getLinkData($link);

        if (!$data['isSucceed']) {
            return $data;
        }

        ($this->productAction)($link, $data['name']);
        ($this->productPriceAction)($link, $data['price']);

        return $data;
    }

    /**
     * @param string $link
     * @return Builder
     */
    protected function ensureProductExists(string $link): Builder
    {
        return DB::table('products')->where('link', $link)->latest();
    }

}

