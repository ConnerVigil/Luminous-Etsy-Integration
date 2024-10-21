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
            keyString: $appIntegrationAccountData->credentials['keyString'],
            baseUrl: $appIntegrationAccountData->credentials['baseUrl'],
        );
        $this->etsyClient = new EtsyClient($config);
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
        $response = [];
        $payload = [];

        foreach ($productStockDataCollection->productStocks as $productStock) {
            $listingId = $productStock->productId;

            $inventoryData = [
                'products' => [
                    [
                        'sku' => $productStock->internalSku,
                        'offerings' => [
                            [
                                'quantity' => $productStock->availableQuantity->toInt(),
                                'is_enabled' => true
                            ]
                        ]
                    ]
                ]
            ];

            $endpoint = "/v3/application/listings/{$listingId}/inventory";

            try {
                $apiResponse = $this->etsyClient->patch($endpoint, $inventoryData);

                $response[] = [
                    'productId' => $productStock->productId,
                    'status' => 'success',
                    'message' => 'Stock updated successfully',
                    'data' => $apiResponse
                ];

                $payload[] = $inventoryData;
            } catch (GuzzleException $e) {
                $response[] = [
                    'productId' => $productStock->productId,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
                Logger::error('Error updating Etsy inventory', ['exception' => $e, 'productId' => $productStock->productId]);
            }
        }

        return [
            'message' => 'Stock push to Etsy completed.',
            'responseData' => $response,
            'payload' => $payload
        ];
    }
}
