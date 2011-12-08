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
    public function beforeLoadLayout($observer)
    {
        $loggedIn = Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn();

        $observer->getEvent()->getLayout()->getUpdate()
           ->addHandle('customer_logged_'.($loggedIn?'in':'out'));
    }
}
