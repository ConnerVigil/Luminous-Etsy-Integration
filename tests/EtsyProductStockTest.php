<?php


namespace JoinLuminous\MiraklOms\Tests;

use JoinLuminous\MiraklOms\Services\MiraklProductStockService;
use JoinLuminous\OmsContracts\Data\AppIntegrationAccountData;
use PHPUnit\Framework\TestCase;

class MiraklProductStockTest extends TestCase
{

    public function testPushStock()
    {
        $configData = [
            'shopKey' => 'be0da337-65ee-4dc0-908d-8d4dacbe5c14',
            'baseUrl' => 'https://macysus-prod.mirakl.net',
        ];
        $appIntegrationAccountData = new AppIntegrationAccountData([
            'id' => 'test',
            'label' => 'test',
            'app' => 'test',
            'authType' => 'test',
            'credentials' => $configData
        ]);

        $miraklProductStockService = new miraklProductStockService($appIntegrationAccountData);

        $offer = $miraklProductStockService->getOffer('22251880');

        $productStockData = [
            'inventoryItemId' => '22251880',
            'availableQuantity' => 1
        ];

        $productStockData = (object) $productStockData;
        $productStockCollection = new ProductStockDataCollection(productStocks: [$productStockData]);

        try {
            $result = $miraklProductStockService->pushStock($productStockCollection);

            $this->assertIsArray($result);
            $this->assertArrayHasKey('message', $result);
            $this->assertEquals('Stocks successfully pushed to Mirakl.', $result['message']);
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

        $miraklProductStockService->pushStock($productStockCollection);
    }


    protected function tearDown(): void
    {
        // Clean up resources or reset states if needed
    }

}
