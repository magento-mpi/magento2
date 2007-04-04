<?php
/**
 * Fields:
 * - origin: 
 *   - countryId: 223
 *   - regionId: 1
 *   - zip: 90034
 * - dest: 
 *   - countryId: 223
 *   - regionId: 2
 *   - zip: 01005
 * - package: 
 *   - value: $100
 *   - weight: 1.5 lb
 *   - height: 10"
 *   - width: 10"
 *   - depth: 10"
 * - order: 
 *   - totalQty: 10
 *   - subtotal: $100
 * - option
 *   - insurance: true
 * - table (shiptable)
 *   - conditionName: package_weight
 * - limit
 *   - vendor
 *   - method
 */
class Mage_Sales_Shipping_Quote_Request extends Varien_DataObject
{

}
