<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance block
 *
 */
namespace Magento\CustomerBalance\Block\Account;

class Balance extends \Magento\Core\Block\Template
{
    /**
     * Retreive current customers balance in base currency
     *
     * @return float
     */
    public function getBalance()
    {
        $customerId = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId();
        if (!$customerId) {
            return 0;
        }

        $model = \Mage::getModel('Magento\CustomerBalance\Model\Balance')
            ->setCustomerId($customerId)
            ->loadByCustomer();

        return $model->getAmount();
    }
}
