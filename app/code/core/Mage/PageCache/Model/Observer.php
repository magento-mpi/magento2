<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache observer model
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PageCache_Model_Observer
{
    const XML_NODE_ALLOWED_CACHE = 'frontend/cache/allowed_requests';

    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return Mage::helper('Mage_PageCache_Helper_Data')->isEnabled();
    }

    /**
     * Check when cache should be disabled
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_PageCache_Model_Observer
     */
    public function processPreDispatch(Varien_Event_Observer $observer)
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
            Mage::helper('Mage_PageCache_Helper_Data')->setNoCacheCookie();
        }

        return $this;
    }

    /**
     * Temporary disabling full page caching if Desigh Editor was launched.
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function launchDesignEditor(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('Mage_Core_Model_Session')
            ->getCookie()
            ->set('NO_CACHE', true, 0);
        return $this;
    }

    /**
     * Activating full page cache after Design Editor was deactivated
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function exitDesignEditor(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('Mage_Core_Model_Session')
            ->getCookie()
            ->delete('NO_CACHE');
        return $this;
    }
}
