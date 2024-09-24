<?php

namespace JoinLuminous\EtsyOms\Tests;

use JoinLuminous\EtsyOms\Services\EtsyProductStockService;
use JoinLuminous\OmsContracts\Data\AppIntegrationAccountData;
use JoinLuminous\OmsContracts\Data\ProductStockDataCollection;
use PHPUnit\Framework\TestCase;

class EtsyProductStockTest extends TestCase
{
    public function testPushStock()
    {
        $configData = [
            'shopKey' => '', // TODO: add correct shop key
            'baseUrl' => '', // TODO: add correct base url
        ];

        $appIntegrationAccountData = new AppIntegrationAccountData([
            'id' => 'test',
            'label' => 'test',
            'app' => 'test',
            'authType' => 'test',
            'credentials' => $configData
        ]);

        $etsyProductStockService = new EtsyProductStockService($appIntegrationAccountData);

        $offer = $etsyProductStockService->getOffer('22251880');

        $productStockData = [
            'inventoryItemId' => '22251880',
            'availableQuantity' => 1
        ];

        $productStockData = (object) $productStockData;
        $productStockCollection = new ProductStockDataCollection(productStocks: [$productStockData]);

        try {
            $result = $etsyProductStockService->pushStock($productStockCollection);

            $this->assertIsArray($result);
            $this->assertArrayHasKey('message', $result);
            $this->assertEquals('Stocks successfully pushed to Etsy.', $result['message']);
            // Add additional assertions based on the expected structure of the response data
        } catch (\Exception $e) {
            $this->fail('Exception thrown: ' . $e->getMessage());
        }

        $productStockData = [
            'inventoryItemId' => '22251880',
            'availableQuantity' => $offer['quantity']
        ];

        $productStockData = (object) $productStockData;
        $productStockCollection = new ProductStockDataCollection(productStocks: [$productStockData]);

        $etsyProductStockService->pushStock($productStockCollection);
    }


    protected function tearDown(): void
    {
        // Clean up resources or reset states if needed
    }
}
