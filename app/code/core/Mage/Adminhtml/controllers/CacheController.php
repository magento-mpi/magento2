<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Display cache management grid
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/cache')
            ->renderLayout();
    }

    /**
     * Flush cache storage
     */
    public function flushAllAction()
    {
        Mage::app()->getCacheInstance()->flush();
        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__("Cache storage was flushed successfully."));
        $this->_redirect('*/*');
    }

    /**
     * Flush all magento cache
     */
    public function flushSystemAction()
    {
        Mage::app()->cleanCache();
        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__("Magento cache storage was flushed successfully ."));
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
            $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__("%s cache type(s) enabled.", $updatedTypes));
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
            $tags = Mage::app()->getCacheInstance()->getTagsByType($code);
            if ($tags) {
                Mage::app()->cleanCache($tags);
            }
        }
        if ($updatedTypes > 0) {
            Mage::app()->saveUseCache($allTypes);
            $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__("%s cache type(s) disabled.", $updatedTypes));
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
                $tags = Mage::app()->getCacheInstance()->getTagsByType($type);
                if ($tags) {
                    Mage::app()->cleanCache($tags);
                    $updatedTypes++;
                }
            }
        }
        if ($updatedTypes > 0) {
            $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__("%s cache type(s) refreshed.", $updatedTypes));
        }
        $this->_redirect('*/*');
    }

    /**
     * Clean JS/css files cache
     */
    public function cleanMediaAction()
    {
        try {
            Mage::getModel('core/design_package')->cleanMergedJsCss();
            Mage::dispatchEvent('clean_media_cache_after');
            $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('JavaScript/CSS cache was cleaned successfully.')
            );
        }
        catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('adminhtml')->__('Error while cleared JavaScript/CSS cache. Please try again later.')
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
            Mage::getModel('catalog/product_image')->clearCache();
            Mage::dispatchEvent('clean_catalog_images_cache_after');
            $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('Image cache was cleaned successfully.')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('adminhtml')->__('Error while cleared Image cache. Please try again later.')
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
        return Mage::getSingleton('admin/session')->isAllowed('cache');
    }
}
