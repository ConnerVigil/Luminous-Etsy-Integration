<?php

namespace JoinLuminous\EtsyOms\Services;

use JoinLuminous\EtsyOms\config\EtsyClient;
use JoinLuminous\EtsyOms\config\EtsyConfig;
use JoinLuminous\OmsContracts\Interfaces\OMSProductStockInterface;
use JoinLuminous\OmsContracts\Data\ProductStockDataCollection;
use JoinLuminous\OmsContracts\Data\AppIntegrationAccountData;
use JoinLuminous\OmsContracts\Helpers\ExceptionHelper;
use JoinLuminous\OmsContracts\Helpers\Logger;
use GuzzleHttp\Exception\GuzzleException;

class EtsyProductStockService implements OMSProductStockInterface
{
    private EtsyClient $etsyClient;

    /**
     * @param AppIntegrationAccountData $appIntegrationAccountData
     *
     * @throws UnknownProperties
     */
    public function __construct(AppIntegrationAccountData $appIntegrationAccountData)
    {
        $config = new EtsyConfig(
            shopKey: $appIntegrationAccountData->credentials['shop_key'],
            baseUrl: $appIntegrationAccountData->credentials['base_url'],
        );
        $this->etsyClient = new EtsyClient($config);
    }


    public function getOffer($offerId)
    {
        $endpoint = '/api/offers/' . $offerId;

        return $this->etsyClient->get($endpoint);
    }

    /**
     * @inheritDoc
     * @throws InvalidConfigurationException
     * @throws AccessDeniedException
     * @throws BadRequestException
     * @throws ResourceNotFoundException
     * @throws Exception
     */
    public function pushStock(ProductStockDataCollection $productStockDataCollection): array
    {
        $response = []; // TODO: Implement pushStock
        $payload = [];

        return [
            'message' => 'Stocks successfully pushed to Etsy.',
            'responseData' => $response,
            'payload' => $payload
        ];
    }
}
