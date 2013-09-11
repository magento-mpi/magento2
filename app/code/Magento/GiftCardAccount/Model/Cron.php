<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Model;

class Cron
{
    /**
     * Update Gift Card Account states by cron
     *
     * @return \Magento\GiftCardAccount\Model\Cron
     */
    public function updateStates()
    {
        // update to expired
        $model = \Mage::getModel('Magento\GiftCardAccount\Model\Giftcardaccount');

        $now = \Mage::getModel('Magento\Core\Model\Date')->date('Y-m-d');

        $collection = $model->getCollection()
            ->addFieldToFilter('state', \Magento\GiftCardAccount\Model\Giftcardaccount::STATE_AVAILABLE)
            ->addFieldToFilter('date_expires', array('notnull'=>true))
            ->addFieldToFilter('date_expires', array('lt'=>$now));

        $ids = $collection->getAllIds();
        if ($ids) {
            $state = \Magento\GiftCardAccount\Model\Giftcardaccount::STATE_EXPIRED;
            $model->updateState($ids, $state);
        }
        return $this;
    }
}
