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
 * Adminhtml Config Field Select Flat Product Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Select_Flatproduct
    extends Mage_Backend_Block_System_Config_Form_Field
{
    /**
     * Retrieve Element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        if (!Mage::helper('Mage_Catalog_Helper_Product_Flat')->isBuilt()) {
            $element->setDisabled(true)
                ->setValue(0);
        }
        return parent::_getElementHtml($element);
    }

}
