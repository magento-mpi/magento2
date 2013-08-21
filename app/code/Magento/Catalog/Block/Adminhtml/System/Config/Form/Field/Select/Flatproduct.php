<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Config Field Select Flat Product Block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Adminhtml_System_Config_Form_Field_Select_Flatproduct
    extends Magento_Backend_Block_System_Config_Form_Field
{
    /**
     * Retrieve Element HTML
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element) {
        if (!$this->helper('Magento_Catalog_Helper_Product_Flat')->isBuilt()) {
            $element->setDisabled(true)
                ->setValue(0);
        }
        return parent::_getElementHtml($element);
    }

}
