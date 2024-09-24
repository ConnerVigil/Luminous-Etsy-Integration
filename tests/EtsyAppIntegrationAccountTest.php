<?php

namespace JoinLuminous\EtsyOms\Tests;

use JoinLuminous\EtsyOms\config\EtsyConfig;
use JoinLuminous\EtsyOms\Services\EtsyAppIntegrationAcountService;
use JoinLuminous\OmsContracts\Data\BaseConfigData;
use JoinLuminous\OmsContracts\Exceptions\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;

class EtsyAppIntegrationAccountTest extends TestCase
{

    public function testGetAuthType()
    {
        $service = new EtsyAppIntegrationAcountService();
        $this->assertEquals('BASIC_AUTH', $service->getAuthType());
    }

    public function testCreateConfig()
    {
        $service = new EtsyAppIntegrationAcountService();
        $credentials = [
            'shop_key' => 'test_shop_key',
            'base_url' => 'test_base_url',
        ];

        /** @var EtsyConfig $config */
        $config = $service->createConfig($credentials);

        $this->assertInstanceOf(EtsyConfig::class, $config);
        $this->assertEquals('test_shop_key', $config->keyString);
        $this->assertEquals('test_base_url', $config->baseUrl);
    }

    public function testGetRules()
    {
        $service = new EtsyAppIntegrationAcountService();
        $rules = $service->getRules();

        $expectedRules = [
            'shop_key' => 'required|string',
            'base_url' => 'required|string'
        ];

        $this->assertEquals($expectedRules, $rules);
    }

    public function testTestCredentials()
    {
        $configData = [
            'shop_key' => '', // TODO: add correct shop key
            'base_url' => '', // TODO: add correct base url
        ];

        $service = new EtsyAppIntegrationAcountService();
        $config = $service->createConfig($configData);
        $result = $service->testCredentials($config);
        $this->assertTrue($result);
    }

    public function testTestCredentialsWithInvalidConfig()
    {
        $this->expectException(InvalidConfigurationException::class);

        $service = new EtsyAppIntegrationAcountService();
        $invalidConfigData = $this->createMock(BaseConfigData::class);

        $service->testCredentials($invalidConfigData);
    }

    public function testGetIntegrationAccountApp()
    {
        $service = new EtsyAppIntegrationAcountService();
        $this->assertEquals('etsy', $service->getIntegrationAccountApp());
    }

    public function testGetIntegrationType()
    {
        $service = new EtsyAppIntegrationAcountService();
        $this->assertEquals('basic_auth', $service->getIntegrationType());
    }

    public function testGetFields()
    {
        $service = new EtsyAppIntegrationAcountService();
        $fields = $service->getFields();

        $expectedFields = [
            'credentials' => [
                'shop_key' => [
                    'type' => 'input_text',
                    'values' => [],
                    'attributes' => []
                ],
                'base_url' => [
                    'type' => 'input_text',
                    'values' => [],
                    'attributes' => []
                ]
            ],
            'settings' => [
                'get_products' => [
                    'type' => 'checkbox',
                    'values' => [],
                    'attributes' => []
                ]
            ]
        ];

        $this->assertEquals($expectedFields, $fields);
    }

    public function testGetActions()
    {
        $service = new EtsyAppIntegrationAcountService();
        $actions = $service->getActions();

        $expectedActions = [];

        $this->assertEquals($expectedActions, $actions);
    }

    public function testGetScriptUrl()
    {
        $service = new EtsyAppIntegrationAcountService();
        $this->assertEquals('', $service->getScriptUrl());
    }
}
