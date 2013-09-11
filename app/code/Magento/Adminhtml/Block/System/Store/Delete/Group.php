<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml store delete group block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\System\Store\Delete;

class Group extends \Magento\Adminhtml\Block\Template
{
    protected function _prepareLayout()
    {
        $itemId = $this->getRequest()->getParam('group_id');

        $this->setTemplate('system/store/delete_group.phtml');
        $this->setAction($this->getUrl('*/*/deleteGroupPost', array('group_id'=>$itemId)));
        $this->addChild('confirm_deletion_button', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Delete Store'),
            'onclick'   => "deleteForm.submit()",
            'class'     => 'cancel'
        ));
        $onClick = "setLocation('".$this->getUrl('*/*/editGroup', array('group_id'=>$itemId))."')";
        $this->addChild('cancel_button', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Cancel'),
            'onclick'   => $onClick,
            'class'     => 'cancel'
        ));
        $this->addChild('back_button', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Back'),
            'onclick'   => $onClick,
            'class'     => 'cancel'
        ));
        return parent::_prepareLayout();
    }
}
