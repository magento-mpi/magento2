<?php
/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_AdminNotification_Block_Grid_Renderer_Actions
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
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
                __('Read Details') .'</a> | '
            : '';

        $markAsReadHtml = (!$row->getIsRead())
            ? '<a href="'. $this->getUrl('*/*/markAsRead/', array('_current' => true, 'id' => $row->getId())) .'">' .
                __('Mark as Read') .'</a> | '
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
            __('Are you sure?'),
            __('Remove')
        );
    }
}
