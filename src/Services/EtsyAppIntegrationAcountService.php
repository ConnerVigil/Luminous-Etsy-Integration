<?php

namespace JoinLuminous\EtsyOms\Services;

use JoinLuminous\EtsyOms\config\EtsyConfig;
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
                'keyString' => 'required|string',
                'callbackUrl' => 'required|string',
                'stateString' => 'required|string',
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
                    label: 'Key String',
                    values: [],
                    attributes: []
                ),
                'callbackUrl' => new FieldConfigData(
                    type: FormElementConstant::INPUT_TEXT,
                    label: 'Callback URL',
                    values: [],
                    attributes: []
                ),
                'stateString' => new FieldConfigData(
                    type: FormElementConstant::INPUT_TEXT,
                    label: 'State String',
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
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getScriptUrl(): string
    {
        return '';
    }
}
