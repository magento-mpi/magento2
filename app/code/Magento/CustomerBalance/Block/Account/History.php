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

class History extends \Magento\View\Element\Template
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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\CustomerBalance\Model\Balance\HistoryFactory $historyFactory
     * @param \Magento\Customer\Model\Session $custoomerSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\CustomerBalance\Model\Balance\HistoryFactory $historyFactory,
        \Magento\Customer\Model\Session $custoomerSession,
        array $data = array()
    ) {
        $this->_customerSession = $custoomerSession;
        $this->_historyFactory = $historyFactory;
        parent::__construct($context, $data);
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
     * Retrieve history events collection
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
