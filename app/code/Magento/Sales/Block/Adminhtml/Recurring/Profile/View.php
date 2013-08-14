<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile view page
 */
class Magento_Sales_Block_Adminhtml_Recurring_Profile_View extends Magento_Adminhtml_Block_Widget_Container
{
    /**
     * Create buttons
     * TODO: implement ACL restrictions
     * @return Magento_Sales_Block_Adminhtml_Recurring_Profile_View
     */
    protected function _prepareLayout()
    {
        $this->_addButton('back', array(
            'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Back'),
            'onclick'   => "setLocation('{$this->getUrl('*/*/')}')",
            'class'     => 'back',
        ));

        $profile = Mage::registry('current_recurring_profile');
        $comfirmationMessage = Mage::helper('Magento_Sales_Helper_Data')->__('Are you sure you want to do this?');

        // cancel
        if ($profile->canCancel()) {
            $url = $this->getUrl('*/*/updateState', array('profile' => $profile->getId(), 'action' => 'cancel'));
            $this->_addButton('cancel', array(
                'label'     => Mage::helper('Magento_Sales_Helper_Data')->__('Cancel'),
                'onclick'   => "confirmSetLocation('{$comfirmationMessage}', '{$url}')",
                'class'     => 'delete',
            ));
        }

        // suspend
        if ($profile->canSuspend()) {
            $url = $this->getUrl('*/*/updateState', array('profile' => $profile->getId(), 'action' => 'suspend'));
            $this->_addButton('suspend', array(
                'label'     => Mage::helper('Magento_Sales_Helper_Data')->__('Suspend'),
                'onclick'   => "confirmSetLocation('{$comfirmationMessage}', '{$url}')",
                'class'     => 'delete',
            ));
        }

        // activate
        if ($profile->canActivate()) {
            $url = $this->getUrl('*/*/updateState', array('profile' => $profile->getId(), 'action' => 'activate'));
            $this->_addButton('activate', array(
                'label'     => Mage::helper('Magento_Sales_Helper_Data')->__('Activate'),
                'onclick'   => "confirmSetLocation('{$comfirmationMessage}', '{$url}')",
                'class'     => 'add',
            ));
        }

        // get update
        if ($profile->canFetchUpdate()) {
            $url = $this->getUrl('*/*/updateProfile', array('profile' => $profile->getId(),));
            $this->_addButton('update', array(
                'label'     => Mage::helper('Magento_Sales_Helper_Data')->__('Get Update'),
                'onclick'   => "confirmSetLocation('{$comfirmationMessage}', '{$url}')",
                'class'     => 'add',
            ));
        }

        return parent::_prepareLayout();
    }

    /**
     * Set title and a hack for tabs container
     *
     * @return Magento_Sales_Block_Adminhtml_Recurring_Profile_View
     */
    protected function _beforeToHtml()
    {
        $profile = Mage::registry('current_recurring_profile');
        $this->_headerText = Mage::helper('Magento_Sales_Helper_Data')->__('Recurring Profile # %s', $profile->getReferenceId());
        $this->setViewHtml('<div id="' . $this->getDestElementId() . '"></div>');
        return parent::_beforeToHtml();
    }
}
