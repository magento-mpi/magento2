<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer module observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Observer
{
    /**
     * VAT ID validation processed flag code
     */
    const VIV_PROCESSED_FLAG = 'viv_after_address_save_processed';

    /**
     * VAT ID validation currently saved address flag
     */
    const VIV_CURRENTLY_SAVED_ADDRESS = 'currently_saved_address';

    /**
     * Check whether specified billing address is default for its customer
     *
     * @param Mage_Customer_Model_Address $address
     * @return bool
     */
    protected function _isDefaultBilling($address)
    {
        return $address->getId() && $address->getId() == $address->getCustomer()->getDefaultBilling();
    }

    /**
     * Check whether specified address should be processed in after_save event handler
     *
     * @param Mage_Customer_Model_Address $address
     * @return bool
     */
    protected function _canProcessAddress($address)
    {
        if ($address->getForceProcess()) {
            return true;
        }

        if (Mage::registry(self::VIV_CURRENTLY_SAVED_ADDRESS) != $address->getId()) {
            return false;
        }

        return $this->_isDefaultBilling($address);
    }

    /**
     * Before load layout event handler
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeLoadLayout($observer)
    {
        $loggedIn = Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn();

        $observer->getEvent()->getLayout()->getUpdate()
           ->addHandle('customer_logged_' . ($loggedIn ? 'in' : 'out'));
    }

    /**
     * Address before save event handler
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeAddressSave($observer)
    {
        if (Mage::registry(self::VIV_CURRENTLY_SAVED_ADDRESS)) {
            Mage::unregister(self::VIV_CURRENTLY_SAVED_ADDRESS);
        }

        /** @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = $observer->getCustomerAddress();
        if ($customerAddress->getId()) {
            Mage::register(self::VIV_CURRENTLY_SAVED_ADDRESS, $customerAddress->getId());
        } elseif ($customerAddress->getIsDefaultBilling()) {
            $customerAddress->setForceProcess(true);
        } else {
            Mage::register(self::VIV_CURRENTLY_SAVED_ADDRESS, 'new_address');
        }
    }

    /**
     * Address after save event handler
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterAddressSave($observer)
    {
        /** @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = $observer->getCustomerAddress();
        $customer = $customerAddress->getCustomer();

        if (!Mage::helper('Mage_Customer_Helper_Address')->isVatValidationEnabled($customer->getStore())
            || Mage::registry(self::VIV_PROCESSED_FLAG)
            || !$this->_canProcessAddress($customerAddress)
        ) {
            return;
        }

        try {
            Mage::register(self::VIV_PROCESSED_FLAG, true);

            /** @var $customerHelper Mage_Customer_Helper_Data */
            $customerHelper = Mage::helper('Mage_Customer_Helper_Data');

            if ($customerAddress->getVatId() == ''
                || !Mage::helper('Mage_Core_Helper_Data')->isCountryInEU($customerAddress->getCountry()))
            {
                $defaultGroupId = $customerHelper->getDefaultCustomerGroupId($customer->getStore());

                if (!$customer->getDisableAutoGroupChange() && $customer->getGroupId() != $defaultGroupId) {
                    $customer->setGroupId($defaultGroupId);
                    $customer->save();
                }
            } else {

                $result = $customerHelper->checkVatNumber(
                    $customerAddress->getCountryId(),
                    $customerAddress->getVatId()
                );

                $newGroupId = $customerHelper->getCustomerGroupIdBasedOnVatNumber(
                    $customerAddress->getCountryId(), $result, $customer->getStore()
                );

                if (!$customer->getDisableAutoGroupChange() && $customer->getGroupId() != $newGroupId) {
                    $customer->setGroupId($newGroupId);
                    $customer->save();
                }

                if (!Mage::app()->getStore()->isAdmin()) {
                    $validationMessage = Mage::helper('Mage_Customer_Helper_Data')->getVatValidationUserMessage($customerAddress,
                        $customer->getDisableAutoGroupChange(), $result);

                    if (!$validationMessage->getIsError()) {
                        Mage::getSingleton('Mage_Customer_Model_Session')->addSuccess($validationMessage->getMessage());
                    } else {
                        Mage::getSingleton('Mage_Customer_Model_Session')->addError($validationMessage->getMessage());
                    }
                }
            }
        } catch (Exception $e) {
            Mage::register(self::VIV_PROCESSED_FLAG, false, true);
        }
    }

    /**
     * Assign custom renderer for VAT ID field in billing address form
     *
     * @param Varien_Event_Observer $observer
     */
    public function prepareFormAfter($observer)
    {
        /** @var $formBlock Mage_Adminhtml_Block_Sales_Order_Create_Billing_Address */
        $formBlock = $observer->getForm();
        $formBlock->getForm()->getElement('vat_id')->setRenderer(
            $formBlock->getLayout()->createBlock('Mage_Adminhtml_Block_Customer_Sales_Order_Address_Form_Billing_Renderer_Vat')
        );
    }

    /**
     * Revert emulated customer group_id
     *
     * @param Varien_Event_Observer $observer
     */
    public function quoteSubmitAfter($observer)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getQuote()->getCustomer();

        if (!Mage::helper('Mage_Customer_Helper_Address')->isVatValidationEnabled($customer->getStore())) {
            return;
        }

        if (!$customer->getId()) {
            return;
        }

        $customer->setGroupId(
            $customer->getOrigData('group_id')
        );
        $customer->save();
    }
}
