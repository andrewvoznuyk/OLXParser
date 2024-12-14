<?php

namespace App\Services;

use App\Contracts\LinkParserServiceInterface;
use App\Contracts\LinkValidatorInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DomCrawler\Crawler;

class LinkParserService implements LinkParserServiceInterface
{
    protected const DEFAULT_NAME = 'Unknown Product';
    protected const DEFAULT_PRICE = 'Unknown Price';
    protected array $parsedData = [];

    public function __construct(
        protected LinkValidatorInterface $validator
    )
    {
    }

    /**
     * @param string $link
     * @return LinkParserService
     * @throws GuzzleException
     */
    public function parse(string $link): self
    {
        $client = new Client();

        try {
            $response = $client->request('GET', $link);

            if ($response->getStatusCode() !== 200) {
                throw new Exception();
            }

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $jsonLd = $crawler->filter('script[type="application/ld+json"]')->text();
            $data = json_decode($jsonLd, true);

            if (isset($data['name'], $data['offers']['price'])) {
                $this->setDataValues($data['name'], $data['offers']['price']);
                return $this;
            }

            $this->setDataValues(null, null);
            return $this;

        } catch (ClientException|ConnectException|Exception $e) {
            $this->setDataValues(null, null);
        }

        return $this;
    }

    /**
     * @param string $link
     * @return array
     * @throws GuzzleException
     */
    public function getLinkData(string $link): array
    {
        if (!$this->validator->validateLink($link)) {
            $this->setDataValues(null, null);
            return $this->parsedData;
        }

        $this->parse($link);

        return $this->parsedData;
    }

    /**
     * @param string|null $name
     * @param string|null $price
     * @return array
     */
    protected function setDataValues(?string $name, ?string $price): array
    {
        $name = $name ?? self::DEFAULT_NAME;
        $price = $price ?? self::DEFAULT_PRICE;
        $isSucceed = $name !== self::DEFAULT_NAME || $price !== self::DEFAULT_PRICE;

        return $this->parsedData = [
            'name' => $name,
            'price' => $price,
            'isSucceed' => $isSucceed,
        ];
    }

}
