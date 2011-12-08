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
 * Adminhtml catalog super product link grid checkbox renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Grid_Renderer_Type
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $type = $row->getData($this->getColumn()->getIndex());
        $devices = Mage::helper('Mage_XmlConnect_Helper_Data')->getSupportedDevices();
        if (isset($devices[$type])) {
            return $devices[$type];
        } else {
            return $type;
        }
    }
}
