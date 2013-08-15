<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sale price effective date attribute model.
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Attribute_SalePriceEffectiveDate extends Magento_GoogleShopping_Model_Attribute_Default
{
    /**
     * Set current attribute to entry (for specified product)
     *
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Gdata_Gshopping_Entry $entry
     * @return Magento_Gdata_Gshopping_Entry
     */
    public function convertAttribute($product, $entry)
    {
        $effectiveDateFrom = $this->getGroupAttributeSalePriceEffectiveDateFrom();
        $fromValue = $effectiveDateFrom->getProductAttributeValue($product);

        $effectiveDateTo = $this->getGroupAttributeSalePriceEffectiveDateTo();
        $toValue = $effectiveDateTo->getProductAttributeValue($product);

        $from = $to = null;
        if (!empty($fromValue) && Zend_Date::isDate($fromValue, Zend_Date::ATOM)) {
            $from = new Zend_Date($fromValue, Zend_Date::ATOM);
        }
        if (!empty($toValue) && Zend_Date::isDate($toValue, Zend_Date::ATOM)) {
            $to = new Zend_Date($toValue, Zend_Date::ATOM);
        }

        $dateString = null;
        // if we have from an to dates, and if these dates are correct
        if (!is_null($from) && !is_null($to) && $from->isEarlier($to)) {
            $dateString = $from->toString(Zend_Date::ATOM) . '/' . $to->toString(Zend_Date::ATOM);
        }

        // if we have only "from" date, send "from" day
        if (!is_null($from) && is_null($to)) {
            $dateString = $from->toString('YYYY-MM-dd');
        }

        // if we have only "to" date, use "now" date for "from"
        if (is_null($from) && !is_null($to)) {
            $from = new Zend_Date();
            // if "now" date is earlier than "to" date
            if ($from->isEarlier($to)) {
                $dateString = $from->toString(Zend_Date::ATOM) . '/' . $to->toString(Zend_Date::ATOM);
            }
        }

        if (!is_null($dateString)) {
            $this->_setAttribute($entry, 'sale_price_effective_date', self::ATTRIBUTE_TYPE_TEXT, $dateString);
        }

        return $entry;
    }
}
