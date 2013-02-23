<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_CacheController extends Mage_Adminhtml_Controller_Action
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
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Cache_Types $cacheTypes
     * @param null $areaCode
     * @param array $invokeArgs
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Cache_Types $cacheTypes,
        $areaCode = null,
        array $invokeArgs = array()
    ) {
        parent::__construct(
            $request, $response, $objectManager, $frontController, $layoutFactory, $areaCode, $invokeArgs
        );
        $this->_cache = $cache;
        $this->_cacheTypes = $cacheTypes;
    }

    /**
     * Retrieve session model
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Adminhtml_Model_Session');
    }

    /**
     * Display cache management grid
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Cache Management'));

        $this->loadLayout()
            ->_setActiveMenu('Mage_Adminhtml::system_cache')
            ->renderLayout();
    }

    /**
     * Flush cache storage
     */
    public function flushAllAction()
    {
        Mage::dispatchEvent('adminhtml_cache_flush_all');
        $allTypes = array_keys($this->_cache->getTypes());
        foreach ($allTypes as $code) {
            $this->_cache->cleanType($code);
        }
        $this->_getSession()->addSuccess(
            Mage::helper('Mage_Adminhtml_Helper_Data')->__("The cache storage has been flushed.")
        );
        $this->_redirect('*/*');
    }

    /**
     * Flush all magento cache
     */
    public function flushSystemAction()
    {
        Mage::app()->cleanCache();
        Mage::dispatchEvent('adminhtml_cache_flush_system');
        $this->_getSession()->addSuccess(
            Mage::helper('Mage_Adminhtml_Helper_Data')->__("The Magento cache storage has been flushed.")
        );
        $this->_redirect('*/*');
    }

    /**
     * Mass action for cache enabling
     */
    public function massEnableAction()
    {
        $types = $this->getRequest()->getParam('types');
        $allTypes = array_keys($this->_cache->getTypes());
        $updatedTypes = 0;
        foreach ($types as $code) {
            if (in_array($code, $allTypes) && !$this->_cacheTypes->isEnabled($code)) {
                $this->_cacheTypes->setEnabled($code, true);
                $updatedTypes++;
            }
        }
        if ($updatedTypes > 0) {
            $this->_cacheTypes->persist();
            $this->_getSession()->addSuccess(
                Mage::helper('Mage_Adminhtml_Helper_Data')->__("%s cache type(s) enabled.", $updatedTypes)
            );
        }
        $this->_redirect('*/*');
    }

    /**
     * Mass action for cache disabling
     */
    public function massDisableAction()
    {
        $types = $this->getRequest()->getParam('types');
        $allTypes = array_keys($this->_cache->getTypes());
        $updatedTypes = 0;
        foreach ($types as $code) {
            if (in_array($code, $allTypes) && $this->_cacheTypes->isEnabled($code)) {
                $this->_cacheTypes->setEnabled($code, false);
                $updatedTypes++;
            }
            $this->_cache->cleanType($code);
        }
        if ($updatedTypes > 0) {
            $this->_cacheTypes->persist();
            $this->_getSession()->addSuccess(
                Mage::helper('Mage_Adminhtml_Helper_Data')->__("%s cache type(s) disabled.", $updatedTypes)
            );
        }
        $this->_redirect('*/*');
    }

    /**
     * Mass action for cache refresh
     */
    public function massRefreshAction()
    {
        $types = $this->getRequest()->getParam('types');
        $updatedTypes = 0;
        if (!empty($types)) {
            foreach ($types as $type) {
                $this->_cache->cleanType($type);
                Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => $type));
                $updatedTypes++;
            }
        }
        if ($updatedTypes > 0) {
            $this->_getSession()->addSuccess(
                Mage::helper('Mage_Adminhtml_Helper_Data')->__("%s cache type(s) refreshed.", $updatedTypes)
            );
        }
        $this->_redirect('*/*');
    }

    /**
     * Clean JS/css files cache
     */
    public function cleanMediaAction()
    {
        try {
            Mage::getModel('Mage_Core_Model_Design_Package')->cleanMergedJsCss();
            Mage::dispatchEvent('clean_media_cache_after');
            $this->_getSession()->addSuccess(
                Mage::helper('Mage_Adminhtml_Helper_Data')->__('The JavaScript/CSS cache has been cleaned.')
            );
        }
        catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('Mage_Adminhtml_Helper_Data')->__('An error occurred while clearing the JavaScript/CSS cache.')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
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
            Mage::dispatchEvent('clean_catalog_images_cache_after');
            $this->_getSession()->addSuccess(
                Mage::helper('Mage_Adminhtml_Helper_Data')->__('The image cache was cleaned.')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('Mage_Adminhtml_Helper_Data')->__('An error occurred while clearing the image cache.')
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
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Adminhtml::cache');
    }
}
