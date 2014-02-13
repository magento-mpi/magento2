<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\System\Store\Delete;

/**
 * Adminhtml store delete group block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Website extends \Magento\Backend\Block\Template
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $itemId = $this->getRequest()->getParam('website_id');

        $this->setTemplate('system/store/delete_website.phtml');
        $this->setAction($this->getUrl('adminhtml/*/deleteWebsitePost', array('website_id'=>$itemId)));
        $this->addChild('confirm_deletion_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Delete Web Site'),
            'onclick'   => "deleteForm.submit()",
            'class'     => 'cancel'
        ));
        $onClick = "setLocation('".$this->getUrl('adminhtml/*/editWebsite', array('website_id'=>$itemId))."')";
        $this->addChild('cancel_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Cancel'),
            'onclick'   => $onClick,
            'class'     => 'cancel'
        ));
        $this->addChild('back_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Back'),
            'onclick'   => $onClick,
            'class'     => 'cancel'
        ));
        return parent::_prepareLayout();
    }

}
