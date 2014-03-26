<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model;

/**
 * Backend event observer
 */
class Observer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\App\CacheInterface
     */
    protected $cache;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @param Session $backendSession
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Magento\App\CacheInterface $cache,
        \Magento\App\RequestInterface $request
    ) {
        $this->_backendSession = $backendSession;
        $this->cache = $cache;
        $this->_request = $request;
    }

    /**
     * Bind locale
     *
     * @param \Magento\Event\Observer $observer
     * @return $this
     */
    public function bindLocale($observer)
    {
        $locale = $observer->getEvent()->getLocale();
        if ($locale) {
            $selectedLocale = $this->_backendSession->getLocale();
            if ($selectedLocale) {
                $locale->setLocaleCode($selectedLocale);
            }
        }
        return $this;
    }

    /**
     * Clear result of configuration files access level verification in system cache
     *
     * @return $this
     */
    public function clearCacheConfigurationFilesAccessLevelVerification()
    {
        return $this;
    }

    /**
     * Backend will always use base class for translation.
     *
     * @return $this
     */
    public function initializeTranslation()
    {
        return $this;
    }

    /**
     * Set url class name for store 'admin'
     *
     * @param \Magento\Event\Observer $observer
     * @return $this
     */
    public function setUrlClassName(\Magento\Event\Observer $observer)
    {
        /** @var $storeCollection \Magento\Core\Model\Resource\Store\Collection */
        $storeCollection = $observer->getEvent()->getStoreCollection();
        /** @var $store \Magento\Core\Model\Store */
        foreach ($storeCollection as $store) {
            if ($store->getId() == 0) {
                $store->setUrlClassName('Magento\Backend\Model\UrlInterface');
                break;
            }
        }
        $this->cache->remove(
            \Magento\AdminNotification\Model\System\Message\Security::VERIFICATION_RESULT_CACHE_KEY
        );
        return $this;
    }
}
