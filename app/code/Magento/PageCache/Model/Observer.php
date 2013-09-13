<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache observer model
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_PageCache_Model_Observer
{
    const XML_NODE_ALLOWED_CACHE = 'frontend/cache/allowed_requests';

    /**
     * Page cache data
     *
     * @var Magento_PageCache_Helper_Data
     */
    protected $_pageCacheData = null;

    /**
     * @param Magento_PageCache_Helper_Data $pageCacheData
     */
    public function __construct(
        Magento_PageCache_Helper_Data $pageCacheData
    ) {
        $this->_pageCacheData = $pageCacheData;
    }

    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->_pageCacheData->isEnabled();
    }

    /**
     * Check when cache should be disabled
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_PageCache_Model_Observer
     */
    public function processPreDispatch(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $action = $observer->getEvent()->getControllerAction();
        $request = $action->getRequest();
        $needCaching = true;

        if ($request->isPost()) {
            $needCaching = false;
        }

        $configuration = Mage::getConfig()->getNode(self::XML_NODE_ALLOWED_CACHE);

        if (!$configuration) {
            $needCaching = false;
        }

        $configuration = $configuration->asArray();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if (!isset($configuration[$module])) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['controller']) && $configuration[$module]['controller'] != $controller) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['action']) && $configuration[$module]['action'] != $action) {
            $needCaching = false;
        }

        if (!$needCaching) {
            $this->_pageCacheData->setNoCacheCookie();
        }

        return $this;
    }

    /**
     * Temporary disabling full page caching by setting bo-cache cookie
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_PageCache_Model_Observer
     */
    public function setNoCacheCookie(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_pageCacheData->setNoCacheCookie(0)->lockNoCacheCookie();
        return $this;
    }

    /**
     * Activating full page cache aby deleting no-cache cookie
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_PageCache_Model_Observer
     */
    public function deleteNoCacheCookie(Magento_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_pageCacheData->unlockNoCacheCookie()->removeNoCacheCookie();
        return $this;
    }
}
