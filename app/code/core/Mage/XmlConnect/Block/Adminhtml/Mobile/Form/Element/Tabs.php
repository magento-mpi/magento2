<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect tabs form element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Tabs
    extends Varien_Data_Form_Element_Text
{
    /**
     * Generate application tabs html
     *
     * @return string
     */
    public function getHtml()
    {
        if ((bool)Mage::getSingleton('Mage_Adminhtml_Model_Session')->getNewApplication()) {
            return '';
        }

        $blockClassName = Mage::getConfig()->getBlockClassName('Mage_Adminhtml_Block_Template');
        //TODO: Get rid from Mage::app
        $block = Mage::app()->getLayout()->createBlock($blockClassName);
        $device = Mage::helper('Mage_XmlConnect_Helper_Data')->getDeviceType();
        if (array_key_exists($device, Mage::helper('Mage_XmlConnect_Helper_Data')->getSupportedDevices())) {
            $template = 'Mage_XmlConnect::form/element/app_tabs_' . strtolower($device) . '.phtml';
        } else {
            Mage::throwException(
                $this->__('Device doesn\'t recognized. Unable to load a template.')
            );
        }

        $block->setTemplate($template);
        $tabs = Mage::getModel('Mage_XmlConnect_Model_Tabs', array('data' => $this->getValue()));
        $block->setTabs($tabs);
        $block->setName($this->getName());
        return $block->toHtml();
    }
}
