<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Controller_Cache extends Magento_Adminhtml_Controller_Action
{
    /**
     * @var Mage_Core_Model_Cache
     */
    private $_cache;

    /**
     * @var Mage_Core_Model_Cache_Types
     */
    private $_cacheTypes;

    /**
     * @var Mage_Core_Model_Cache_Frontend_Pool
     */
    private $_cacheFrontendPool;

    /**
     * @param Mage_Backend_Controller_Context $context
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Cache_Types $cacheTypes
     * @param Mage_Core_Model_Cache_Frontend_Pool $cacheFrontendPool
     * @param string $areaCode
     */
    public function __construct(
        Mage_Backend_Controller_Context $context,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Cache_Types $cacheTypes,
        Mage_Core_Model_Cache_Frontend_Pool $cacheFrontendPool,
        $areaCode = null
    ) {
        parent::__construct($context, $areaCode);
        $this->_cache = $cache;
        $this->_cacheTypes = $cacheTypes;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * Retrieve session model
     *
     * @return Magento_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return $this->_objectManager->get('Magento_Adminhtml_Model_Session');
    }

    /**
     * Display cache management grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Cache Management'));

        $this->loadLayout()
            ->_setActiveMenu('Magento_Adminhtml::system_cache')
            ->renderLayout();
    }

    /**
     * Flush cache storage
     */
    public function flushAllAction()
    {
        $this->_eventManager->dispatch('adminhtml_cache_flush_all');
        /** @var $cacheFrontend Magento_Cache_FrontendInterface */
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
        $this->_getSession()->addSuccess(
            Mage::helper('Magento_Adminhtml_Helper_Data')->__("You flushed the cache storage.")
        );
        $this->_redirect('*/*');
    }

    /**
     * Flush all magento cache
     */
    public function flushSystemAction()
    {
        /** @var $cacheFrontend Magento_Cache_FrontendInterface */
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->clean();
        }
        $this->_eventManager->dispatch('adminhtml_cache_flush_system');
        $this->_getSession()->addSuccess(
            Mage::helper('Magento_Adminhtml_Helper_Data')->__("The Magento cache storage has been flushed.")
        );
        $this->_redirect('*/*');
    }

    /**
     * Mass action for cache enabling
     */
    public function massEnableAction()
    {
        try {
            $types = $this->getRequest()->getParam('types');
            $updatedTypes = 0;
            $this->_validateTypes($types);
            foreach ($types as $code) {
                if (!$this->_cacheTypes->isEnabled($code)) {
                    $this->_cacheTypes->setEnabled($code, true);
                    $updatedTypes++;
                }
            }
            if ($updatedTypes > 0) {
                $this->_cacheTypes->persist();
                $this->_getSession()->addSuccess(
                    Mage::helper('Magento_Adminhtml_Helper_Data')->__("%s cache type(s) enabled.", $updatedTypes)
                );
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('Magento_Adminhtml_Helper_Data')->__('An error occurred while enabling cache.')
            );
        }
        $this->_redirect('*/*');
    }

    /**
     * Mass action for cache disabling
     */
    public function massDisableAction()
    {
        try {
            $types = $this->getRequest()->getParam('types');
            $updatedTypes = 0;
            $this->_validateTypes($types);
            foreach ($types as $code) {
                if ($this->_cacheTypes->isEnabled($code)) {
                    $this->_cacheTypes->setEnabled($code, false);
                    $updatedTypes++;
                }
                $this->_cache->cleanType($code);
            }
            if ($updatedTypes > 0) {
                $this->_cacheTypes->persist();
                $this->_getSession()->addSuccess(
                    Mage::helper('Magento_Adminhtml_Helper_Data')->__("%s cache type(s) disabled.", $updatedTypes)
                );
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('Magento_Adminhtml_Helper_Data')->__('An error occurred while disabling cache.')
            );
        }
        $this->_redirect('*/*');
    }

    /**
     * Mass action for cache refresh
     */
    public function massRefreshAction()
    {
        try {
            $types = $this->getRequest()->getParam('types');
            $updatedTypes = 0;
            $this->_validateTypes($types);
            foreach ($types as $type) {
                $this->_cache->cleanType($type);
                $this->_eventManager->dispatch('adminhtml_cache_refresh_type', array('type' => $type));
                $updatedTypes++;
            }
            if ($updatedTypes > 0) {
                $this->_getSession()->addSuccess(
                    Mage::helper('Magento_Adminhtml_Helper_Data')->__("%s cache type(s) refreshed.", $updatedTypes)
                );
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('Magento_Adminhtml_Helper_Data')->__('An error occurred while refreshing cache.')
            );
        }
        $this->_redirect('*/*');
    }

    /**
     * Check whether specified cache types exist
     *
     * @param array $types
     */
    protected function _validateTypes(array $types)
    {
        if (empty($types)) {
            return;
        }
        $allTypes = array_keys($this->_cache->getTypes());
        $invalidTypes = array_diff($types, $allTypes);
        if (count($invalidTypes) > 0) {
            Mage::throwException(Mage::helper('Magento_Adminhtml_Helper_Data')
                ->__("Specified cache type(s) don't exist: " . join(', ', $invalidTypes)));
        }
    }

    /**
     * Clean JS/css files cache
     */
    public function cleanMediaAction()
    {
        try {
            $this->_objectManager->get('Mage_Core_Model_Page_Asset_MergeService')
                ->cleanMergedJsCss();
            $this->_eventManager->dispatch('clean_media_cache_after');
            $this->_getSession()->addSuccess(
                $this->_objectManager->get('Magento_Adminhtml_Helper_Data')->__('The JavaScript/CSS cache has been cleaned.')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                $this->_objectManager->get('Magento_Adminhtml_Helper_Data')->__('An error occurred while clearing the JavaScript/CSS cache.')
            );
        }
        $this->_redirect('*/*');
    }

    /**
     * Clean JS/css files cache
     */
    public function cleanImagesAction()
    {
        try {
            Mage::getModel('Mage_Catalog_Model_Product_Image')->clearCache();
            $this->_eventManager->dispatch('clean_catalog_images_cache_after');
            $this->_getSession()->addSuccess(
                Mage::helper('Magento_Adminhtml_Helper_Data')->__('The image cache was cleaned.')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('Magento_Adminhtml_Helper_Data')->__('An error occurred while clearing the image cache.')
            );
        }
        $this->_redirect('*/*');
    }

    /**
     * Check if cache management is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::cache');
    }
}
