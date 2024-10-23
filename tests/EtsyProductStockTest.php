<?php

namespace JoinLuminous\EtsyOms\Tests;

use JoinLuminous\EtsyOms\Services\EtsyProductStockService;
use JoinLuminous\OmsContracts\Data\AppIntegrationAccountData;
use JoinLuminous\OmsContracts\Data\ProductStockDataCollection;
use JoinLuminous\OmsContracts\Data\ProductStockData;
use PHPUnit\Framework\TestCase;
use Brick\Math\BigDecimal;

class EtsyProductStockTest extends TestCase
{
    public function testPushStock()
    {
        $configData = [
            'keyString' => 'wdqtud5uqgl8b59v4vrkw5j5',
            'baseUrl' => 'https://openapi.etsy.com',
            'shopId' => '55051636',
        ];

        $appIntegrationAccountData = new AppIntegrationAccountData([
            'id' => 'test',
            'label' => 'test',
            'app' => 'test',
            'authType' => 'test',
            'credentials' => $configData
        ]);

        $etsyProductStockService = new EtsyProductStockService($appIntegrationAccountData);

        $listingId = '1813923221';
        $result = $etsyProductStockService->getListing($listingId);

        $productStockData = new ProductStockData([
            'productId' => $result['products'][0]['product_id'],
            'inventoryItemId' => $listingId,
            'availableQuantity' => BigDecimal::of(10)
        ]);

        $productStockCollection = new ProductStockDataCollection(productStocks: [$productStockData]);

        try {
            $result = $etsyProductStockService->pushStock($productStockCollection);

            $this->assertIsArray($result);
            $this->assertArrayHasKey('message', $result);
            $this->assertEquals('Stock push to Etsy completed.', $result['message']);
            // Add additional assertions based on the expected structure of the response data
        } catch (\Exception $e) {
            $this->fail('Exception thrown: ' . $e->getMessage());
        }

        $productStockData = new ProductStockData([
            'inventoryItemId' => $listingId,
            'availableQuantity' => BigDecimal::of($result['products'][0]['offerings'][0]['quantity'])
        ]);

        $productStockCollection = new ProductStockDataCollection(productStocks: [$productStockData]);
        $etsyProductStockService->pushStock($productStockCollection);
    }

    protected function tearDown(): void
    {
        // Clean up resources or reset states if needed
    }
}
