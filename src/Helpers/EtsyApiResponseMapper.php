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
     * @param array $listings
     * @param string|null $nextPageToken
     *
     * @return ProductDataCollection
     * @throws UnknownProperties
     */
    public static function mapProductsToProductDataCollection(array $listings, ?string $nextPageToken = null): ProductDataCollection
    {
        $productDataArray = [];

        foreach ($listings as $listing) {
            $inventory = $listing['inventory']['products'] ?? [];

            foreach ($inventory as $product) {
                $offering = $product['offerings'][0] ?? null;

                $productDataArray[] = new ProductData(
                    productId: (string)$listing['listing_id'],
                    variantId: (string)$product['product_id'],
                    inventoryItemId: (string)$product['product_id'],
                    sku: $product['sku'] ?? '',
                    productName: $listing['title'],
                    warehouseLocationId: null,
                    warehouseCustomerId: null,
                    currentInventory: $offering['quantity'] ?? 0,
                    price: $offering['price']['amount'] ?? 0,
                    remoteStatus: $listing['state'] === 'active' ? 'ACTIVE' : 'INACTIVE',
                    source: 'etsy',
                    modifyDate: DateHelper::toUTCCarbon(Carbon::createFromTimestamp($listing['last_modified_timestamp'])),
                    createDate: DateHelper::toUTCCarbon(Carbon::createFromTimestamp($listing['creation_timestamp'])),
                    unfulfillableInventory: BigDecimal::zero(),
                );
            }
        }

        return new ProductDataCollection(
            products: $productDataArray,
            nextPageToken: $nextPageToken
        );
    }
}
