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
     * @var Magento_GiftCardAccount_Model_GiftcardaccountFactory
     */
    protected $_giftCAFactory = null;

    /**
     * @var Magento_Core_Model_Date
     */
    protected $_coreDate = null;

    /**
     * @param Magento_GiftCardAccount_Model_GiftcardaccountFactory $giftCAFactory
     * @param Magento_Core_Model_Date $coreDate
     */
    public function __construct(
        Magento_GiftCardAccount_Model_GiftcardaccountFactory $giftCAFactory,
        Magento_Core_Model_Date $coreDate
    ) {
        $this->_giftCAFactory = $giftCAFactory;
        $this->_coreDate = $coreDate;
    }

    /**
     * Update Gift Card Account states by cron
     *
     * @return Magento_GiftCardAccount_Model_Cron
     */
    public function updateStates()
    {
        // update to expired
        $model = $this->_giftCAFactory->create();

        $now = $this->_coreDate->date('Y-m-d');

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
