<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Model\Balance;

use Magento\Core\Exception;

/**
 * Customerbalance history model
 *
 * @method \Magento\CustomerBalance\Model\Resource\Balance\History _getResource()
 * @method \Magento\CustomerBalance\Model\Resource\Balance\History getResource()
 * @method int getBalanceId()
 * @method \Magento\CustomerBalance\Model\Balance\History setBalanceId(int $value)
 * @method string getUpdatedAt()
 * @method \Magento\CustomerBalance\Model\Balance\History setUpdatedAt(string $value)
 * @method int getAction()
 * @method \Magento\CustomerBalance\Model\Balance\History setAction(int $value)
 * @method float getBalanceAmount()
 * @method \Magento\CustomerBalance\Model\Balance\History setBalanceAmount(float $value)
 * @method float getBalanceDelta()
 * @method \Magento\CustomerBalance\Model\Balance\History setBalanceDelta(float $value)
 * @method string getAdditionalInfo()
 * @method \Magento\CustomerBalance\Model\Balance\History setAdditionalInfo(string $value)
 * @method int getIsCustomerNotified()
 * @method \Magento\CustomerBalance\Model\Balance\History setIsCustomerNotified(int $value)
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class History extends \Magento\Core\Model\AbstractModel
{
    const ACTION_UPDATED = 1;

    const ACTION_CREATED = 2;

    const ACTION_USED = 3;

    const ACTION_REFUNDED = 4;

    const ACTION_REVERTED = 5;

    /**
     * Design package instance
     *
     * @var \Magento\View\DesignInterface
     */
    protected $_design = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\View\DesignInterface $design,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_transportBuilder = $transportBuilder;
        $this->_design = $design;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CustomerBalance\Model\Resource\Balance\History');
    }

    /**
     * Available action names getter
     *
     * @return array
     */
    public function getActionNamesArray()
    {
        return array(
            self::ACTION_CREATED => __('Created'),
            self::ACTION_UPDATED => __('Updated'),
            self::ACTION_USED => __('Used'),
            self::ACTION_REFUNDED => __('Refunded'),
            self::ACTION_REVERTED => __('Reverted')
        );
    }

    /**
     * Validate balance history before saving
     *
     * @return $this
     * @throws Exception
     */
    protected function _beforeSave()
    {
        $balance = $this->getBalanceModel();
        if (!$balance || !$balance->getId()) {
            throw new Exception(__('You need a balance to save your balance history.'));
        }

        $this->addData(
            array(
                'balance_id' => $balance->getId(),
                'updated_at' => time(),
                'balance_amount' => $balance->getAmount(),
                'balance_delta' => $balance->getAmountDelta()
            )
        );

        switch ((int)$balance->getHistoryAction()) {
            case self::ACTION_CREATED:
                // break intentionally omitted
            case self::ACTION_UPDATED:
                if ($balance->getUpdatedActionAdditionalInfo()) {
                    $this->setAdditionalInfo($balance->getUpdatedActionAdditionalInfo());
                }
                break;
            case self::ACTION_USED:
                $this->_checkBalanceModelOrder($balance);
                $this->setAdditionalInfo(__('Order #%1', $balance->getOrder()->getIncrementId()));
                break;
            case self::ACTION_REFUNDED:
                $this->_checkBalanceModelOrder($balance);
                if (!$balance->getCreditMemo() || !$balance->getCreditMemo()->getIncrementId()) {
                    throw new Exception(__('There is no credit memo set to balance model.'));
                }
                $this->setAdditionalInfo(
                    __(
                        'Order #%1, creditmemo #%2',
                        $balance->getOrder()->getIncrementId(),
                        $balance->getCreditMemo()->getIncrementId()
                    )
                );
                break;
            case self::ACTION_REVERTED:
                $this->_checkBalanceModelOrder($balance);
                $this->setAdditionalInfo(__('Order #%1', $balance->getOrder()->getIncrementId()));
                break;
            default:
                // break intentionally omitted
                throw new Exception(__('Unknown balance history action code'));
        }
        $this->setAction((int)$balance->getHistoryAction());

        return parent::_beforeSave();
    }

    /**
     * Send balance update if required
     *
     * @return $this
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        // attempt to send email
        $this->setIsCustomerNotified(false);
        if ($this->getBalanceModel()->getNotifyByEmail()) {
            $storeId = $this->getBalanceModel()->getStoreId();
            $customer = $this->getBalanceModel()->getCustomer();

            $transport = $this->_transportBuilder->setTemplateIdentifier(
                $this->_coreStoreConfig->getConfig('customer/magento_customerbalance/email_template', $storeId)
            )->setTemplateOptions(
                array('area' => $this->_design->getArea(), 'store' => $storeId)
            )->setTemplateVars(
                array(
                    'balance' => $this->_storeManager->getWebsite(
                        $this->getBalanceModel()->getWebsiteId()
                    )->getBaseCurrency()->format(
                        $this->getBalanceModel()->getAmount(),
                        array(),
                        false
                    ),
                    'name' => $customer->getName(),
                    'store' => $this->_storeManager->getStore($storeId)
                )
            )->setFrom(
                $this->_coreStoreConfig->getConfig('customer/magento_customerbalance/email_identity', $storeId)
            )->addTo(
                $customer->getEmail(),
                $customer->getName()
            )->getTransport();

            $transport->sendMessage();
            $this->getResource()->markAsSent($this->getId());
            $this->setIsCustomerNotified(true);
        }

        return $this;
    }

    /**
     * Validate order model for balance update
     *
     * @param \Magento\Sales\Model\Order $model
     * @return void
     * @throws Exception
     */
    protected function _checkBalanceModelOrder($model)
    {
        if (!$model->getOrder() || !$model->getOrder()->getIncrementId()) {
            throw new Exception(__('There is no order set to balance model.'));
        }
    }

    /**
     * Retrieve history data items as array
     *
     * @param string $customerId
     * @param string|null $websiteId
     * @return array
     */
    public function getHistoryData($customerId, $websiteId = null)
    {
        $result = array();
        /** @var $collection \Magento\CustomerBalance\Model\Resource\Balance\History\Collection */
        $collection = $this->getCollection()->loadHistoryData($customerId, $websiteId);
        foreach ($collection as $historyItem) {
            $result[] = $historyItem->getData();
        }
        return $result;
    }
}
