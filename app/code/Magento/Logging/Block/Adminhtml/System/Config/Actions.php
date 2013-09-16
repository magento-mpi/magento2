<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Action group checkboxes renderer for system configuration
 */
class Magento_Logging_Block_Adminhtml_System_Config_Actions
    extends Magento_Backend_Block_System_Config_Form_Field
{
    protected $_template = 'system/config/actions.phtml';

    /**
     * Action group labels getter
     *
     * @return array
     */
    public function getLabels()
    {
        return Mage::getSingleton('Magento_Logging_Model_Config')->getLabels();
    }

    /**
     * Check whether specified group is active
     *
     * @param string $key
     * @return bool
     */
    public function getIsChecked($key)
    {
        return Mage::getSingleton('Magento_Logging_Model_Config')->isEventGroupLogged($key);
    }

    /**
     * Render element html
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $this->setNamePrefix($element->getName())
            ->setHtmlId($element->getHtmlId());
        return $this->_toHtml();
    }
}
