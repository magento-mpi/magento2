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
     * Core url
     *
     * @var Magento_Core_Helper_Url
     */
    protected $_coreUrl = null;

    /**
     * @param Magento_Core_Helper_Url $coreUrl
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Url $coreUrl,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Context $context,
        array $data = array()
    ) {
        $this->_coreUrl = $coreUrl;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        $readDetailsHtml = ($row->getUrl())
            ? '<a target="_blank" href="'. $row->getUrl() .'">' .
                __('Read Details') .'</a> | '
            : '';

        $markAsReadHtml = (!$row->getIsRead())
            ? '<a href="'. $this->getUrl('*/*/markAsRead/', array('_current' => true, 'id' => $row->getId())) .'">' .
                __('Mark as Read') .'</a> | '
            : '';

        $encodedUrl = $this->_coreUrl->getEncodedUrl();
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
