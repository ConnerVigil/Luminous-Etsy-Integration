<?php

namespace JoinLuminous\EtsyOms\Services;

use Exception;
use JoinLuminous\EtsyOms\config\EtsyClient;
use JoinLuminous\EtsyOms\config\EtsyConfig;
use JoinLuminous\OmsContracts\Exceptions\InvalidConfigurationException;
use JoinLuminous\OmsContracts\Interfaces\OMSAppIntegrationAccountInterface;
use JoinLuminous\OmsContracts\Constants\IntegrationTypeConstant;
use JoinLuminous\OmsContracts\Data\BaseConfigData;
use JoinLuminous\OmsContracts\Data\Config\FieldConfigData;
use JoinLuminous\OmsContracts\Constants\FormElementConstant;
use JoinLuminous\OmsContracts\Constants\IntegrationAuthTypeConstant;

class EtsyAppIntegrationAcountService implements OMSAppIntegrationAccountInterface
{
    /**
     * @inheritDoc
     */
    public function getIntegrationAccountApp(): string
    {
        return "etsy";
    }

    /**
     * @inheritDoc
     */
    public function getIntegrationType(): string
    {
        return IntegrationTypeConstant::OAUTH;
    }

    /**
     * @inheritDoc
     */
    public function getAuthType(): string
    {
        return IntegrationAuthTypeConstant::API_TOKEN;
    }

    /**
     * @inheritDoc
     */
    public function createConfig(array $credentials): BaseConfigData
    {
        return new EtsyConfig(
            keyString: $credentials['keyString'],
            baseUrl: $credentials['baseUrl']
        );
    }

    /**
     * @inheritDoc
     */
    public function getRules(): array
    {
        return [
            'credentials' => [
                'keyString' => 'required|string',
                'baseUrl' => 'required|string',
            ],
            'settings' => [
                'get_products' => 'boolean'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        return [
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
    }

    /**
     * @inheritDoc
     */
    public function getActions(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function testCredentials(BaseConfigData $configData): bool
    {
        if (!($configData instanceof EtsyConfig)) {
            throw new InvalidConfigurationException('Config data is not an instance of EtsyConfig');
        }

        try {
            $etsyClient = new EtsyClient($configData);
            $response = $etsyClient->get('/v3/application/openapi-ping');
            return !empty($response);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function getScriptUrl(): string
    {
        return '';
    }
}
