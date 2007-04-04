<?php
/**
 * Fields:
 * - origin: 
 *   - country_id: 223
 *   - region_id: 1
 *   - zip: 90034
 * - dest: 
 *   - country_id: 223
 *   - region_id: 2
 *   - zip: 01005
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
 * - table (shiptable)
 *   - condition_name: package_weight
 * - limit
 *   - vendor
 *   - method
 */
class Mage_Sales_Shipping_Quote_Request extends Varien_DataObject
{

}
