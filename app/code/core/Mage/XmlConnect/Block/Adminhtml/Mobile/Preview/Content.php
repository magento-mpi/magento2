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
 * XmlConnect preview content block
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Content extends Mage_Adminhtml_Block_Template
{
    /**
     * Set path to template used for generating block's output.
     *
     * @param string $templateType
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Content
     */
    public function setTemplate($templateType)
    {
        $deviceType = Mage::helper('Mage_XmlConnect_Helper_Data')->getDeviceType();
        parent::setTemplate('Mage_XmlConnect::edit/tab/design/preview/' . $templateType . '_' . $deviceType . '.phtml');
        return $this;
    }
}