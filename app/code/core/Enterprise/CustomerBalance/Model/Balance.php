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
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_CustomerBalance_Model_Balance extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('enterprise_customerbalance/balance');
    }

    public function updateBalance()
    {
        if( abs($this->getDelta()) > 0 ) {
            $this->loadByCustomerWebsite($this->getCustomerId(), $this->getWebsiteId());
            if( !$this->getId() ) {
                $this->setBalance($this->getDelta())
                     ->save();
                Mage::dispatchEvent('enterprise_customerbalance_create', array('balance' => $this->getData()));
            } else {
                $newBalance = $this->getBalance() + $this->getDelta();
                $this->setBalance($newBalance)
                     ->save();
                Mage::dispatchEvent('enterprise_customerbalance_update', array('balance' => $this->getData()));
            }
        }
    }

    public function loadByCustomerWebsite($customerId, $websiteId)
    {
        $this->getResource()->loadByCustomerWebsite($this, $customerId, $websiteId);
        return $this;
    }

    public function getTotal($customerId)
    {
        return $this->getResource()->getTotal($customerId);
    }
}