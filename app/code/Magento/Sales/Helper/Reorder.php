<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Helper;

/**
 * Sales module base helper
 */
class Reorder extends \Magento\Core\Helper\Data
{
    const XML_PATH_SALES_REORDER_ALLOW = 'sales/reorder/allow';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Customer\Model\Session $customerSession
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState,
        \Magento\Customer\Model\Session $customerSession,
        $dbCompatibleMode = true
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context, $scopeConfig, $storeManager, $appState, $dbCompatibleMode);
    }

    /**
     * @return bool
     */
    public function isAllow()
    {
        return $this->isAllowed();
    }

    /**
     * Check if reorder is allowed for given store
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        if ($this->_scopeConfig->getValue(self::XML_PATH_SALES_REORDER_ALLOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store)) {
            return true;
        }
        return false;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function canReorder(\Magento\Sales\Model\Order $order)
    {
        if (!$this->isAllowed($order->getStore())) {
            return false;
        }
        if ($this->_customerSession->isLoggedIn()) {
            return $order->canReorder();
        } else {
            return true;
        }
    }
}
