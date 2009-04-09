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
 * @package    Enterprise_CustomerBalance
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_CustomerBalance_Block_Checkout_Onepage_Payment_Additional extends Mage_Core_Block_Template
{
    protected $_balanceModel = null;

    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    protected function _getBalanceModel()
    {
        if (is_null($this->_balanceModel)) {
            $this->_balanceModel = Mage::getModel('enterprise_customerbalance/balance')
                ->setCustomerId($this->_getCustomer()->getId())
                ->setWebsiteId(Mage::app()->getWebsite()->getId())
                ->loadByCustomer();
        }
        return $this->_balanceModel;
    }

    protected function _getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function isAllowed()
    {
        if (!$this->_getCustomer()->getId()) {
            return false;
        }

        if (!$this->getBalance()) {
            return false;
        }

        if (!$this->getAmountToCharge()) {
            return false;
        }

        return true;
    }

    public function getBalance()
    {
        if (!$this->_getCustomer()->getId()) {
            return 0;
        }
        return $this->_getBalanceModel()->getAmount();
    }

    public function getAmountToCharge()
    {
        if ($this->isCustomerBalanceUsed()) {
            return $this->_getQuote()->getCustomerBalanceAmountUsed();
        }

        return min($this->getBalance(), $this->_getQuote()->getBaseGrandTotal());
    }

    public function isCustomerBalanceUsed() {
        return $this->_getQuote()->getUseCustomerBalance();
    }

    public function isFullyPaidAfterApplication() {
        $total = $this->_getQuote()->getBaseGrandTotal() + $this->_getQuote()->getBaseCustomerBalanceAmountUsed();

        return ($this->getBalance() >= $total);
    }
}