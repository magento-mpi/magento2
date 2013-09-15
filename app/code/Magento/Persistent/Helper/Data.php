<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Persistent Shopping Cart Data Helper
 *
 * @category   Magento
 * @package    Magento_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Persistent\Helper;

class Data extends \Magento\Core\Helper\Data
{
    const XML_PATH_ENABLED = 'persistent/options/enabled';
    const XML_PATH_LIFE_TIME = 'persistent/options/lifetime';
    const XML_PATH_LOGOUT_CLEAR = 'persistent/options/logout_clear';
    const XML_PATH_REMEMBER_ME_ENABLED = 'persistent/options/remember_enabled';
    const XML_PATH_REMEMBER_ME_DEFAULT = 'persistent/options/remember_default';
    const XML_PATH_PERSIST_SHOPPING_CART = 'persistent/options/shopping_cart';

    /**
     * Name of config file
     *
     * @var string
     */
    protected $_configFileName = 'persistent.xml';

    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * Checkout data
     *
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_checkoutData = null;

    /**
     * Core url
     *
     * @var \Magento\Core\Helper\Url
     */
    protected $_coreUrl = null;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Helper\Url $coreUrl
     * @param \Magento\Checkout\Helper\Data $checkoutData
     * @param \Magento\Persistent\Helper\Session $persistentSession
     * @param \Magento\Core\Helper\Http $coreHttp
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Config $config
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Helper\Url $coreUrl,
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Persistent\Helper\Session $persistentSession,
        \Magento\Core\Helper\Http $coreHttp,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Config $config
    ) {
        $this->_coreUrl = $coreUrl;
        $this->_checkoutData = $checkoutData;
        $this->_persistentSession = $persistentSession;
        parent::__construct($eventManager, $coreHttp, $context, $config);
    }

    /**
     * Checks whether Persistence Functionality is enabled
     *
     * @param int|string|\Magento\Core\Model\Store $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return \Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Checks whether "Remember Me" enabled
     *
     * @param int|string|\Magento\Core\Model\Store $store
     * @return bool
     */
    public function isRememberMeEnabled($store = null)
    {
        return \Mage::getStoreConfigFlag(self::XML_PATH_REMEMBER_ME_ENABLED, $store);
    }

    /**
     * Is "Remember Me" checked by default
     *
     * @param int|string|\Magento\Core\Model\Store $store
     * @return bool
     */
    public function isRememberMeCheckedDefault($store = null)
    {
        return \Mage::getStoreConfigFlag(self::XML_PATH_REMEMBER_ME_DEFAULT, $store);
    }

    /**
     * Is shopping cart persist
     *
     * @param int|string|\Magento\Core\Model\Store $store
     * @return bool
     */
    public function isShoppingCartPersist($store = null)
    {
        return \Mage::getStoreConfigFlag(self::XML_PATH_PERSIST_SHOPPING_CART, $store);
    }

    /**
     * Get Persistence Lifetime
     *
     * @param int|string|\Magento\Core\Model\Store $store
     * @return int
     */
    public function getLifeTime($store = null)
    {
        $lifeTime = intval(\Mage::getStoreConfig(self::XML_PATH_LIFE_TIME, $store));
        return ($lifeTime < 0) ? 0 : $lifeTime;
    }

    /**
     * Check if set `Clear on Logout` in config settings
     *
     * @return bool
     */
    public function getClearOnLogout()
    {
        return \Mage::getStoreConfigFlag(self::XML_PATH_LOGOUT_CLEAR);
    }

    /**
     * Retrieve url for unset long-term cookie
     *
     * @return string
     */
    public function getUnsetCookieUrl()
    {
        return $this->_getUrl('persistent/index/unsetCookie');
    }

    /**
     * Retrieve name of persistent customer
     *
     * @return string
     */
    public function getPersistentName()
    {
        return __('(Not %1?)', $this->escapeHtml($this->_persistentSession->getCustomer()->getName()));
    }

    /**
     * Retrieve path for config file
     *
     * @return string
     */
    public function getPersistentConfigFilePath()
    {
        return \Mage::getConfig()->getModuleDir('etc', $this->_getModuleName()) . DS . $this->_configFileName;
    }

    /**
     * Check whether specified action should be processed
     *
     * @param \Magento\Event\Observer $observer
     * @return bool
     */
    public function canProcess($observer)
    {
        $action = $observer->getEvent()->getAction();
        $controllerAction = $observer->getEvent()->getControllerAction();

        if ($action instanceof \Magento\Core\Controller\Varien\Action) {
            return !$action->getFlag('', \Magento\Core\Controller\Varien\Action::FLAG_NO_START_SESSION);
        }
        if ($controllerAction instanceof \Magento\Core\Controller\Varien\Action) {
            return !$controllerAction->getFlag('', \Magento\Core\Controller\Varien\Action::FLAG_NO_START_SESSION);
        }
        return true;
    }

    /**
     * Get create account url depends on checkout
     *
     * @param  $url string
     * @return string
     */
    public function getCreateAccountUrl($url)
    {
        if ($this->_checkoutData->isContextCheckout()) {
            $url = $this->_coreUrl->addRequestParam($url, array('context' => 'checkout'));
        }
        return $url;
    }

}
