<?php

namespace JoinLuminous\EtsyOms\Tests;

use JoinLuminous\EtsyOms\Services\EtsyProductService;
use JoinLuminous\OmsContracts\Data\AppIntegrationAccountData;
use JoinLuminous\OmsContracts\Data\Params\BaseParam;
use JoinLuminous\OmsContracts\Data\ProductDataCollection;
use PHPUnit\Framework\TestCase;

class EtsyProductTest extends TestCase
{
    public function testGetAppIntegrationAccountData()
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

        $etsyProductService = new EtsyProductService($appIntegrationAccountData);
        $this->assertSame($appIntegrationAccountData, $etsyProductService->getAppIntegrationAccountData());
    }

    public function testGetPaginatedProducts()
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

        $etsyProductService = new EtsyProductService($appIntegrationAccountData);
        $baseParam = new BaseParam();

        try {
            $productDataCollection = $etsyProductService->getPaginatedProducts($baseParam);

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
