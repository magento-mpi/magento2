<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Pbridge observer
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_Observer
{
    /**
     * Add HTTP header to response that allows browsers accept third-party cookies
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Pbridge_Model_Observer
     */
    public function addPrivacyHeader(Varien_Event_Observer $observer)
    {
        /* @var $controllerAction Mage_Core_Controller_Varien_Action */
        $controllerAction = $observer->getEvent()->getData('controller_action');
        $controllerAction->getResponse()->setHeader("P3P", 'CP="CAO PSA OUR"', true);
        return $this;
    }

    /**
     * Check payment methods availability
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Pbridge_Model_Observer
     */
    public function isPaymentMethodAvailable(Varien_Event_Observer $observer)
    {
        $method = $observer->getEvent()->getData('method_instance');
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getData('quote');
        $result = $observer->getEvent()->getData('result');
        $storeId = $quote ? $quote->getStoreId() : null;

        if (((bool)$this->_getMethodConfigData('using_pbridge', $method, $storeId) === true)
            && ((bool)$method->getIsDummy() === false)) {
            $result->isAvailable = false;
        }
        return $this;
    }

    /**
     * Update Payment Profiles functionality switcher
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Pbridge_Model_Observer
     */
    public function updatePaymentProfileStatus(Varien_Event_Observer $observer)
    {
        $groups = $observer->getEvent()->getData('object')->getGroups();

        $profileStatus = null;
        $braintreeEnabled = isset($groups['braintree_basic']['fields']['active']['value'])
            && $groups['braintree_basic']['fields']['active']['value']
            && isset($groups['braintree_basic']['fields']['payment_profiles_enabled']['value'])
            && $groups['braintree_basic']['fields']['payment_profiles_enabled']['value'];
        $authorizenetEnabled = isset($groups['authorizenet']['fields']['active']['value'])
            && $groups['authorizenet']['fields']['active']['value']
            && isset($groups['authorizenet']['fields']['payment_profiles_enabled']['value'])
            && $groups['authorizenet']['fields']['payment_profiles_enabled']['value'];

        if ($braintreeEnabled || $authorizenetEnabled) {
            $profileStatus = 1;
        } elseif (isset($groups['braintree_basic']['fields']) || isset($groups['authorizenet']['fields'])) {
            $profileStatus = 0;
        }
        if ($profileStatus !== null) {
            Mage::getConfig()->saveConfig('payment/pbridge/profilestatus', $profileStatus);
        }
        return $this;
    }

    /**
     * Return system config value by key for specified payment method
     *
     * @param string $key
     * @param Mage_Payment_Model_Method_Abstract $method
     * @param int $storeId
     *
     * @return string
     */
    protected function _getMethodConfigData($key, Mage_Payment_Model_Method_Abstract $method, $storeId = null)
    {
        if (!$method->getCode()) {
            return null;
        }
        return Mage::getStoreConfig("payment/{$method->getCode()}/$key", $storeId);
    }
}
