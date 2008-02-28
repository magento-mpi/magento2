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
 * @category   Mage
 * @package    Mage_ProductAlert
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * ProductAlert observer
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author     Victor Tihonchuk <victor@varien.com>
 */
class Mage_productAlert_Model_Observer
{
    /**
     * Website collection array
     *
     * @var array
     */
    protected $_websites;

    /**
     * Retrieve website collection array
     *
     * @return array
     */
    protected function _getWebsites()
    {
        if (is_null($this->_websites)) {
            $this->_websites = Mage::app()->getWebsites();
        }
        return $this->_websites;
    }

    /**
     * Process price emails
     *
     * @param Mage_ProductAlert_Model_Email $email
     * @return Mage_productAlert_Model_Observer
     */
    protected function _processPrice(Mage_ProductAlert_Model_Email $email)
    {
        $email->setType('price');
        foreach ($this->_getWebsites() as $website) {
            $collection = Mage::getModel('productalert/price')
                ->getCollection()
                ->addWebsiteFilter($website->getId())
                ->setCustomerOrder();

            $previousCustomer = null;
            $email->setWebsite($website);
            foreach ($collection as $alert) {
                if (!$previousCustomer || $previousCustomer->getId() != $alert->getCustomerId()) {
                    $customer = Mage::getModel('customer/customer')->load($alert->getCustomerId());
                    if ($previousCustomer) {
                        $email->send();
                    }
                    if (!$customer) {
                        continue;
                    }
                    $previousCustomer = $customer;
                    $email->clean();
                    $email->setCustomer($customer);
                }
                else {
                    $customer = $previousCustomer;
                }

                $product = Mage::getModel('catalog/product')->load($alert->getProductId());
                if (!$product) {
                    continue;
                }
                $product->setCustomerGroupId($customer->getGroupId());
                if ($alert->getPrice() > $product->getFinalPrice()) {
                    $email->addPriceProduct($product);

                    $alert->setPrice($product->getFinalPrice());
                    $alert->setLastSendDate(Mage::getModel('core/date')->gmtDate());
                    $alert->setSendCount($alert->getSendCount() + 1);
                    $alert->save();
                }
            }
            if ($previousCustomer) {
                $email->send();
            }
        }
        return $this;
    }

    /**
     * Process stock emails
     *
     * @param Mage_ProductAlert_Model_Email $email
     * @return Mage_productAlert_Model_Observer
     */
    protected function _processStock(Mage_ProductAlert_Model_Email $email)
    {
        $email->setType('stock');
        foreach ($this->_getWebsites() as $website) {
            $collection = Mage::getModel('productalert/stock')
                ->getCollection()
                ->addWebsiteFilter($website->getId())
                ->setCustomerOrder();

            $previousCustomer = null;
            $email->setWebsite($website);
            foreach ($collection as $alert) {
                if (!$previousCustomer || $previousCustomer->getId() != $alert->getCustomerId()) {
                    $customer = Mage::getModel('customer/customer')->load($alert->getCustomerId());
                    if ($previousCustomer) {
                        $email->send();
                    }
                    if (!$customer) {
                        continue;
                    }
                    $previousCustomer = $customer;
                    $email->clean();
                    $email->setCustomer($customer);
                }
                else {
                    $customer = $previousCustomer;
                }

                $product = Mage::getModel('catalog/product')->load($alert->getProductId());
                if (!$product) {
                    continue;
                }
                $product->setCustomerGroupId($customer->getGroupId());
                if ($product->isSaleable()) {
                    $email->addStockProduct($product);

                    $alert->setSendDate(Mage::getModel('core/date')->gmtDate());
                    $alert->setSendCount($alert->getSendCount() + 1);
                    $alert->setStatus(1);
                    $alert->save();
                }
            }
            if ($previousCustomer) {
                $email->send();
            }
        }
        return $this;
    }

    public function process(Varien_Event_Observer $observer)
    {
        $email = Mage::getModel('productalert/email');
        /* @var $email Mage_ProductAlert_Model_Email */

        $this->_processPrice($email);
        $this->_processStock($email);
    }
}