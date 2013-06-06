<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Current ip block
 */
class Enterprise_Pbridge_Block_Adminhtml_System_Config_Currentip extends Mage_Backend_Block_System_Config_Form_Field
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Enterprise_Pbridge::system/config/currentip.phtml';

    /**
     * Unset some non-related element parameters
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Set current ip
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->addData(array(
            'current_ip' => Mage::helper('Mage_Core_Helper_Http')->getRemoteAddr()
        ));
        return $this->_toHtml();
    }
}
