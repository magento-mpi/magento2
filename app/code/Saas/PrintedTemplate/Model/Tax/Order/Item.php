<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Tax infoirmation for order item
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Tax_Order_Item extends Magento_Core_Model_Abstract
{
    /**
     * Model constructor. Initialize resource for data.
     */
    protected function _construct()
    {
        $this->_init('Saas_PrintedTemplate_Model_Resource_Tax_Order_Item');
    }

    /**
     * Return true if tax is applied after discount
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsTaxAfterDiscount()
    {
        return (bool)$this->_getData('is_tax_after_discount');
    }

    /**
     * Return true if discount is applied on price including taxes
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsDiscountOnInclTax()
    {
        return (bool)$this->_getData('is_discount_on_incl_tax');
    }
}
