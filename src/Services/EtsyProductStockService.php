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

    public function getImportStatus($importId)
    {
        $endpoint = '/api/offers/imports/' . $importId;

        return $this->etsyClient->get($endpoint);
    }

    public function getErrorReport($importId)
    {
        $endpoint = '/api/offers/imports/' . $importId . '/error_report';

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
        $productStockData = $productStockDataCollection->productStocks[0];
        $offer_id = $productStockData->inventoryItemId;

        $offer = $this->getOffer($offer_id);

        $quantity = is_int($productStockData->availableQuantity) ? $productStockData->availableQuantity : $productStockData->availableQuantity->toInt();

        $payload = [
            'offers' => [
                [
                    'shop_sku' => $offer['shop_sku'],
                    'quantity' => $quantity,
                    'update_delete' => 'update',
                    'price' => $offer['price']
                ]
            ]
        ];

        $endpoint = '/api/offers';

        try {
            $response = $this->etsyClient->post($endpoint, $payload);
            sleep(2);
            $import = $this->getImportStatus($response['import_id']);

            if ($import['has_error_report']) {

                $error_report = $this->getErrorReport($response['import_id']);
                Logger::error('MiraklProductStockService: Mirakl updateInventory error', [
                    'errorMessage' => $error_report,
                    'payload' => $payload
                ]);

                ExceptionHelper::throwExceptionFromStatusCode(400);
            }

            return [
                'message' => 'Stocks successfully pushed to Mirakl.',
                'responseData' => $response,
                'payload' => $payload
            ];
        } catch (GuzzleException $e) {
            Logger::error('MiraklProductStockService: Mirakl updateInventory exception', [
                'errorMessage' => $e->getMessage()
            ]);
            ExceptionHelper::throwExceptionFromStatusCode($e->getCode() ?? 0);
        }
    }
}
