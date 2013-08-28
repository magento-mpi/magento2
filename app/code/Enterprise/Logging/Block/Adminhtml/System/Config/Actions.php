<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Action group checkboxes renderer for system configuration
 */
class Enterprise_Logging_Block_Adminhtml_System_Config_Actions
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
        return Mage::getSingleton('Enterprise_Logging_Model_Config')->getLabels();
    }

    /**
     * Check whether specified group is active
     *
     * @param string $key
     * @return bool
     */
    public function getIsChecked($key)
    {
        return Mage::getSingleton('Enterprise_Logging_Model_Config')->isActive($key, true);
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
