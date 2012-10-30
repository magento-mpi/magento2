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
 * XmlConnect Tab items block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Tabitems extends Mage_Adminhtml_Block_Template
{
    /**
     * Set preview tab items template
     */
    protected function _construct()
    {
        parent::_construct();

        $deviceType = Mage::helper('Mage_XmlConnect_Helper_Data')->getDeviceType();
        if ($deviceType !== Mage_XmlConnect_Helper_Data::DEVICE_TYPE_DEFAULT) {
            $this->setTemplate(
                'edit/tab/design/preview/tab_items_' . $deviceType . '.phtml'
            );
        }
    }

    /**
     * Set active tab
     *
     * @param string $tab
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Tabitems
     */
    public function setActiveTab($tab)
    {
        Mage::helper('Mage_XmlConnect_Helper_Data')->getPreviewModel()->setActiveTab($tab);
        return $this;
    }
}
