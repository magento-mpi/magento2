<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Store;

use Magento\Profiler;
use Magento\Core\Model\StoreManagerInterface;
use Magento\Core\Model\Store;

class StorageFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Default storage class name
     *
     * @var string
     */
    protected $_defaultStorageClassName;

    /**
     * Installed storage class name
     *
     * @var string
     */
    protected $_installedStoreClassName;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface[]
     */
    protected $_cache = array();

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Logger
     */
    protected $_log;

    /**
     * @var \Magento\Session\SidResolverInterface
     */
    protected $_sidResolver;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var string
     */
    protected $_writerModel;

    /**
     * @var \Magento\Stdlib\Cookie
     */
    protected $_cookie;

    /**
     * @var \Magento\App\Http\Context
     */
    protected $_httpContext;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storage;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Logger $logger
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\App\State $appState
     * @param \Magento\Stdlib\Cookie $cookie
     * @param \Magento\App\Http\Context $httpContext
     * @param string $defaultStorageClassName
     * @param string $installedStoreClassName
     * @param string $writerModel
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Logger $logger,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\App\State $appState,
        \Magento\Stdlib\Cookie $cookie,
        \Magento\App\Http\Context $httpContext,
        $defaultStorageClassName = 'Magento\Core\Model\Store\Storage\DefaultStorage',
        $installedStoreClassName = 'Magento\Core\Model\Store\Storage\Db',
        $writerModel = ''
    ) {
        $this->_objectManager = $objectManager;
        $this->_defaultStorageClassName = $defaultStorageClassName;
        $this->_installedStoreClassName = $installedStoreClassName;
        $this->_eventManager = $eventManager;
        $this->_log = $logger;
        $this->_appState = $appState;
        $this->_sidResolver = $sidResolver;
        $this->_writerModel = $writerModel;
        $this->_cookie = $cookie;
        $this->_httpContext = $httpContext;
    }

    /**
     * Get storage instance
     *
     * @param array $arguments
     * @return \Magento\Core\Model\StoreManagerInterface
     * @throws \InvalidArgumentException
     */
    public function get(array $arguments = array())
    {
        $className = $this->_appState->isInstalled() ?
            $this->_installedStoreClassName :
            $this->_defaultStorageClassName;

        if (false == isset($this->_cache[$className])) {
            $this->_storage = $this->_objectManager->create($className, $arguments);

            if (false === ($this->_storage instanceof \Magento\Core\Model\StoreManagerInterface)) {
                throw new \InvalidArgumentException($className
                    . ' doesn\'t implement \Magento\Core\Model\StoreManagerInterface'
                );
            }
            $this->_cache[$className] = $this->_storage;
            if ($className === $this->_installedStoreClassName) {
                $this->_reinitStores($arguments);
                $useSid = $this->_storage->getStore()
                    ->getConfig(\Magento\Core\Model\Session\SidResolver::XML_PATH_USE_FRONTEND_SID);
                $this->_sidResolver->setUseSessionInUrl($useSid);

                $this->_eventManager->dispatch('core_app_init_current_store_after');

                $store = $this->_storage->getStore(true);
                if ($store->getConfig('dev/log/active')) {

                    $this->_log->unsetLoggers();
                    $this->_log->addStreamLog(
                        \Magento\Logger::LOGGER_SYSTEM, $store->getConfig('dev/log/file'), $this->_writerModel);
                    $this->_log->addStreamLog(
                        \Magento\Logger::LOGGER_EXCEPTION,
                        $store->getConfig('dev/log/exception_file'),
                        $this->_writerModel
                    );
                }
            }
        }
        return $this->_cache[$className];
    }

    /**
     * Initialize currently ran store
     *
     * @param array $arguments
     * @return void
     */
    protected function _reinitStores($arguments)
    {
        Profiler::start('init_stores');
        $this->_storage->reinitStores();
        Profiler::stop('init_stores');

        $scopeCode = $arguments['scopeCode'];
        $scopeType = $arguments['scopeType']  ?: StoreManagerInterface::SCOPE_TYPE_STORE;
        if (empty($scopeCode) && false == is_null($this->_storage->getWebsite(true))) {
            $scopeCode = $this->_storage->getWebsite(true)->getCode();
            $scopeType = StoreManagerInterface::SCOPE_TYPE_WEBSITE;
        }
        switch ($scopeType) {
            case StoreManagerInterface::SCOPE_TYPE_STORE:
                $this->_storage->setCurrentStore($scopeCode);
                break;
            case StoreManagerInterface::SCOPE_TYPE_GROUP:
                $this->_storage->setCurrentStore($this->_getStoreByGroup($scopeCode));
                break;
            case StoreManagerInterface::SCOPE_TYPE_WEBSITE:
                $this->_storage->setCurrentStore($this->_getStoreByWebsite($scopeCode));
                break;
            default:
                $this->_storage->throwStoreException();
        }

        $currentStore = $this->_storage->getCurrentStore();
        if (!empty($currentStore)) {
            $this->_checkCookieStore($scopeType);
            $this->_checkGetStore($scopeType);
        }
    }

    /**
     * @param string $scopeCode
     * @return null|string
     */
    protected function _getStoreByGroup($scopeCode)
    {
        $groups = $this->_storage->getGroups(true);
        $stores = $this->_storage->getStores(true);
        if (!isset($groups[$scopeCode])) {
            return null;
        }
        if (!$groups[$scopeCode]->getDefaultStoreId()) {
            return null;
        }
        return $stores[$groups[$scopeCode]->getDefaultStoreId()]->getCode();
    }

    /**
     * @param string $scopeCode
     * @return null|string
     */
    protected function _getStoreByWebsite($scopeCode)
    {
        $websites = $this->_storage->getWebsites(true, true);
        if (!isset($websites[$scopeCode])) {
            return null;
        }
        if (!$websites[$scopeCode]->getDefaultGroupId()) {
            return null;
        }
        return $this->_getStoreByGroup($websites[$scopeCode]->getDefaultGroupId());
    }

    /**
     * @param string $scopeType
     * @return void
     */
    protected function _checkCookieStore($scopeType)
    {
        if (!$this->_cookie->get()) {
            return;
        }

        $store = $this->_cookie->get(Store::COOKIE_NAME);
        $stores = $this->_storage->getStores(true);
        if ($store && isset($stores[$store])
            && $stores[$store]->getId()
            && $stores[$store]->getIsActive()
        ) {
            if ($scopeType == 'website'
                && $stores[$store]->getWebsiteId() == $stores[$this->_storage->getCurrentStore()]->getWebsiteId()
            ) {
                $this->_storage->setCurrentStore($store);
            }
            if ($scopeType == 'group'
                && $stores[$store]->getGroupId() == $stores[$this->_storage->getCurrentStore()]->getGroupId()
            ) {
                $this->_storage->setCurrentStore($store);
            }
            if ($scopeType == 'store') {
                $this->_storage->setCurrentStore($store);
            }
        }
    }

    /**
     * @param string $scopeType
     * @return void
     */
    protected function _checkGetStore($scopeType)
    {
        if (empty($_GET)) {
            return;
        }

        if (!isset($_GET['___store'])) {
            return;
        }

        $store = $_GET['___store'];
        $stores = $this->_storage->getStores(true);
        if (!isset($stores[$store])) {
            return;
        }

        $storeObj = $stores[$store];
        if (!$storeObj->getId() || !$storeObj->getIsActive()) {
            return;
        }

        /**
         * prevent running a store from another website or store group,
         * if website or store group was specified explicitly
         */
        $curStoreObj = $stores[$this->_storage->getCurrentStore()];
        if ($scopeType == 'website' && $storeObj->getWebsiteId() == $curStoreObj->getWebsiteId()) {
            $this->_storage->setCurrentStore($store);
        } elseif ($scopeType == 'group' && $storeObj->getGroupId() == $curStoreObj->getGroupId()) {
            $this->_storage->setCurrentStore($store);
        } elseif ($scopeType == 'store') {
            $this->_storage->setCurrentStore($store);
        }

        if ($this->_storage->getCurrentStore() == $store) {
            $store = $this->_storage->getStore($store);
            if ($store->getWebsite()->getDefaultStore()->getId() == $store->getId()) {
                $this->_cookie->set(Store::COOKIE_NAME, null);
            } else {
                $this->_cookie->set(Store::COOKIE_NAME, $this->_storage->getCurrentStore(), true);
                $this->_httpContext->setValue(Store::ENTITY, $this->_storage->getCurrentStore());
            }
        }
        return;
    }
}
