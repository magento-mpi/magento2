<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales module base helper
 */
namespace Magento\Sales\Helper;

class Reorder extends \Magento\Core\Helper\Data
{
    const XML_PATH_SALES_REORDER_ALLOW = 'sales/reorder/allow';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Helper\Http $coreHttp
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Locale $locale
     * @param \Magento\Core\Model\Date $dateModel
     * @param \Magento\Core\Model\App\State $appState
     * @param \Magento\Core\Model\Encryption $encryptor
     * @param \Magento\Customer\Model\Session $customerSession
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Helper\Http $coreHttp,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Locale $locale,
        \Magento\Core\Model\Date $dateModel,
        \Magento\Core\Model\App\State $appState,
        \Magento\Core\Model\Encryption $encryptor,
        \Magento\Customer\Model\Session $customerSession,
        $dbCompatibleMode = true
    )
    {
        $this->_customerSession = $customerSession;
        parent::__construct($context, $eventManager, $coreHttp, $config, $coreStoreConfig, $storeManager,
            $locale, $dateModel, $appState, $encryptor, $dbCompatibleMode
        );
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
     * @param \Magento\Core\Model\Store|int|null $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        if ($this->_coreStoreConfig->getConfig(self::XML_PATH_SALES_REORDER_ALLOW, $store)) {
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
