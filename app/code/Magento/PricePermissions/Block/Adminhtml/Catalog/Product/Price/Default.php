<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PricePermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default Product Price field renderer
*
 * @category    Magento
 * @package     Magento_PricePermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_PricePermissions_Block_Adminhtml_Catalog_Product_Price_Default
    extends Magento_Backend_Block_System_Config_Form_Field
{
    /**
     * Render Default Product Price field as disabled if user does not have enough permissions
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        if (!Mage::helper('Magento_PricePermissions_Helper_Data')->getCanAdminEditProductPrice()) {
            $element->setReadonly(true, true);
        }
        return parent::_getElementHtml($element);
    }
}
