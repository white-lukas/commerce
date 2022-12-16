<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\commerce\base;

use craft\commerce\elements\Order;
use craft\commerce\models\LineItem;
use craft\commerce\models\Store;

/**
 * Interface Purchasable
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
interface PurchasableInterface
{
    /**
     * Returns the element’s ID.
     */
    public function getId(): ?int;

    /**
     * Returns the store for the current instance of the purchasable.
     *
     * @return Store
     */
    public function getStore(): Store;

    /**
     * Returns the element’s status.
     *
     * @return string|null
     * @TODO figure out what to do with this as it comes from the `ElementInterface`
     */
    public function getStatus(): ?string;

    /**
     * Returns the base price the item will be added to the line item with.
     *
     * @return float|null decimal(14,4)
     */
    public function getBasePrice(?Store $store = null): ?float;

    /**
     * @param Store|null $store
     * @return float|null
     * @since 5.0.0
     */
    public function getBasePromotionalPrice(?Store $store = null): ?float;

    /**
     * @param Store|null $store
     * @return int|null
     * @since 5.0.0
     */
    public function getStock(?Store $store = null): ?int;

    /**
     * @param Store|null $store
     * @return bool
     * @since 5.0.0
     */
    public function getHasUnlimitedStock(?Store $store = null): bool;

    /**
     * @param Store|null $store
     * @return bool
     * @since 5.0.0
     */
    public function getPromotable(?Store $store = null): bool;

    /**
     * @param Store|null $store
     * @return bool
     * @since 5.0.0
     */
    public function getAvailableForPurchase(?Store $store = null): bool;

    /**
     * @param Store|null $store
     * @return int|null
     * @since 5.0.0
     */
    public function getMinQty(?Store $store = null): ?int;

    /**
     * @param Store|null $store
     * @return int|null
     * @since 5.0.0
     */
    public function getMaxQty(?Store $store = null): ?int;

    /**
     * @param Store|null $store
     * @return bool|null
     * @since 5.0.0
     */
    public function getFreeShipping(?Store $store = null): ?bool;

    /**
     * Returns the live price including catalog rule pricing.
     *
     * @param Store|null $store
     * @return float|null decimal(14,4)
     */
    public function getPrice(?Store $store = null): ?float;

    /**
     * Returns the live promotional price including the catalog rule pricing.
     *
     * @param Store|null $store
     * @return float|null decimal(14,4)
     * @since 5.0.0
     */
    public function getPromotionalPrice(?Store $store = null): ?float;

    /**
     * Returns the actual price the purchasable will be sold for.
     *
     * @param Store|null $store
     * @return float|null decimal(14,4)
     */
    public function getSalePrice(?Store $store = null): ?float;

    /**
     * @param Store|null $store
     * @return bool
     * @since 5.0.0
     */
    public function getOnPromotion(?Store $store = null): bool;

    /**
     * Returns a unique code. Unique as per the commerce_purchasables table.
     */
    public function getSku(): string;

    /**
     * Returns your element's title or any additional descriptive information.
     */
    public function getDescription(): string;

    /**
     * Returns the purchasable's tax category ID.
     */
    public function getTaxCategoryId(): int;

    /**
     * Returns the purchasable's shipping category ID.
     */
    public function getShippingCategoryId(): int;

    /**
     * Returns whether the purchasable is currently available for purchase.
     */
    public function getIsAvailable(): bool;

    /**
     * Populates the line item when this purchasable is found on it. Called when
     * Purchasable is added to the cart and when the cart recalculates.
     * This is your chance to modify the weight, height, width, length, price
     * and saleAmount. This is called before any LineItems::EVENT_POPULATE_LINE_ITEM event listeners.
     */
    public function populateLineItem(LineItem $lineItem): void;

    /**
     * Returns an array of data that is serializable to json for storing a line
     * item at time of adding to the cart or order.
     */
    public function getSnapshot(): array;

    /**
     * Returns any validation rules this purchasable required the line item to have.
     *
     * @param LineItem $lineItem
     * @return array
     */
    public function getLineItemRules(LineItem $lineItem): array;

    /**
     * Runs any logic needed for this purchasable after it was on an order that was just completed (not when an order was paid, although paying an order will complete it).
     *
     * This is called for each line item the purchasable was contained within.
     *
     * @param Order $order
     * @param LineItem $lineItem
     */
    public function afterOrderComplete(Order $order, LineItem $lineItem): void;

    /**
     * Returns whether this purchasable has free shipping.
     */
    public function hasFreeShipping(): bool;

    /**
     * Returns whether this purchasable can be shipped and whether it is counted in shipping calculations.
     */
    public function getIsShippable(): bool;

    /**
     * Returns whether this purchasable is exempt from taxes.
     */
    public function getIsTaxable(): bool;

    /**
     * Returns whether this purchasable can be subject to discounts or sales.
     */
    public function getIsPromotable(): bool;

    /**
     * Returns the source param used for knowing if a promotion category is related to this purchasable.
     *
     * @return mixed
     */
    public function getPromotionRelationSource(): mixed;

    /**
     * Returns the purchasable's weight.
     *
     * @return float|null
     * @since 5.0.0
     */
    public function getWeight(): ?float;

    /**
     * Returns the purchasable's height.
     *
     * @return float|null
     * @since 5.0.0
     */
    public function getHeight(): ?float;

    /**
     * Returns the purchasable's length.
     *
     * @return float|null
     * @since 5.0.0
     */
    public function getLength(): ?float;

    /**
     * Returns the purchasable's width.
     *
     * @return float|null
     * @since 5.0.0
     */
    public function getWidth(): ?float;
}
