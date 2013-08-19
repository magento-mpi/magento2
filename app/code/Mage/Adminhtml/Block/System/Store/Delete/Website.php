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
 * Adminhtml store delete group block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Store_Delete_Website extends Mage_Adminhtml_Block_Template
{

    protected function _prepareLayout()
    {
        $itemId = $this->getRequest()->getParam('website_id');

        $this->setTemplate('system/store/delete_website.phtml');
        $this->setAction($this->getUrl('*/*/deleteWebsitePost', array('website_id'=>$itemId)));
        $this->addChild('confirm_deletion_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Delete Web Site'),
            'onclick'   => "deleteForm.submit()",
            'class'     => 'cancel'
        ));
        $onClick = "setLocation('".$this->getUrl('*/*/editWebsite', array('website_id'=>$itemId))."')";
        $this->addChild('cancel_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Cancel'),
            'onclick'   => $onClick,
            'class'     => 'cancel'
        ));
        $this->addChild('back_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Back'),
            'onclick'   => $onClick,
            'class'     => 'cancel'
        ));
        return parent::_prepareLayout();
    }

}
