<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dashboard neswletter info
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Customer\Block\Account\Dashboard;

class Newsletter extends \Magento\Core\Block\Template
{
    public function getSubscriptionObject()
    {
        if(is_null($this->_subscription)) {
            $this->_subscription = \Mage::getModel('Magento\Newsletter\Model\Subscriber')
                ->loadByCustomer(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer());
        }
        return $this->_subscription;
    }
}
