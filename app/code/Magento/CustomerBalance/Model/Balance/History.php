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
 * Customerbalance history model
 *
 * @method Magento_CustomerBalance_Model_Resource_Balance_History _getResource()
 * @method Magento_CustomerBalance_Model_Resource_Balance_History getResource()
 * @method int getBalanceId()
 * @method Magento_CustomerBalance_Model_Balance_History setBalanceId(int $value)
 * @method string getUpdatedAt()
 * @method Magento_CustomerBalance_Model_Balance_History setUpdatedAt(string $value)
 * @method int getAction()
 * @method Magento_CustomerBalance_Model_Balance_History setAction(int $value)
 * @method float getBalanceAmount()
 * @method Magento_CustomerBalance_Model_Balance_History setBalanceAmount(float $value)
 * @method float getBalanceDelta()
 * @method Magento_CustomerBalance_Model_Balance_History setBalanceDelta(float $value)
 * @method string getAdditionalInfo()
 * @method Magento_CustomerBalance_Model_Balance_History setAdditionalInfo(string $value)
 * @method int getIsCustomerNotified()
 * @method Magento_CustomerBalance_Model_Balance_History setIsCustomerNotified(int $value)
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerBalance_Model_Balance_History extends Magento_Core_Model_Abstract
{
    const ACTION_UPDATED  = 1;
    const ACTION_CREATED  = 2;
    const ACTION_USED     = 3;
    const ACTION_REFUNDED = 4;
    const ACTION_REVERTED = 5;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_CustomerBalance_Model_Resource_Balance_History');
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
     * @return Magento_CustomerBalance_Model_Balance_History
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
     * @return Magento_CustomerBalance_Model_Balance_History
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
                $this->_coreStoreConfig->getConfig('customer/magento_customerbalance/email_template', $storeId),
                $this->_coreStoreConfig->getConfig('customer/magento_customerbalance/email_identity', $storeId),
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
        /** @var $collection Magento_CustomerBalance_Model_Resource_Balance_History_Collection */
        $collection = $this->getCollection()->loadHistoryData($customerId, $websiteId);
        foreach($collection as $historyItem) {
            $result[] = $historyItem->getData();
        }
        return $result;
    }
}
