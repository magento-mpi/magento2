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
class Magento_CustomerBalance_Block_Account_Balance extends Magento_Core_Block_Template
{
    /**
     * Retreive current customers balance in base currency
     *
     * @return float
     */
    public function getBalance()
    {
        $customerId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId();
        if (!$customerId) {
            return 0;
        }

        $model = Mage::getModel('Magento_CustomerBalance_Model_Balance')
            ->setCustomerId($customerId)
            ->loadByCustomer();

        return $model->getAmount();
    }
}
