<?php
/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminNotification_Block_Grid_Renderer_Actions
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        $readDetailsHtml = ($row->getUrl())
            ? '<a target="_blank" href="'. $row->getUrl() .'">' .
                __('Read Details') .'</a> | '
            : '';

        $markAsReadHtml = (!$row->getIsRead())
            ? '<a href="'. $this->getUrl('*/*/markAsRead/', array('_current' => true, 'id' => $row->getId())) .'">' .
                __('Mark as Read') .'</a> | '
            : '';

        $encodedUrl = $this->helper('Magento_Core_Helper_Url')->getEncodedUrl();
        return sprintf('%s%s<a href="%s" onClick="deleteConfirm(\'%s\', this.href); return false;">%s</a>',
            $readDetailsHtml,
            $markAsReadHtml,
            $this->getUrl('*/*/remove/', array(
                '_current'=>true,
                'id' => $row->getId(),
                Magento_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $encodedUrl)
            ),
            __('Are you sure?'),
            __('Remove')
        );
    }
}
