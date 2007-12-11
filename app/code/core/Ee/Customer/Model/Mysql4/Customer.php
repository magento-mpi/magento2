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
 * @category   Ee
 * @package    Ee_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Ee customer resource model
 *
 * @category   Ee
 * @package    Ee_Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */

class Ee_Customer_Model_Mysql4_Customer extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('customer/members', 'member_id');
        $this->_setResource('ee_customer');
    }
    
    public function getEntityIdField()
    {
        return $this->getIdFieldName();
    }

    public function loadByEmail(Mage_Customer_Model_Customer $customer, $email, $testOnly=false)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getMainTable())
            ->where($read->quoteInto('email = ?', $email));

        $customer->setData($read->fetchRow($select));
        return $this;
    }

    public function loadByUsername(Mage_Customer_Model_Customer $customer, $username, $testOnly=false)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getMainTable())
            ->where($read->quoteInto('username = ?', $username));

        $customer->setData($read->fetchRow($select));
        return $this;
    }

    public function authenticate(Mage_Customer_Model_Customer $customer, $login, $password)
    {
        $this->loadByUsername($customer, $login);
        echo "<pre>DEBUG:\n";
        print_r($customer);
        echo "</pre>";
        die();
    }

    public function changePassword(Mage_Customer_Model_Customer $customer, $newPassword, $checkCurrent=true)
    {
        $customer->setPassword($newPassword);
        # FIXME;
        return $this;
    }

/*    public function __call($m, $a)
    {
        print "Method $m called:\n";
        var_dump($a);
    }*/
}