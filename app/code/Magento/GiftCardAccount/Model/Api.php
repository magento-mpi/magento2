<?php
/**
 * Gift card account API
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Model;

class Api extends \Magento\Api\Model\Resource\AbstractResource
{
    /**
     * Attributes, allowed for update
     *
     * @var array
     */
    protected $_updateAllowedAttributes = array(
        'is_active', 'is_redeemable', 'store_id', 'date_expires', 'balance'
    );

    /**
     * Attribute name mappings
     *
     * @var array
     */
    protected $_mapAttributes = array(
        'giftcard_id' => 'giftcardaccount_id',
        'is_active'   => 'status',
        'status'      => 'state',
        'store_id'    => 'website_id'
    );

    /**
     * Retrieve gift card accounts list
     *
     * @param object|array $filters
     * @return array
     */
    public function items($filters)
    {
        /** @var $collection \Magento\GiftCardAccount\Model\Resource\Giftcardaccount\Collection */
        $collection = \Mage::getResourceModel('Magento\GiftCardAccount\Model\Resource\Giftcardaccount\Collection');
        /** @var $apiHelper \Magento\Api\Helper\Data */
        $apiHelper = \Mage::helper('Magento\Api\Helper\Data');
        $filters = $apiHelper->parseFilters($filters, $this->_mapAttributes);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $result = array();
        foreach($collection->getItems() as $card){
            $result[] = $this->_getEntityInfo($card);
        }

        return $result;
    }

    /**
     * Retrieve full information
     *
     * @param integer $giftcardAccountId
     * @return array
     */
    public function info($giftcardAccountId)
    {
        $model = $this->_init($giftcardAccountId);

        $result = $this->_getEntityInfo($model);
        $result['is_redeemable'] = $model->getIsRedeemable();
        $result['history']       = array();

        /** @var $historyCollection \Magento\GiftCardAccount\Model\Resource\History\Collection */
        $historyCollection = \Mage::getModel('Magento\GiftCardAccount\Model\History')
            ->getCollection()
            ->addFieldToFilter('giftcardaccount_id', $model->getId());

        foreach ($historyCollection->getItems() as $record) {
            $actions = $record->getActionNamesArray();
            $result['history'][] = array(
                'record_id'     => $record->getId(),
                'date'          => $record->getUpdatedAt(),
                'action'        => $actions[$record->getAction()],
                'balance_delta' => $record->getBalanceDelta(),
                'balance'       => $record->getBalanceAmount(),
                'info'          => $record->getAdditionalInfo()
            );
        }

        return $result;
    }

    /**
     * Create gift card account
     *
     * @param array $giftcardAccountData
     * @param array|null $notificationData
     * @return int
     */
    public function create($giftcardAccountData, $notificationData = null)
    {
        $giftcardAccountData = $this->_prepareCreateGiftcardAccountData($giftcardAccountData);
        $notificationData = $this->_prepareCreateNotificationData($notificationData);
        /** @var $giftcardAccount \Magento\GiftCardAccount\Model\Giftcardaccount */
        $giftcardAccount = \Mage::getModel('Magento\GiftCardAccount\Model\Giftcardaccount');
        try {
            $giftcardAccount->setData($giftcardAccountData);
            $giftcardAccount->save();
        } catch (\Exception $e) {
            $this->_fault('invalid_giftcardaccount_data', $e->getMessage());
        }
        // send email notification if recipient parameters are set
        if (isset($notificationData)) {
            try {
                if($giftcardAccount->getStatus()){
                    $giftcardAccount->addData($notificationData);
                    $giftcardAccount->sendEmail();
                }
            } catch (\Exception $e) {
                $this->_fault('invalid_notification_data', $e->getMessage());
            }
        }
        return (int)$giftcardAccount->getId();
    }

    /**
     * Update GitCard Account
     *
     * @param integer $giftcardAccountId
     * @param array $giftcardData
     * @return bool
     */
    public function update($giftcardAccountId, $giftcardData)
    {
        $model = $this->_init($giftcardAccountId);
        $updateData = array();
        foreach ((array)$giftcardData as $field=> $value) {
            if (in_array($field, $this->_updateAllowedAttributes)) {
                if (isset($this->_mapAttributes[$field])) {
                    $field = $this->_mapAttributes[$field];
                }
                $updateData[$field] = $value;
            }
        }

        try{
            $model->addData($updateData)->save();
        }catch (\Exception $e){
            $this->_fault('unable_to_save');
            return false;
        }

        return true;
    }

    /**
     * Delete gift card account
     *
     * @param  int $giftcardAccountId
     * @return bool
     */
    public function remove($giftcardAccountId)
    {
        /** @var $giftcardAccount \Magento\GiftCardAccount\Model\Giftcardaccount */
        $giftcardAccount = \Mage::getModel('Magento\GiftCardAccount\Model\Giftcardaccount')->load($giftcardAccountId);
        if (!$giftcardAccount->getId()) {
            $this->_fault('giftcard_account_not_found_by_id');
        }
        try {
            $giftcardAccount->delete();
        } catch (\Exception $e) {
            $this->_fault('delete_error', $e->getMessage());
        }
        return true;
    }

    /**
     * Load model and check existence of GiftCard
     *
     * @param integer $giftcardId
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    protected function _init($giftcardId)
    {
        $model = \Mage::getModel('Magento\GiftCardAccount\Model\Giftcardaccount')
            ->load($giftcardId);

        if (!$model->getId()) {
            $this->_fault('not_exists');
        }

        return $model;
    }

    /**
     * Retrieve GiftCard model data to set into API response
     *
     * @param \Magento\GiftCardAccount\Model\Giftcardaccount
     * @return array
     */
    protected function _getEntityInfo($model)
    {
        return array(
            'giftcard_id'  => $model->getId(),
            'code'         => $model->getCode(),
            'store_id'     => $model->getWebsiteId(),
            'date_created' => $model->getDateCreated(),
            'expire_date'  => $model->getDateExpires(),
            'is_active'    => $model->getStatus(),
            'status'       => $model->getStateText(),
            'balance'      => $model->getBalance()
        );
    }

    /**
     * Checks giftcard account data
     *
     * @param  array $giftcardAccountData
     * @throws \Magento\Api\Exception
     * @return array
     */
    protected function _prepareCreateGiftcardAccountData($giftcardAccountData)
    {
        if (!isset($giftcardAccountData['status'])
            || !isset($giftcardAccountData['is_redeemable'])
            || !isset($giftcardAccountData['website_id'])
            || !isset($giftcardAccountData['balance'])
        ) {
            $this->_fault('invalid_giftcardaccount_data');
        }
        return $giftcardAccountData;
    }

    /**
     * Checks email notification data
     *
     * @param  null|array $notificationData
     * @throws \Magento\Api\Exception
     * @return array
     */
    protected function _prepareCreateNotificationData($notificationData = null)
    {
        if (isset($notificationData)) {
            if (!isset($notificationData['recipient_name'])
                || empty($notificationData['recipient_name'])
                || !isset($notificationData['recipient_email'])
                || empty($notificationData['recipient_email'])
            ) {
                $this->_fault('invalid_notification_data');
            }
        }
        return $notificationData;
    }
}
