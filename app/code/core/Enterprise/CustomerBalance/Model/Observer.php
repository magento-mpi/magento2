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
 * @category   Enterprise
 * @package    Enterprise_CustomerBalance
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Customer balance observer
 *
 */
class Enterprise_CustomerBalance_Model_Observer
{
    /**
     * Prepare customer balance POST data
     *
     * @param Varien_Event_Observer $observer
     */
    public function prepareCustomerBalanceSave($observer)
    {
        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            return;
        }
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getCustomer();
        /* @var $request Mage_Core_Controller_Request_Http */
        $request = $observer->getRequest();
        if ($data = $request->getPost('customerbalance')) {
            $customer->setCustomerBalanceData($data);
        }
    }

    /**
     * Customer balance update after save
     *
     * @param Varien_Event_Observer $observer
     */
    public function customerSaveAfter($observer)
    {
        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            return;
        }
        if ($data = $observer->getCustomer()->getCustomerBalanceData()) {
            if (!empty($data['amount_delta'])) {
                $balance = Mage::getModel('enterprise_customerbalance/balance')
                    ->setCustomer($observer->getCustomer())
                    ->setWebsiteId(isset($data['website_id']) ? $data['website_id'] : $observer->getCustomer()->getWebsiteId())
                    ->setAmountDelta($data['amount_delta'])
                ;
                if (isset($data['notify_by_email']) && isset($data['store_id'])) {
                    $balance->setNotifyByEmail(true, $data['store_id']);
                }
                $balance->save();
            }
        }
    }

    /**
     * Check for customer balance use switch & update payment info
     *
     * @param Varien_Event_Observer $observer
     */
    public function paymentDataImport(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            return;
        }
        $input = $observer->getEvent()->getInput();
        $payment = $observer->getEvent()->getPayment();
        $quote = $payment->getQuote();

        if (!$quote->getCustomerId()) {
            return;
        }

        $balance = Mage::getModel('enterprise_customerbalance/balance')
            ->setCustomerId($quote->getCustomerId())
            ->setWebsiteId($quote->getWebsiteId())
            ->loadByCustomer()
            ->getAmount();

        $total = $quote->getBaseGrandTotal()+$quote->getBaseCustomerBalanceAmountUsed();

        $quote->setUseCustomerBalance($input->getUseCustomerBalance());
        if ($input->getUseCustomerBalance() && $balance >= $total) {
            $input->setMethod('free');
        }
    }

    /**
     * Check if customer balance was used in quote and reduce balance if so
     *
     * @param Varien_Event_Observer $observer
     */
    public function processOrderPlace(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            return;
        }
        $order = $observer->getEvent()->getOrder();
        if ($order->getBaseCustomerBalanceAmount() > 0) {
            $balance = Mage::getModel('enterprise_customerbalance/balance')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId($order->getWebsiteId())
                ->setAmountDelta(-$order->getBaseCustomerBalanceAmount())
                ->setHistoryAction(Enterprise_CustomerBalance_Model_Balance_History::ACTION_USED)
                ->setOrder($order)
                ->save();
        }
    }

    /**
     * Disable entire customerbalance layout
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableLayout($observer)
    {
        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            unset($observer->getUpdates()->enterprise_customerbalance);
        }
    }
}
