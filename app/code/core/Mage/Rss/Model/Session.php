<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Auth session model
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('rss');
    }

    public function isAdminLoggedIn()
    {
        return $this->getAdmin() && $this->getAdmin()->getId();
    }

    public function isCustomerLoggedIn()
    {
        return $this->getCustomer() && $this->getCustomer()->getId();
    }
}
