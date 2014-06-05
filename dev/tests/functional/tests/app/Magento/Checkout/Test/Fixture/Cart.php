<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class Cart
 *
 * @package Magento\Checkout\Test\Fixture
 */
class Cart extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Checkout\Test\Repository\Cart';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Checkout\Test\Handler\Cart\CartInterface';

    protected $defaultDataSet = [];

    protected $item_id = [
        'attribute_code' => 'item_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $quote_id = [
        'attribute_code' => 'quote_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $created_at = [
        'attribute_code' => 'created_at',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => 'CURRENT_TIMESTAMP',
        'input' => '',
    ];

    protected $updated_at = [
        'attribute_code' => 'updated_at',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '0000-00-00 00:00:00',
        'input' => '',
    ];

    protected $product_id = [
        'attribute_code' => 'product_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_id = [
        'attribute_code' => 'store_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $parent_item_id = [
        'attribute_code' => 'parent_item_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_virtual = [
        'attribute_code' => 'is_virtual',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $sku = [
        'attribute_code' => 'sku',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $name = [
        'attribute_code' => 'name',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $description = [
        'attribute_code' => 'description',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $applied_rule_ids = [
        'attribute_code' => 'applied_rule_ids',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $additional_data = [
        'attribute_code' => 'additional_data',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_qty_decimal = [
        'attribute_code' => 'is_qty_decimal',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $no_discount = [
        'attribute_code' => 'no_discount',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $weight = [
        'attribute_code' => 'weight',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $qty = [
        'attribute_code' => 'qty',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $price = [
        'attribute_code' => 'price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $base_price = [
        'attribute_code' => 'base_price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $custom_price = [
        'attribute_code' => 'custom_price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $discount_percent = [
        'attribute_code' => 'discount_percent',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $discount_amount = [
        'attribute_code' => 'discount_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $base_discount_amount = [
        'attribute_code' => 'base_discount_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $tax_percent = [
        'attribute_code' => 'tax_percent',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $tax_amount = [
        'attribute_code' => 'tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $base_tax_amount = [
        'attribute_code' => 'base_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $row_total = [
        'attribute_code' => 'row_total',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $base_row_total = [
        'attribute_code' => 'base_row_total',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $row_total_with_discount = [
        'attribute_code' => 'row_total_with_discount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $row_weight = [
        'attribute_code' => 'row_weight',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '0.0000',
        'input' => '',
    ];

    protected $product_type = [
        'attribute_code' => 'product_type',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_tax_before_discount = [
        'attribute_code' => 'base_tax_before_discount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $tax_before_discount = [
        'attribute_code' => 'tax_before_discount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $original_custom_price = [
        'attribute_code' => 'original_custom_price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $redirect_url = [
        'attribute_code' => 'redirect_url',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_cost = [
        'attribute_code' => 'base_cost',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $price_incl_tax = [
        'attribute_code' => 'price_incl_tax',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_price_incl_tax = [
        'attribute_code' => 'base_price_incl_tax',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $row_total_incl_tax = [
        'attribute_code' => 'row_total_incl_tax',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_row_total_incl_tax = [
        'attribute_code' => 'base_row_total_incl_tax',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $hidden_tax_amount = [
        'attribute_code' => 'hidden_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_hidden_tax_amount = [
        'attribute_code' => 'base_hidden_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gift_message_id = [
        'attribute_code' => 'gift_message_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $giftregistry_item_id = [
        'attribute_code' => 'giftregistry_item_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_id = [
        'attribute_code' => 'gw_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_base_price = [
        'attribute_code' => 'gw_base_price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_price = [
        'attribute_code' => 'gw_price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_base_tax_amount = [
        'attribute_code' => 'gw_base_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_tax_amount = [
        'attribute_code' => 'gw_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $free_shipping = [
        'attribute_code' => 'free_shipping',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $event_id = [
        'attribute_code' => 'event_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $weee_tax_disposition = [
        'attribute_code' => 'weee_tax_disposition',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $weee_tax_row_disposition = [
        'attribute_code' => 'weee_tax_row_disposition',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_weee_tax_disposition = [
        'attribute_code' => 'base_weee_tax_disposition',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_weee_tax_row_disposition = [
        'attribute_code' => 'base_weee_tax_row_disposition',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $weee_tax_applied = [
        'attribute_code' => 'weee_tax_applied',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $weee_tax_applied_amount = [
        'attribute_code' => 'weee_tax_applied_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $weee_tax_applied_row_amount = [
        'attribute_code' => 'weee_tax_applied_row_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_weee_tax_applied_amount = [
        'attribute_code' => 'base_weee_tax_applied_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_weee_tax_applied_row_amnt = [
        'attribute_code' => 'base_weee_tax_applied_row_amnt',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    public function getItemId()
    {
        return $this->getData('item_id');
    }

    public function getQuoteId()
    {
        return $this->getData('quote_id');
    }

    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    public function getProductId()
    {
        return $this->getData('product_id');
    }

    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    public function getParentItemId()
    {
        return $this->getData('parent_item_id');
    }

    public function getIsVirtual()
    {
        return $this->getData('is_virtual');
    }

    public function getSku()
    {
        return $this->getData('sku');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getDescription()
    {
        return $this->getData('description');
    }

    public function getAppliedRuleIds()
    {
        return $this->getData('applied_rule_ids');
    }

    public function getAdditionalData()
    {
        return $this->getData('additional_data');
    }

    public function getIsQtyDecimal()
    {
        return $this->getData('is_qty_decimal');
    }

    public function getNoDiscount()
    {
        return $this->getData('no_discount');
    }

    public function getWeight()
    {
        return $this->getData('weight');
    }

    public function getQty()
    {
        return $this->getData('qty');
    }

    public function getPrice()
    {
        return $this->getData('price');
    }

    public function getBasePrice()
    {
        return $this->getData('base_price');
    }

    public function getCustomPrice()
    {
        return $this->getData('custom_price');
    }

    public function getDiscountPercent()
    {
        return $this->getData('discount_percent');
    }

    public function getDiscountAmount()
    {
        return $this->getData('discount_amount');
    }

    public function getBaseDiscountAmount()
    {
        return $this->getData('base_discount_amount');
    }

    public function getTaxPercent()
    {
        return $this->getData('tax_percent');
    }

    public function getTaxAmount()
    {
        return $this->getData('tax_amount');
    }

    public function getBaseTaxAmount()
    {
        return $this->getData('base_tax_amount');
    }

    public function getRowTotal()
    {
        return $this->getData('row_total');
    }

    public function getBaseRowTotal()
    {
        return $this->getData('base_row_total');
    }

    public function getRowTotalWithDiscount()
    {
        return $this->getData('row_total_with_discount');
    }

    public function getRowWeight()
    {
        return $this->getData('row_weight');
    }

    public function getProductType()
    {
        return $this->getData('product_type');
    }

    public function getBaseTaxBeforeDiscount()
    {
        return $this->getData('base_tax_before_discount');
    }

    public function getTaxBeforeDiscount()
    {
        return $this->getData('tax_before_discount');
    }

    public function getOriginalCustomPrice()
    {
        return $this->getData('original_custom_price');
    }

    public function getRedirectUrl()
    {
        return $this->getData('redirect_url');
    }

    public function getBaseCost()
    {
        return $this->getData('base_cost');
    }

    public function getPriceInclTax()
    {
        return $this->getData('price_incl_tax');
    }

    public function getBasePriceInclTax()
    {
        return $this->getData('base_price_incl_tax');
    }

    public function getRowTotalInclTax()
    {
        return $this->getData('row_total_incl_tax');
    }

    public function getBaseRowTotalInclTax()
    {
        return $this->getData('base_row_total_incl_tax');
    }

    public function getHiddenTaxAmount()
    {
        return $this->getData('hidden_tax_amount');
    }

    public function getBaseHiddenTaxAmount()
    {
        return $this->getData('base_hidden_tax_amount');
    }

    public function getGiftMessageId()
    {
        return $this->getData('gift_message_id');
    }

    public function getGiftregistryItemId()
    {
        return $this->getData('giftregistry_item_id');
    }

    public function getGwId()
    {
        return $this->getData('gw_id');
    }

    public function getGwBasePrice()
    {
        return $this->getData('gw_base_price');
    }

    public function getGwPrice()
    {
        return $this->getData('gw_price');
    }

    public function getGwBaseTaxAmount()
    {
        return $this->getData('gw_base_tax_amount');
    }

    public function getGwTaxAmount()
    {
        return $this->getData('gw_tax_amount');
    }

    public function getFreeShipping()
    {
        return $this->getData('free_shipping');
    }

    public function getEventId()
    {
        return $this->getData('event_id');
    }

    public function getWeeeTaxDisposition()
    {
        return $this->getData('weee_tax_disposition');
    }

    public function getWeeeTaxRowDisposition()
    {
        return $this->getData('weee_tax_row_disposition');
    }

    public function getBaseWeeeTaxDisposition()
    {
        return $this->getData('base_weee_tax_disposition');
    }

    public function getBaseWeeeTaxRowDisposition()
    {
        return $this->getData('base_weee_tax_row_disposition');
    }

    public function getWeeeTaxApplied()
    {
        return $this->getData('weee_tax_applied');
    }

    public function getWeeeTaxAppliedAmount()
    {
        return $this->getData('weee_tax_applied_amount');
    }

    public function getWeeeTaxAppliedRowAmount()
    {
        return $this->getData('weee_tax_applied_row_amount');
    }

    public function getBaseWeeeTaxAppliedAmount()
    {
        return $this->getData('base_weee_tax_applied_amount');
    }

    public function getBaseWeeeTaxAppliedRowAmnt()
    {
        return $this->getData('base_weee_tax_applied_row_amnt');
    }
}
