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
        return IntegrationTypeConstant::BASIC_AUTH; // TODO: double check which type this is
    }

    /**
     * @inheritDoc
     */
    public function createConfig(array $credentials): BaseConfigData
    {
        return new EtsyConfig($credentials); // TODO: specify parameters explicitly
    }

    /**
     * @inheritDoc
     */
    public function getRules(): array
    {
        return [
            'credentials' => [
                'shop_key' => 'required|string',
                'base_url' => 'required|string',
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
                'shop_key' => new FieldConfigData(
                    type: FormElementConstant::INPUT_TEXT,
                    label: 'Shop Key',
                    values: [],
                    attributes: []
                ),
                'base_url' => new FieldConfigData(
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
            $response = $etsyClient->get('/api/offers'); // TODO: change to correct endpoint

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
