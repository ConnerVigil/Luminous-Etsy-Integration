<?php

namespace JoinLuminous\EtsyOms\Helpers;

use Brick\Math\BigDecimal;
use Carbon\Carbon;
use JoinLuminous\OmsContracts\Data\ProductData;
use JoinLuminous\OmsContracts\Data\ProductDataCollection;
use JoinLuminous\OmsContracts\Helpers\DateHelper;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class EtsyApiResponseMapper
{
    /**
     * @param array $products
     * @param string|null $nextPageToken
     *
     * @return ProductDataCollection
     * @throws UnknownProperties
     */
    public static function mapProductsToProductDataCollection(array $products, ?string $nextPageToken = null): ProductDataCollection
    {
        $productDataArray = [];

        // TODO: make sure that this is mapping correctly
        foreach ($products as $product) {
            $productDataArray[] = new ProductData(
                productId: $product['offer_id'],
                variantId: $product['offer_id'],
                inventoryItemId: $product['offer_id'],
                sku: $product['shop_sku'],
                productName: $product['product_title'],
                warehouseLocationId: null,
                warehouseCustomerId: null,
                currentInventory: $product['quantity'] ?? 0,
                price: $product['price'],
                remoteStatus: $product['active'] ? 'ACTIVE' : 'INACTIVE',
                source: 'etsy',
                modifyDate: DateHelper::toUTCCarbon(Carbon::now()),
                createDate: DateHelper::toUTCCarbon(Carbon::now()),
                unfulfillableInventory: BigDecimal::zero(),
            );
        }

        return new ProductDataCollection(
            products: $productDataArray,
            nextPageToken: $nextPageToken
        );
    }
}
