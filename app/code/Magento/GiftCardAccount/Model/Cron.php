<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Model_Cron
{
    /**
     * Update Gift Card Account states by cron
     *
     * @return Magento_GiftCardAccount_Model_Cron
     */
    public function updateStates()
    {
        // update to expired
        $model = Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount');

        $now = Mage::getModel('Magento_Core_Model_Date')->date('Y-m-d');

        $collection = $model->getCollection()
            ->addFieldToFilter('state', Magento_GiftCardAccount_Model_Giftcardaccount::STATE_AVAILABLE)
            ->addFieldToFilter('date_expires', array('notnull'=>true))
            ->addFieldToFilter('date_expires', array('lt'=>$now));

        $ids = $collection->getAllIds();
        if ($ids) {
            $state = Magento_GiftCardAccount_Model_Giftcardaccount::STATE_EXPIRED;
            $model->updateState($ids, $state);
        }
        return $this;
    }
}
