<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for base currency
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Directory_Block_Adminhtml_Frontend_Currency_Base extends Magento_Backend_Block_System_Config_Form_Field
{
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        if ($this->getRequest()->getParam('website') != '') {
            $priceScope = Mage::app()->getStore()->getConfig(Magento_Core_Model_Store::XML_PATH_PRICE_SCOPE);
            if ($priceScope == Magento_Core_Model_Store::PRICE_SCOPE_GLOBAL) {
                return '';
            }
        }
        return parent::render($element);
    }
}
