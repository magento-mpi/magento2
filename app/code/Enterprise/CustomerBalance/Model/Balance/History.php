<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customerbalance history model
 *
 * @method Enterprise_CustomerBalance_Model_Resource_Balance_History _getResource()
 * @method Enterprise_CustomerBalance_Model_Resource_Balance_History getResource()
 * @method int getBalanceId()
 * @method Enterprise_CustomerBalance_Model_Balance_History setBalanceId(int $value)
 * @method string getUpdatedAt()
 * @method Enterprise_CustomerBalance_Model_Balance_History setUpdatedAt(string $value)
 * @method int getAction()
 * @method Enterprise_CustomerBalance_Model_Balance_History setAction(int $value)
 * @method float getBalanceAmount()
 * @method Enterprise_CustomerBalance_Model_Balance_History setBalanceAmount(float $value)
 * @method float getBalanceDelta()
 * @method Enterprise_CustomerBalance_Model_Balance_History setBalanceDelta(float $value)
 * @method string getAdditionalInfo()
 * @method Enterprise_CustomerBalance_Model_Balance_History setAdditionalInfo(string $value)
 * @method int getIsCustomerNotified()
 * @method Enterprise_CustomerBalance_Model_Balance_History setIsCustomerNotified(int $value)
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerBalance_Model_Balance_History extends Magento_Core_Model_Abstract
{
    const ACTION_UPDATED  = 1;
    const ACTION_CREATED  = 2;
    const ACTION_USED     = 3;
    const ACTION_REFUNDED = 4;
    const ACTION_REVERTED = 5;

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_CustomerBalance_Model_Resource_Balance_History');
    }

    /**
     * Available action names getter
     *
     * @return array
     */
    public function getActionNamesArray()
    {
        return array(
            self::ACTION_CREATED  => __('Created'),
            self::ACTION_UPDATED  => __('Updated'),
            self::ACTION_USED     => __('Used'),
            self::ACTION_REFUNDED => __('Refunded'),
            self::ACTION_REVERTED => __('Reverted'),
        );
    }

    /**
     * Validate balance history before saving
     *
     * @return Enterprise_CustomerBalance_Model_Balance_History
     */
    protected function _beforeSave()
    {
        $balance = $this->getBalanceModel();
        if ((!$balance) || !$balance->getId()) {
            Mage::throwException(__('You need a balance to save your balance history.'));
        }

        $this->addData(array(
            'balance_id'     => $balance->getId(),
            'updated_at'     => time(),
            'balance_amount' => $balance->getAmount(),
            'balance_delta'  => $balance->getAmountDelta(),
        ));

        switch ((int)$balance->getHistoryAction())
        {
            case self::ACTION_CREATED:
                // break intentionally omitted
            case self::ACTION_UPDATED:
                if (!$balance->getUpdatedActionAdditionalInfo()) {
                    if (Mage::getSingleton('Magento_Core_Model_StoreManagerInterface')->getStore()->isAdmin()
                        && $user = Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()
                    ) {
                        if ($user->getUsername()) {
                            if (!trim($balance->getComment())){
                                $this->setAdditionalInfo(__('By admin: %1.', $user->getUsername()));
                            }else{
                                $this->setAdditionalInfo(__('By admin: %1. (%2)', $user->getUsername(), $balance->getComment()));
                            }
                        }
                    }
                } else {
                    $this->setAdditionalInfo($balance->getUpdatedActionAdditionalInfo());
                }
                break;
            case self::ACTION_USED:
                $this->_checkBalanceModelOrder($balance);
                $this->setAdditionalInfo(__('Order #%1', $balance->getOrder()->getIncrementId()));
                break;
            case self::ACTION_REFUNDED:
                $this->_checkBalanceModelOrder($balance);
                if ((!$balance->getCreditMemo()) || !$balance->getCreditMemo()->getIncrementId()) {
                    Mage::throwException(__('There is no credit memo set to balance model.'));
                }
                $this->setAdditionalInfo(
                    __('Order #%1, creditmemo #%2', $balance->getOrder()->getIncrementId(), $balance->getCreditMemo()->getIncrementId())
                );
                break;
            case self::ACTION_REVERTED:
                $this->_checkBalanceModelOrder($balance);
                $this->setAdditionalInfo(__('Order #%1', $balance->getOrder()->getIncrementId()));
                break;
            default:
                Mage::throwException(__('Unknown balance history action code'));
                // break intentionally omitted
        }
        $this->setAction((int)$balance->getHistoryAction());

        return parent::_beforeSave();
    }

    /**
     * Send balance update if required
     *
     * @return Enterprise_CustomerBalance_Model_Balance_History
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        // attempt to send email
        $this->setIsCustomerNotified(false);
        if ($this->getBalanceModel()->getNotifyByEmail()) {
            $storeId = $this->getBalanceModel()->getStoreId();
            $email = Mage::getModel('Magento_Core_Model_Email_Template')
                ->setDesignConfig(array('store' => $storeId, 'area' => Mage::getDesign()->getArea()));
            $customer = $this->getBalanceModel()->getCustomer();
            $email->sendTransactional(
                Mage::getStoreConfig('customer/enterprise_customerbalance/email_template', $storeId),
                Mage::getStoreConfig('customer/enterprise_customerbalance/email_identity', $storeId),
                $customer->getEmail(), $customer->getName(),
                array(
                    'balance' => Mage::app()->getWebsite($this->getBalanceModel()->getWebsiteId())
                        ->getBaseCurrency()->format($this->getBalanceModel()->getAmount(), array(), false),
                    'name'    => $customer->getName(),
            ));
            if ($email->getSentSuccess()) {
                $this->getResource()->markAsSent($this->getId());
                $this->setIsCustomerNotified(true);
            }
        }

        return $this;
    }

    /**
     * Validate order model for balance update
     *
     * @param Magento_Sales_Model_Order $model
     */
    protected function _checkBalanceModelOrder($model)
    {
        if ((!$model->getOrder()) || !$model->getOrder()->getIncrementId()) {
            Mage::throwException(__('There is no order set to balance model.'));
        }
    }

    /**
     * Retrieve history data items as array
     *
     * @param  string $customerId
     * @param string|null $websiteId
     * @return array
     */
    public function getHistoryData($customerId, $websiteId = null)
    {
        $result = array();
        /** @var $collection Enterprise_CustomerBalance_Model_Resource_Balance_History_Collection */
        $collection = $this->getCollection()->loadHistoryData($customerId, $websiteId);
        foreach($collection as $historyItem) {
            $result[] = $historyItem->getData();
        }
        return $result;
    }
}
