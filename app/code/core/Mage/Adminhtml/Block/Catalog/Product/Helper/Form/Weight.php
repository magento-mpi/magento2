<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product form weight field helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight extends Varien_Data_Form_Element_Text
{
    /**
     * Validation classes for weight field which corresponds to DECIMAL(12,4) SQL type
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->addClass('validate-number validate-zero-or-greater validate-number-range number-range-0-99999999.9999');
    }
}
