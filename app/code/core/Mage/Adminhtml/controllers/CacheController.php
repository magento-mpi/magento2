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
        Mage::app()->getCacheInstance()->flush();
        $this->_getSession()->addSuccess(Mage::helper('Mage_Adminhtml_Helper_Data')->__("The cache storage has been flushed."));
        $this->_redirect('*/*');
    }

    /**
     * Flush all magento cache
     */
    public function flushSystemAction()
    {
        Mage::app()->cleanCache();
        Mage::dispatchEvent('adminhtml_cache_flush_system');
        $this->_getSession()->addSuccess(Mage::helper('Mage_Adminhtml_Helper_Data')->__("The Magento cache storage has been flushed."));
        $this->_redirect('*/*');
    }

    /**
     * Mass action for cache enabeling
     */
    public function massEnableAction()
    {
        $types = $this->getRequest()->getParam('types');
        $allTypes = Mage::app()->useCache();

        $updatedTypes = 0;
        foreach ($types as $code) {
            if (empty($allTypes[$code])) {
                $allTypes[$code] = 1;
                $updatedTypes++;
            }
        }
        if ($updatedTypes > 0) {
            Mage::app()->saveUseCache($allTypes);
            $this->_getSession()->addSuccess(Mage::helper('Mage_Adminhtml_Helper_Data')->__("%s cache type(s) enabled.", $updatedTypes));
        }
        $this->_redirect('*/*');
    }

    /**
     * Mass action for cache disabeling
     */
    public function massDisableAction()
    {
        $types = $this->getRequest()->getParam('types');
        $allTypes = Mage::app()->useCache();

        $updatedTypes = 0;
        foreach ($types as $code) {
            if (!empty($allTypes[$code])) {
                $allTypes[$code] = 0;
                $updatedTypes++;
            }
            $tags = Mage::app()->getCacheInstance()->cleanType($code);
        }
        if ($updatedTypes > 0) {
            Mage::app()->saveUseCache($allTypes);
            $this->_getSession()->addSuccess(Mage::helper('Mage_Adminhtml_Helper_Data')->__("%s cache type(s) disabled.", $updatedTypes));
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
                $tags = Mage::app()->getCacheInstance()->cleanType($type);
                Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => $type));
                $updatedTypes++;
            }
        }
        if ($updatedTypes > 0) {
            $this->_getSession()->addSuccess(Mage::helper('Mage_Adminhtml_Helper_Data')->__("%s cache type(s) refreshed.", $updatedTypes));
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
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('Mage_Adminhtml::cache');
    }
}
