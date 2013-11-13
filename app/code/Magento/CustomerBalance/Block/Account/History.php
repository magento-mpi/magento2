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
 * Customer balance history block
 */
namespace Magento\CustomerBalance\Block\Account;

class History extends \Magento\Core\Block\Template
{
    /**
     * Balance history action names
     *
     * @var array
     */
    protected $_actionNames = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\CustomerBalance\Model\Balance\HistoryFactory
     */
    protected $_historyFactory;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\CustomerBalance\Model\Balance\HistoryFactory $historyFactory
     * @param \Magento\Customer\Model\Session $custoomerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\CustomerBalance\Model\Balance\HistoryFactory $historyFactory,
        \Magento\Customer\Model\Session $custoomerSession,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_customerSession = $custoomerSession;
        $this->_historyFactory = $historyFactory;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Check if history can be shown to customer
     *
     * @return bool
     */
    public function canShow()
    {
        return $this->_storeConfig->getConfigFlag('customer/magento_customerbalance/show_history');
    }

    /**
     * Retreive history events collection
     *
     * @return mixed
     */
    public function getEvents()
    {
        $customerId = $this->_customerSession->getCustomerId();
        if (!$customerId) {
            return false;
        }

        $collection = $this->_historyFactory->create()
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('website_id', $this->_storeManager->getStore()->getWebsiteId())
                ->addOrder('updated_at', 'DESC')
                ->addOrder('history_id', 'DESC');

        return $collection;
    }

    /**
     * Retrieve action labels
     *
     * @return array
     */
    public function getActionNames()
    {
        if (is_null($this->_actionNames)) {
            $this->_actionNames = $this->_historyFactory->create()->getActionNamesArray();
        }
        return $this->_actionNames;
    }

    /**
     * Retrieve action label
     *
     * @param mixed $action
     * @return string
     */
    public function getActionLabel($action)
    {
        $names = $this->getActionNames();
        if (isset($names[$action])) {
            return $names[$action];
        }
        return '';
    }
}
