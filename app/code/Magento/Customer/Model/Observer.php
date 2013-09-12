<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer module observer
 */
class Magento_Customer_Model_Observer
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
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Check whether specified billing address is default for its customer
     *
     * @param Magento_Customer_Model_Address $address
     * @return bool
     */
    protected function _isDefaultBilling($address)
    {
        return ($address->getId() && $address->getId() == $address->getCustomer()->getDefaultBilling())
            || $address->getIsPrimaryBilling() || $address->getIsDefaultBilling();
    }

    /**
     * Check whether specified shipping address is default for its customer
     *
     * @param Magento_Customer_Model_Address $address
     * @return bool
     */
    protected function _isDefaultShipping($address)
    {
        return ($address->getId() && $address->getId() == $address->getCustomer()->getDefaultShipping())
            || $address->getIsPrimaryShipping() || $address->getIsDefaultShipping();
    }

    /**
     * Check whether specified address should be processed in after_save event handler
     *
     * @param Magento_Customer_Model_Address $address
     * @return bool
     */
    protected function _canProcessAddress($address)
    {
        if ($address->getForceProcess()) {
            return true;
        }

        if ($this->_coreRegistry->registry(self::VIV_CURRENTLY_SAVED_ADDRESS) != $address->getId()) {
            return false;
        }

        $configAddressType = Mage::helper('Magento_Customer_Helper_Address')->getTaxCalculationAddressType();
        if ($configAddressType == Magento_Customer_Model_Address_Abstract::TYPE_SHIPPING) {
            return $this->_isDefaultShipping($address);
        }
        return $this->_isDefaultBilling($address);
    }

    /**
     * Address before save event handler
     *
     * @param Magento_Event_Observer $observer
     */
    public function beforeAddressSave($observer)
    {
        if ($this->_coreRegistry->registry(self::VIV_CURRENTLY_SAVED_ADDRESS)) {
            $this->_coreRegistry->unregister(self::VIV_CURRENTLY_SAVED_ADDRESS);
        }

        /** @var $customerAddress Magento_Customer_Model_Address */
        $customerAddress = $observer->getCustomerAddress();
        if ($customerAddress->getId()) {
            $this->_coreRegistry->register(self::VIV_CURRENTLY_SAVED_ADDRESS, $customerAddress->getId());
        } else {
            $configAddressType = Mage::helper('Magento_Customer_Helper_Address')->getTaxCalculationAddressType();

            $forceProcess = ($configAddressType == Magento_Customer_Model_Address_Abstract::TYPE_SHIPPING)
                ? $customerAddress->getIsDefaultShipping() : $customerAddress->getIsDefaultBilling();

            if ($forceProcess) {
                $customerAddress->setForceProcess(true);
            } else {
                $this->_coreRegistry->register(self::VIV_CURRENTLY_SAVED_ADDRESS, 'new_address');
            }
        }
    }

    /**
     * Address after save event handler
     *
     * @param Magento_Event_Observer $observer
     */
    public function afterAddressSave($observer)
    {
        /** @var $customerAddress Magento_Customer_Model_Address */
        $customerAddress = $observer->getCustomerAddress();
        $customer = $customerAddress->getCustomer();

        if (!Mage::helper('Magento_Customer_Helper_Address')->isVatValidationEnabled($customer->getStore())
            || $this->_coreRegistry->registry(self::VIV_PROCESSED_FLAG)
            || !$this->_canProcessAddress($customerAddress)
        ) {
            return;
        }

        try {
            $this->_coreRegistry->register(self::VIV_PROCESSED_FLAG, true);

            /** @var $customerHelper Magento_Customer_Helper_Data */
            $customerHelper = Mage::helper('Magento_Customer_Helper_Data');

            if ($customerAddress->getVatId() == ''
                || !Mage::helper('Magento_Core_Helper_Data')->isCountryInEU($customerAddress->getCountry())
            ) {
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
                    $validationMessage = Mage::helper('Magento_Customer_Helper_Data')->getVatValidationUserMessage(
                        $customerAddress,
                        $customer->getDisableAutoGroupChange(),
                        $result
                    );

                    if (!$validationMessage->getIsError()) {
                        Mage::getSingleton('Magento_Customer_Model_Session')->addSuccess($validationMessage->getMessage());
                    } else {
                        Mage::getSingleton('Magento_Customer_Model_Session')->addError($validationMessage->getMessage());
                    }
                }
            }
        } catch (Exception $e) {
            $this->_coreRegistry->register(self::VIV_PROCESSED_FLAG, false, true);
        }
    }

    /**
     * Revert emulated customer group_id
     *
     * @param Magento_Event_Observer $observer
     */
    public function quoteSubmitAfter($observer)
    {
        /** @var $customer Magento_Customer_Model_Customer */
        $customer = $observer->getQuote()->getCustomer();

        if (!Mage::helper('Magento_Customer_Helper_Address')->isVatValidationEnabled($customer->getStore())) {
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
