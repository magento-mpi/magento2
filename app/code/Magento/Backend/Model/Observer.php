<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend event observer
 */
namespace Magento\Backend\Model;

class Observer
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Core\Model\App $app
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Core\Model\App $app
    ) {
        $this->_backendSession = $backendSession;
        $this->_app = $app;
    }

    /**
     * Bind locale
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Backend\Model\Observer
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
     * Prepare mass action separated data
     *
     * @return \Magento\Backend\Model\Observer
     */
    public function massactionPrepareKey()
    {
        $request = $this->_app->getFrontController()->getRequest();
        $key = $request->getPost('massaction_prepare_key');
        if ($key) {
            $postData = $request->getPost($key);
            $value = is_array($postData) ? $postData : explode(',', $postData);
            $request->setPost($key, $value ? $value : null);
        }
        return $this;
    }

    /**
     * Clear result of configuration files access level verification in system cache
     *
     * @return \Magento\Backend\Model\Observer
     */
    public function clearCacheConfigurationFilesAccessLevelVerification()
    {
        return $this;
    }

    /**
     * Backend will always use base class for translation.
     *
     * @return \Magento\Backend\Model\Observer
     */
    public function initializeTranslation()
    {
        return $this;
    }

    /**
     * Set url class name for store 'admin'
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Backend\Model\Observer
     */
    public function setUrlClassName(\Magento\Event\Observer $observer)
    {
        /** @var $storeCollection \Magento\Core\Model\Resource\Store\Collection */
        $storeCollection = $observer->getEvent()->getStoreCollection();
        /** @var $store \Magento\Core\Model\Store */
        foreach ($storeCollection as $store) {
            if ($store->getId() == 0) {
                $store->setUrlClassName('Magento\Backend\Model\Url');
                break;
            }
        }
        $this->_app->removeCache(
            \Magento\AdminNotification\Model\System\Message\Security::VERIFICATION_RESULT_CACHE_KEY
        );
        return $this;
    }
}
