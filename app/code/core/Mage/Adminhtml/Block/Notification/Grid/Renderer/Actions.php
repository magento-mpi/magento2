<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Notification_Grid_Renderer_Actions
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $readDetailsHtml = ($row->getUrl())
            ? '<a target="_blank" href="'. $row->getUrl() .'">' .
                Mage::helper('Mage_AdminNotification_Helper_Data')->__('Read Details') .'</a> | '
            : '';

        $markAsReadHtml = (!$row->getIsRead())
            ? '<a href="'. $this->getUrl('*/*/markAsRead/', array('_current' => true, 'id' => $row->getId())) .'">' .
                Mage::helper('Mage_AdminNotification_Helper_Data')->__('Mark as Read') .'</a> | '
            : '';

        $encodedUrl = $this->helper('Mage_Core_Helper_Url')->getEncodedUrl();
        return sprintf('%s%s<a href="%s" onClick="deleteConfirm(\'%s\', this.href); return false;">%s</a>',
            $readDetailsHtml,
            $markAsReadHtml,
            $this->getUrl('*/*/remove/', array(
                '_current'=>true,
                'id' => $row->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $encodedUrl)
            ),
            Mage::helper('Mage_AdminNotification_Helper_Data')->__('Are you sure?'),
            Mage::helper('Mage_AdminNotification_Helper_Data')->__('Remove')
        );
    }
}
