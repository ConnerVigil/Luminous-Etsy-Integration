<?php

namespace JoinLuminous\MiraklOms\Tests;

use JoinLuminous\MiraklOms\Services\MiraklAppIntegrationAccountService;
use JoinLuminous\MiraklOms\Config\MiraklConfig;
use JoinLuminous\OmsContracts\Data\BaseConfigData;
use JoinLuminous\OmsContracts\Exceptions\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;

class MiraklAppIntegrationAccountTest extends TestCase
{

    public function testGetAuthType()
    {
        $service = new MiraklAppIntegrationAccountService();
        $this->assertEquals('API_KEYS', $service->getAuthType());
    }

    public function testCreateConfig()
    {
        $service = new MiraklAppIntegrationAccountService();
        $credentials = [
            'shop_key' => 'test_shop_key',
            'base_url' => 'test_base_url',
        ];

        $config = $service->createConfig($credentials);

        $this->assertInstanceOf(MiraklConfig::class, $config);
        $this->assertEquals('test_shop_key', $config->shopKey);
        $this->assertEquals('test_base_url', $config->baseUrl);
    }

    public function testGetRules()
    {
        $service = new MiraklAppIntegrationAccountService();
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
            'shop_key' => 'be0da337-65ee-4dc0-908d-8d4dacbe5c14',
            'base_url' => 'https://macysus-prod.mirakl.net',
        ];

        $service = new MiraklAppIntegrationAccountService();

        $config = $service->createConfig($configData);

        $result = $service->testCredentials($config);

        $this->assertTrue($result);
    }

    public function testTestCredentialsWithInvalidConfig()
    {
        $this->expectException(InvalidConfigurationException::class);

        $service = new MiraklAppIntegrationAccountService();
        $invalidConfigData = $this->createMock(BaseConfigData::class);

        $service->testCredentials($invalidConfigData);
    }

    public function testGetIntegrationAccountApp()
    {
        $service = new MiraklAppIntegrationAccountService();
        $this->assertEquals('mirakl', $service->getIntegrationAccountApp());
    }

    public function testGetIntegrationType()
    {
        $service = new MiraklAppIntegrationAccountService();
        $this->assertEquals('basic_auth', $service->getIntegrationType());
    }

    public function testGetFields()
    {
        $service = new MiraklAppIntegrationAccountService();
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
        $service = new MiraklAppIntegrationAccountService();
        $actions = $service->getActions();

        $expectedActions = [];

        $this->assertEquals($expectedActions, $actions);
    }

    public function testGetScriptUrl()
    {
        $service = new MiraklAppIntegrationAccountService();
        $this->assertEquals('', $service->getScriptUrl());
    }

}
