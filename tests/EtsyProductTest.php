<?php

namespace JoinLuminous\MiraklOms\Tests;

use JoinLuminous\MiraklOms\Services\MiraklProductService;
use JoinLuminous\OmsContracts\Data\AppIntegrationAccountData;
use JoinLuminous\OmsContracts\Data\Params\BaseParam;
use JoinLuminous\OmsContracts\Data\ProductDataCollection;
use PHPUnit\Framework\TestCase;

class MiraklProductTest extends TestCase
{
    public function testGetAppIntegrationAccountData()
    {
        $configData = [
            'shopKey' => 'be0da337-65ee-4dc0-908d-8d4dacbe5c14',
            'baseUrl' => 'https://macysus-prod.mirakl.net',
        ];
        $appIntegrationAccountData = new AppIntegrationAccountData([
            'id'=> 'test',
            'label'=>'test',
            'app'=>'test',
            'authType'=>'test',
            'credentials' => $configData
        ]);

        $miraklProductService = new MiraklProductService($appIntegrationAccountData);
        $this->assertSame($appIntegrationAccountData, $miraklProductService->getAppIntegrationAccountData());
    }

    public function testGetPaginatedProducts()
    {
        $configData = [
            'shopKey' => 'be0da337-65ee-4dc0-908d-8d4dacbe5c14',
            'baseUrl' => 'https://macysus-prod.mirakl.net',
        ];
        $appIntegrationAccountData = new AppIntegrationAccountData([
            'id'=> 'test',
            'label'=>'test',
            'app'=>'test',
            'authType'=>'test',
            'credentials' => $configData
        ]);

        $miraklProductService = new MiraklProductService($appIntegrationAccountData);

        $baseParam = new BaseParam();

        try {
            $productDataCollection = $miraklProductService->getPaginatedProducts($baseParam);

            $this->assertInstanceOf(ProductDataCollection::class, $productDataCollection);
        } catch (\Exception $e) {
            $this->fail('Exception thrown: ' . $e->getMessage());
        }
    }


    protected function tearDown(): void
    {
        // Clean up resources or reset states if needed
    }
}
