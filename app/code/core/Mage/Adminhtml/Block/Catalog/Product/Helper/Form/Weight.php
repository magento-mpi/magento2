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
    /*
     * Add validate-zero-or-greater css class to weigh field
     * for input validation
     */
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->addClass('validate-zero-or-greater');
    }
}
