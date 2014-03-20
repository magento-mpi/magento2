<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Store\Model;

use Magento\Profiler;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

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
     * @var \Magento\Store\Model\StoreManagerInterface[]
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
        $defaultStorageClassName = 'Magento\Store\Model\Storage\DefaultStorage',
        $installedStoreClassName = 'Magento\Store\Model\Storage\Db',
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
     * @return \Magento\Store\Model\StoreManagerInterface
     * @throws \InvalidArgumentException
     */
    public function get(array $arguments = array())
    {
        $className = $this->_appState->isInstalled() ?
            $this->_installedStoreClassName :
            $this->_defaultStorageClassName;

        if (false == isset($this->_cache[$className])) {
            /** @var $storage \Magento\Store\Model\StoreManagerInterface */
            $storage = $this->_objectManager->create($className, $arguments);

            if (false === ($storage instanceof \Magento\Store\Model\StoreManagerInterface)) {
                throw new \InvalidArgumentException($className
                    . ' doesn\'t implement \Magento\Store\Model\StoreManagerInterface'
                );
            }
            $this->_cache[$className] = $storage;
            if ($className === $this->_installedStoreClassName) {
                $this->_reinitStores($storage, $arguments);
                $useSid = $storage->getStore()
                    ->getConfig(\Magento\Core\Model\Session\SidResolver::XML_PATH_USE_FRONTEND_SID);
                $this->_sidResolver->setUseSessionInUrl($useSid);

                $this->_eventManager->dispatch('core_app_init_current_store_after');

                $store = $storage->getStore(true);
                if ($store->getConfig('dev/log/active') || $this->_appState->getMode() === \Magento\App\State::MODE_DEVELOPER) {
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
     * @param \Magento\Store\Model\StoreManagerInterface $storage
     * @param array $arguments
     * @return void
     */
    protected function _reinitStores(\Magento\Store\Model\StoreManagerInterface $storage, $arguments)
    {
        Profiler::start('init_stores');
        $storage->reinitStores();
        Profiler::stop('init_stores');

        $scopeCode = $arguments['scopeCode'];
        $scopeType = $arguments['scopeType']  ?: StoreManagerInterface::SCOPE_TYPE_STORE;
        if (empty($scopeCode) && false == is_null($storage->getWebsite(true))) {
            $scopeCode = $storage->getWebsite(true)->getCode();
            $scopeType = StoreManagerInterface::SCOPE_TYPE_WEBSITE;
        }
        switch ($scopeType) {
            case StoreManagerInterface::SCOPE_TYPE_STORE:
                $storage->setCurrentStore($scopeCode);
                break;
            case StoreManagerInterface::SCOPE_TYPE_GROUP:
                $storage->setCurrentStore($this->_getStoreByGroup($storage, $scopeCode));
                break;
            case StoreManagerInterface::SCOPE_TYPE_WEBSITE:
                $storage->setCurrentStore($this->_getStoreByWebsite($storage, $scopeCode));
                break;
            default:
                $storage->throwStoreException();
        }

        $currentStore = $storage->getCurrentStore();
        if (!empty($currentStore)) {
            $this->_checkCookieStore($storage, $scopeType);
            $this->_checkGetStore($storage, $scopeType);
        }
    }

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storage
     * @param string $scopeCode
     * @return null|string
     */
    protected function _getStoreByGroup(\Magento\Store\Model\StoreManagerInterface $storage, $scopeCode)
    {
        $groups = $storage->getGroups(true);
        $stores = $storage->getStores(true);
        if (!isset($groups[$scopeCode])) {
            return null;
        }
        if (!$groups[$scopeCode]->getDefaultStoreId()) {
            return null;
        }
        return $stores[$groups[$scopeCode]->getDefaultStoreId()]->getCode();
    }

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storage
     * @param string $scopeCode
     * @return null|string
     */
    protected function _getStoreByWebsite(\Magento\Store\Model\StoreManagerInterface $storage, $scopeCode)
    {
        $websites = $storage->getWebsites(true, true);
        if (!isset($websites[$scopeCode])) {
            return null;
        }
        if (!$websites[$scopeCode]->getDefaultGroupId()) {
            return null;
        }
        return $this->_getStoreByGroup($storage, $websites[$scopeCode]->getDefaultGroupId());
    }

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storage
     * @param string $scopeType
     * @return void
     */
    protected function _checkCookieStore(\Magento\Store\Model\StoreManagerInterface $storage, $scopeType)
    {
        if (!$this->_cookie->get()) {
            return;
        }

        $store = $this->_cookie->get(Store::COOKIE_NAME);
        $stores = $storage->getStores(true, true);
        if ($store && isset($stores[$store])
            && $stores[$store]->getId()
            && $stores[$store]->getIsActive()
        ) {
            if ($scopeType == 'website'
                && $stores[$store]->getWebsiteId() == $stores[$storage->getCurrentStore()]->getWebsiteId()
            ) {
                $storage->setCurrentStore($store);
            }
            if ($scopeType == 'group'
                && $stores[$store]->getGroupId() == $stores[$storage->getCurrentStore()]->getGroupId()
            ) {
                $storage->setCurrentStore($store);
            }
            if ($scopeType == 'store') {
                $storage->setCurrentStore($store);
            }
        }
    }

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storage
     * @param string $scopeType
     * @return void
     */
    protected function _checkGetStore(\Magento\Store\Model\StoreManagerInterface $storage, $scopeType)
    {
        if (empty($_GET)) {
            return;
        }

        if (!isset($_GET['___store'])) {
            return;
        }

        $store = $_GET['___store'];
        $stores = $storage->getStores(true, true);
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
        $curStoreObj = $stores[$storage->getCurrentStore()];
        if ($scopeType == 'website' && $storeObj->getWebsiteId() == $curStoreObj->getWebsiteId()) {
            $storage->setCurrentStore($store);
        } elseif ($scopeType == 'group' && $storeObj->getGroupId() == $curStoreObj->getGroupId()) {
            $storage->setCurrentStore($store);
        } elseif ($scopeType == 'store') {
            $storage->setCurrentStore($store);
        }

        if ($storage->getCurrentStore() == $store) {
            $store = $storage->getStore($store);
            if ($store->getWebsite()->getDefaultStore()->getId() == $store->getId()) {
                $this->_cookie->set(Store::COOKIE_NAME, null);
            } else {
                $this->_cookie->set(Store::COOKIE_NAME, $storage->getCurrentStore(), true);
                $this->_httpContext->setValue(Store::ENTITY, $storage->getCurrentStore());
            }
        }
        return;
    }
}
