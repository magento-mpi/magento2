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

class Enterprise_CustomerBalance_Block_Adminhtml_Sales_Order_Create_Payment
extends Mage_Core_Block_Template
{
    /**
     * Retrieve order create model
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    public function formatPrice($value)
    {
        return Mage::getSingleton('adminhtml/session_quote')->getStore()->formatPrice($value);
    }

    public function getBalance()
    {
        $quote = $this->_getOrderCreateModel()->getQuote();
        $store = Mage::app()->getStore($quote->getStoreId());

        if (!Mage::helper('enterprise_customerbalance')->isEnabled($store)) {
            return false;
        }
        if (!$quote->getCustomerId()) {
            return false;
        }

        $balance = Mage::getModel('enterprise_customerbalance/balance')
            ->setCustomerId($quote->getCustomerId())
            ->setWebsiteId($store->getWebsiteId())
            ->loadByCustomer()
            ->getAmount();

        return $balance;
    }

    public function getUseCustomerBalance()
    {
        return $this->_getOrderCreateModel()->getQuote()->getUseCustomerBalance();
    }

    public function isFullyPaid()
    {
        $quote = $this->_getOrderCreateModel()->getQuote();
        $total = $quote->getBaseGrandTotal()+$quote->getBaseCustomerBalanceAmountUsed();
        if ($this->getBalance() >= $total && $quote->getUseCustomerBalance()) {
            return true;
        }
        return false;
    }
}
