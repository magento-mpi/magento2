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
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory
     */
    protected $_giftCAFactory = null;

    /**
     * @var \Magento\Core\Model\Date
     */
    protected $_coreDate = null;

    /**
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCAFactory
     * @param \Magento\Core\Model\Date $coreDate
     */
    public function __construct(
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCAFactory,
        \Magento\Core\Model\Date $coreDate
    ) {
        $this->_giftCAFactory = $giftCAFactory;
        $this->_coreDate = $coreDate;
    }

    /**
     * Update Gift Card Account states by cron
     *
     * @return \Magento\GiftCardAccount\Model\Cron
     */
    public function updateStates()
    {
        // update to expired
        $model = $this->_giftCAFactory->create();

        $now = $this->_coreDate->date('Y-m-d');

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
