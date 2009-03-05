<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Invitation view general tab block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation_View_Tab_General
    extends Mage_Core_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        $this->setTemplate('enterprise/invitation/view/tab/general.phtml');
    }

    /**
     * Prepare layout
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Invitation_View_Tab_General
     */
    protected function _prepareLayout()
    {
        $this->setChild('save_message_button', $this->getLayout()->createBlock('adminhtml/widget_button', 'save_message_button', array(
            'type' => 'submit',
            'class' => 'save',
            'label' => $this->helper('enterprise_invitation')->__('Save invitation message')
        )));
        return parent::_prepareLayout();
    }

    public function getTabLabel()
    {
        return Mage::helper('enterprise_invitation')->__('General');
    }

    public function getTabTitle()
    {
        return Mage::helper('enterprise_invitation')->__('General');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * Return invitaion for view
     *
     * @return Enterprise_Invitation_Model_Invitation
     */
    public function getInvitation()
    {
        return Mage::registry('current_invitation');
    }

    /**
     * Checks availability to edit invitation message
     *
     * @return boolean
     */
    public function canEditMessage()
    {
        if ($this->getInvitation()->getStatus() == Enterprise_Invitation_Model_Invitation::STATUS_SENT) {
            return true;
        }

        return false;
    }

    /**
     * Return save message button html
     *
     * @return string
     */
    public function getSaveMessageButtonHtml()
    {
        return $this->getChildHtml('save_message_button');
    }

    /**
     * Retrieve formating date
     *
     * @param   string $date
     * @param   string $format
     * @param   bool $showTime
     * @return  string
     */
    public function formatDate($date=null, $format='short', $showTime=false)
    {
        if (is_string($date)) {
            $date = Mage::app()->getLocale()->date($date, Varien_Date::DATETIME_INTERNAL_FORMAT);
        }

        return parent::formatDate($date, $format, $showTime);
    }

    /**
     * Return invintation customer model
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getReferral()
    {
        if (!$this->hasData('referral')) {
            if ($this->getInvitation()->getReferralId()) {
                $referral = Mage::getModel('customer/customer')->load(
                    $this->getInvitation()->getReferralId()
                );
            } else {
                $referral = false;
            }

            $this->setData('referral', $referral);
        }

        return $this->getData('referral');
    }

    /**
     * Return invitation customer model
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->hasData('customer')) {
            if ($this->getInvitation()->getCustomerId()) {
                $customer = Mage::getModel('customer/customer')->load(
                    $this->getInvitation()->getCustomerId()
                );
            } else {
                $customer = false;
            }

            $this->setData('customer', $customer);
        }

        return $this->getData('customer');
    }

    /**
     * Return customer group collection
     *
     * @return Mage_Customer_Model_Entity_Group_Collection
     */
    public function getCustomerGroupCollection()
    {
        if (!$this->hasData('customer_groups_collection')) {
            $groups = Mage::getModel('customer/group')->getCollection()
                ->addFieldToFilter('customer_group_id', array('gt'=> 0))
                ->load();
            $this->setData('customer_groups_collection', $groups);
        }

        return $this->getData('customer_groups_collection');
    }

    /**
     * Return customer group code by group id
     *
     * @return string
     */
    public function getCustomerGroupCode($groupId)
    {
        $group = $this->getCustomerGroupCollection()->getItemById($groupId);
        if ($group) {
            return $group->getCustomerGroupCode();
        } else {
            return $this->helper('enterprise_invitation')->__('N/A');
        }
    }
}