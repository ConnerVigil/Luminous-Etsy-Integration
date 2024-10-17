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
            'limit' => 100,
            'offset' => 0
        ];

        $accessToken = 'test'; // TODO: Figure out where to get the access token from

        $shopId = $this->appIntegrationAccountData->credentials['shopId'];
        $endpoint = "/v3/application/shops/$shopId/listings";
        $etsyClient = new EtsyClient($this->config);
        $allListings = [];

        try {
            $headers = [
                'x-api-key' => $this->config->keyString,
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ];

            do {
                $response = $etsyClient->get($endpoint, $params, $headers);
                $listings = $response['results'];
                $allListings = array_merge($allListings, $listings);
                $params['offset'] += $params['limit'];
            } while ($response['count'] != 0);

            return EtsyApiResponseMapper::mapProductsToProductDataCollection($allListings);
        } catch (GuzzleException $e) {
            ExceptionHelper::throwExceptionFromStatusCode($e->getCode() ?? 0);
        }
    }
}
