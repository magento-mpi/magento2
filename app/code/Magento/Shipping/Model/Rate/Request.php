<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fields:
 * - orig:
 *   - country_id: UK
 *   - region_id: 1
 *   - postcode: 90034
 * - dest:
 *   - country_id: UK
 *   - region_id: 2
 *   - postcode: 01005
 * - package:
 *   - value: $100
 *   - weight: 1.5 lb
 *   - height: 10"
 *   - width: 10"
 *   - depth: 10"
 * - order:
 *   - total_qty: 10
 *   - subtotal: $100
 * - option
 *   - insurance: true
 *   - handling: $1
 * - table (shiptable)
 *   - condition_name: package_weight
 * - limit
 *   - carrier: ups
 *   - method: 3dp
 * - ups
 *   - pickup: CC
 *   - container: CP
 *   - address: RES
 *
 * @method int getStoreId()
 * @method Magento_Shipping_Model_Rate_Request setStoreId(int $value)
 * @method int getWebsiteId()
 * @method Magento_Shipping_Model_Rate_Request setWebsiteId(int $value)
 * @method string getBaseCurrency()
 * @method Magento_Shipping_Model_Rate_Request setBaseCurrency(string $value)
 *
 * @method Magento_Shipping_Model_Rate_Request setAllItems(array $items)
 * @method array getAllItems()
 *
 * @method Magento_Shipping_Model_Rate_Request setOrigCountryId(string $value)
 * @method string getOrigCountryId()
 * @method Magento_Shipping_Model_Rate_Request setOrigRegionId(int $value)
 * @method int getOrigRegionId()
 * @method Magento_Shipping_Model_Rate_Request setOrigPostcode(string $value)
 * @method string getOrigPostcode()
 * @method Magento_Shipping_Model_Rate_Request setOrigCity(string $value)
 * @method string getOrigCity()
 *
 * @method Magento_Shipping_Model_Rate_Request setDestCountryId(string $value)
 * @method string getDestCountryId()
 * @method Magento_Shipping_Model_Rate_Request setDestRegionId(int $value)
 * @method int getDestRegionId()
 * @method Magento_Shipping_Model_Rate_Request setDestRegionCode(string $value)
 * @method string getDestRegionCode()
 * @method Magento_Shipping_Model_Rate_Request setDestPostcode(string $value)
 * @method string getDestPostcode()
 * @method Magento_Shipping_Model_Rate_Request setDestCity(string $value)
 * @method string getDestCity()
 * @method Magento_Shipping_Model_Rate_Request setDestStreet(string $value)
 * @method string getDestStreet()
 *
 * @method Magento_Shipping_Model_Rate_Request setPackageValue(float $value)
 * @method float getPackageValue()
 * @method Magento_Shipping_Model_Rate_Request setPackageValueWithDiscount(float $value)
 * @method float getPackageValueWithDiscount()
 * @method Magento_Shipping_Model_Rate_Request setPackagePhysicalValue(float $value)
 * @method float getPackagePhysicalValue()
 * @method Magento_Shipping_Model_Rate_Request setPackageQty(float $value)
 * @method float getPackageQty()
 * @method Magento_Shipping_Model_Rate_Request setPackageWeight(float $value)
 * @method float getPackageWeight()
 * @method Magento_Shipping_Model_Rate_Request setPackageHeight(int $value)
 * @method int getPackageHeight()
 * @method Magento_Shipping_Model_Rate_Request setPackageWidth(int $value)
 * @method int getPackageWidth()
 * @method Magento_Shipping_Model_Rate_Request setPackageDepth(int $value)
 * @method int getPackageDepth()
 * @method Magento_Shipping_Model_Rate_Request setPackageCurrency(string $value)
 * @method string getPackageCurrency()
 *
 * @method Magento_Shipping_Model_Rate_Request setOrderTotalQty(float $value)
 * @method float getOrderTotalQty()
 * @method Magento_Shipping_Model_Rate_Request setOrderSubtotal(float $value)
 * @method float getOrderSubtotal()
 *
 * @method boolean getFreeShipping()
 * @method Magento_Shipping_Model_Rate_Request setFreeShipping(boolean $flag)
 * @method float getFreeMethodWeight()
 * @method Magento_Shipping_Model_Rate_Request setFreeMethodWeight(float $value)
 *
 * @method Magento_Shipping_Model_Rate_Request setOptionInsurance(boolean $value)
 * @method boolean getOptionInsurance()
 * @method Magento_Shipping_Model_Rate_Request setOptionHandling(float $flag)
 * @method float getOptionHandling()
 *
 * @method Magento_Shipping_Model_Rate_Request setConditionName(array $value)
 * @method Magento_Shipping_Model_Rate_Request setConditionName(string $value)
 * @method string getConditionName()
 * @method array getConditionName()
 *
 * @method Magento_Shipping_Model_Rate_Request setLimitCarrier(string $value)
 * @method string getLimitCarrier()
 * @method Magento_Shipping_Model_Rate_Request setLimitMethod(string $value)
 * @method string getLimitMethod()
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Shipping_Model_Rate_Request extends Magento_Object
{}
