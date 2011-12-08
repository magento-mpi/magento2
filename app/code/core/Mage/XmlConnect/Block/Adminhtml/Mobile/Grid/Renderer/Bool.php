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
 * XmlConnect status field grid renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Grid_Renderer_Bool
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render application status image
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $result = '';
        $status = (int) $row->getData($this->getColumn()->getIndex());
        $options = Mage::helper('Mage_XmlConnect_Helper_Data')->getStatusOptions();
        if ($status == Mage_XmlConnect_Model_Application::APP_STATUS_SUCCESS) {
            $result = '<img src="'
                . Mage::helper('Mage_XmlConnect_Helper_Image')->getSkinImagesUrl('gel_green.png')
                . '" >&nbsp;'
                . (isset($options[$status]) ? $options[$status] : '');
        } else if ($status == Mage_XmlConnect_Model_Application::APP_STATUS_INACTIVE) {
            $result = '<img src="'
            . Mage::helper('Mage_XmlConnect_Helper_Image')->getSkinImagesUrl('gel_red.png')
            . '" >&nbsp;'
            . (isset($options[$status]) ? $options[$status] : '');
        }
        return $result;
    }
}
