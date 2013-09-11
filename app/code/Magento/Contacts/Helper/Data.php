<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Contacts
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Contacts base helper
 *
 * @category   Magento
 * @package    Magento_Contacts
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Contacts\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{

    const XML_PATH_ENABLED   = 'contacts/contacts/enabled';

    public function isEnabled()
    {
        return \Mage::getStoreConfig( self::XML_PATH_ENABLED );
    }

    public function getUserName()
    {
        if (!\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            return '';
        }
        $customer = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer();
        return trim($customer->getName());
    }

    public function getUserEmail()
    {
        if (!\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            return '';
        }
        $customer = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer();
        return $customer->getEmail();
    }
}
