<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Enterprise_GiftCardAccount_Block_Adminhtml_System_Config_Generate extends Magento_Backend_Block_System_Config_Form_Field
{

    protected $_template = 'config/generate.phtml';

    /**
     * Get the button and scripts contents
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->_toHtml();
    }

    /**
     * Return code pool usage
     *
     * @return Magento_Object
     */
    public function getUsage()
    {
        return Mage::getModel('Enterprise_GiftCardAccount_Model_Pool')->getPoolUsageInfo();
    }
}
