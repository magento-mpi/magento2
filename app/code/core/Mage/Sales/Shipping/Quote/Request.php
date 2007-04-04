<?php
/**
 * Fields:
 * - origin: 
 *   - country: US
 *   - region: CA
 *   - zip: 90034
 * - dest: 
 *   - country: US
 *   - region: NY
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
 * - insurance: true
 * - conditionName: package_weight (tablerate)
 */
class Mage_Sales_Shipping_Quote_Request extends Varien_DataObject
{

}
