<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Model_Cron
{
    /**
     * Update Gift Card Account states by cron
     *
     * @return Enterprise_GiftCardAccount_Model_Cron
     */
    public function updateStates()
    {
        // update to expired
        $model = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount');

        $now = Mage::getModel('Magento_Core_Model_Date')->date('Y-m-d');

        $collection = $model->getCollection()
            ->addFieldToFilter('state', Enterprise_GiftCardAccount_Model_Giftcardaccount::STATE_AVAILABLE)
            ->addFieldToFilter('date_expires', array('notnull'=>true))
            ->addFieldToFilter('date_expires', array('lt'=>$now));

        $ids = $collection->getAllIds();
        if ($ids) {
            $state = Enterprise_GiftCardAccount_Model_Giftcardaccount::STATE_EXPIRED;
            $model->updateState($ids, $state);
        }
        return $this;
    }
}
