<?php

namespace JoinLuminous\EtsyOms\Services;

use GuzzleHttp\Exception\GuzzleException;
use JoinLuminous\EtsyOms\config\EtsyClient;
use JoinLuminous\EtsyOms\config\EtsyConfig;
use JoinLuminous\EtsyOms\Helpers\EtsyApiResponseMapper;
use JoinLuminous\OmsContracts\Helpers\Logger;
use JoinLuminous\OmsContracts\Data\Params\BaseParam;
use JoinLuminous\OmsContracts\Helpers\ExceptionHelper;
use JoinLuminous\OmsContracts\Data\ProductDataCollection;
use JoinLuminous\OmsContracts\Data\AppIntegrationAccountData;
use JoinLuminous\OmsContracts\Interfaces\OMSProductInterface;

class EtsyProductService implements OMSProductInterface
{

    private AppIntegrationAccountData $appIntegrationAccountData;
    private EtsyConfig $config;

    /**
     * @param AppIntegrationAccountData $appIntegrationAccountData
     *
     * @throws UnknownProperties
     */
    public function __construct(AppIntegrationAccountData $appIntegrationAccountData)
    {
        $this->appIntegrationAccountData = $appIntegrationAccountData;
        $this->config = new EtsyConfig(
            keyString: $appIntegrationAccountData->credentials['keyString'],
            baseUrl: $appIntegrationAccountData->credentials['baseUrl'],
        );
    }

    /**
     * @inheritDoc
     */
    public function getAppIntegrationAccountData(): AppIntegrationAccountData
    {
        return $this->appIntegrationAccountData;
    }

    /**
     * @inheritDoc
     * @throws InvalidConfigurationException
     * @throws UnknownProperties
     * @throws AccessDeniedException
     * @throws BadRequestException
     * @throws ResourceNotFoundException
     * @throws Exception
     */
    public function getPaginatedProducts(BaseParam $baseParam): ProductDataCollection
    {
        $params = [
            'max' => 100, // Adjust this to the maximum number of items per page, e.g., 100
            'offset' => 0
        ];

        $endpoint = '/api/offers'; // TODO: change to correct endpoint
        $etsyClient = new EtsyClient($this->config);
        $allProducts = [];

        try {
            do {
                $response = $etsyClient->get($endpoint, $params);
                $products = $response['offers'] ?? [];
                $allProducts = array_merge($allProducts, $products);

                // Update the offset for the next page
                $params['offset'] += $params['max'];

                // Check if we've fetched all products
                $totalCount = $response['total_count'] ?? 0;
            } while (count($allProducts) < $totalCount);

            return EtsyApiResponseMapper::mapProductsToProductDataCollection($allProducts);
        } catch (GuzzleException $e) {
            Logger::error('Etsy getPaginatedProducts', ['errorMessage' => $e->getMessage()]);
            ExceptionHelper::throwExceptionFromStatusCode($e->getCode() ?? 0);
        }
    }
}
