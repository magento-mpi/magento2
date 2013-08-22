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
 * Recurring profiles view/management controller
 *
 * TODO: implement ACL restrictions
 */
class Magento_Adminhtml_Controller_Sales_Recurring_Profile extends Magento_Adminhtml_Controller_Action
{
    /**
     * Recurring profiles list
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title(__('Recurring Billing Profiles'))
            ->loadLayout()
            ->_setActiveMenu('Magento_Sales::sales_recurring_profile')
            ->renderLayout();
        return $this;
    }

    /**
     * View recurring profile detales
     */
    public function viewAction()
    {
        try {
            $this->_title(__('Recurring Billing Profiles'));
            $profile = $this->_initProfile();
            $this->loadLayout()
                ->_setActiveMenu('Magento_Sales::sales_recurring_profile')
                ->_title(__('Profile #%1', $profile->getReferenceId()))
                ->renderLayout()
            ;
            return;
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Profiles ajax grid
     */
    public function gridAction()
    {
        try {
            $this->loadLayout()->renderLayout();
            return;
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Profile orders ajax grid
     */
    public function ordersAction()
    {
        try {
            $this->_initProfile();
            $this->loadLayout()->renderLayout();
        } catch (Exception $e) {
            Mage::logException($e);
            $this->norouteAction();
        }
    }

    /**
     * Profile state updater action
     */
    public function updateStateAction()
    {
        $profile = null;
        try {
            $profile = $this->_initProfile();

            switch ($this->getRequest()->getParam('action')) {
                case 'cancel':
                    $profile->cancel();
                    break;
                case 'suspend':
                    $profile->suspend();
                    break;
                case 'activate':
                    $profile->activate();
                    break;
            }
            $this->_getSession()->addSuccess(__('The profile state has been updated.'));
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError(__('We could not update the profile.'));
            Mage::logException($e);
        }
        if ($profile) {
            $this->_redirect('*/*/view', array('profile' => $profile->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Profile information updater action
     */
    public function updateProfileAction()
    {
        $profile = null;
        try {
            $profile = $this->_initProfile();
            $profile->fetchUpdate();
            if ($profile->hasDataChanges()) {
                $profile->save();
                $this->_getSession()->addSuccess(__('You updated the profile.'));
            } else {
                $this->_getSession()->addNotice(__('The profile has no changes.'));
            }
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError(__('We could not update the profile.'));
            Mage::logException($e);
        }
        if ($profile) {
            $this->_redirect('*/*/view', array('profile' => $profile->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Cutomer billing agreements ajax action
     *
     */
    public function customerGridAction()
    {
        $this->_initCustomer();
        $this->loadLayout(false)
            ->renderLayout();
    }

    /**
     * Initialize customer by ID specified in request
     *
     * @return Magento_Adminhtml_Controller_Sales_Billing_Agreement
     */
    protected function _initCustomer()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        $customer = Mage::getModel('Magento_Customer_Model_Customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }

    /**
     * Load/set profile
     *
     * @return Magento_Sales_Model_Recurring_Profile
     */
    protected function _initProfile()
    {
        $profile = Mage::getModel('Magento_Sales_Model_Recurring_Profile')->load($this->getRequest()->getParam('profile'));
        if (!$profile->getId()) {
            Mage::throwException(__('The profile you specified does not exist.'));
        }
        Mage::register('current_recurring_profile', $profile);
        return $profile;
    }
}
