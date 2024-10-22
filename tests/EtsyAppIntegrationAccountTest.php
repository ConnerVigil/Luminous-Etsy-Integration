<?php

namespace JoinLuminous\EtsyOms\Tests;

use JoinLuminous\EtsyOms\config\EtsyConfig;
use JoinLuminous\EtsyOms\Services\EtsyAppIntegrationAcountService;
use JoinLuminous\OmsContracts\Data\BaseConfigData;
use JoinLuminous\OmsContracts\Data\Config\FieldConfigData;
use JoinLuminous\OmsContracts\Constants\FormElementConstant;
use JoinLuminous\OmsContracts\Exceptions\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;

class EtsyAppIntegrationAccountTest extends TestCase
{

    public function testGetAuthType()
    {
        $service = new EtsyAppIntegrationAcountService();
        $this->assertEquals('API_TOKEN', $service->getAuthType());
    }

    public function testCreateConfig()
    {
        $service = new EtsyAppIntegrationAcountService();
        $credentials = [
            'keyString' => 'test_key_string',
            'baseUrl' => 'test_base_url',
        ];

        /** @var EtsyConfig $config */
        $config = $service->createConfig($credentials);
        $this->assertInstanceOf(EtsyConfig::class, $config);
        $this->assertEquals('test_key_string', $config->keyString);
        $this->assertEquals('test_base_url', $config->baseUrl);
    }

    public function testGetRules()
    {
        $service = new EtsyAppIntegrationAcountService();
        $rules = $service->getRules();

        $expectedRules = [
            'credentials' => [
                'keyString' => 'required|string',
                'baseUrl' => 'required|string',
                'shopId' => 'required|string',
            ],
            'settings' => [
                'get_products' => 'boolean'
            ]
        ];

        $this->assertEquals($expectedRules, $rules);
    }

    public function testTestCredentials()
    {
        $configData = [
            'keyString' => 'wdqtud5uqgl8b59v4vrkw5j5',
            'baseUrl' => 'https://openapi.etsy.com',
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
        $this->assertEquals('OAUTH', $service->getIntegrationType());
    }

    public function testGetFields()
    {
        $service = new EtsyAppIntegrationAcountService();
        $fields = $service->getFields();

        $expectedFields = [
            'credentials' => [
                'keyString' => new FieldConfigData(
                    type: FormElementConstant::INPUT_TEXT,
                    label: 'Shop Key',
                    values: [],
                    attributes: []
                ),
                'baseUrl' => new FieldConfigData(
                    type: FormElementConstant::INPUT_TEXT,
                    label: 'Base URL',
                    values: [],
                    attributes: []
                ),
                'shopId' => new FieldConfigData(
                    type: FormElementConstant::INPUT_TEXT,
                    label: 'Shop ID',
                    values: [],
                    attributes: []
                )
            ],
            'settings' => [
                'get_products' => new FieldConfigData(
                    type: FormElementConstant::CHECKBOX,
                    label: 'Get Products',
                    values: [],
                    attributes: []
                )
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
