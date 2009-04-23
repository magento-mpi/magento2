<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category   Enterprise
 * @package    Enterprise_AdminGws
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Controllers AdminGws validator
 */
class Enterprise_AdminGws_Model_Controllers
{
    /**
     * @var Enterprise_AdminGws_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * Initialize helper
     *
     */
    public function __construct()
    {
        $this->_helper  = Mage::helper('enterprise_admingws');
        $this->_request = Mage::app()->getRequest();
    }

    /**
     * Make sure the System Configuration pages are used in proper scopes
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateSystemConfig($controller)
    {
        // allow specific store view scope
        if ($storeCode = $this->_request->getParam('store')) {
            if ($store = Mage::app()->getStore($storeCode)) {
                if ($this->_helper->hasStoreAccess($store->getId())) {
                    return;
                }
            }
        }
        // allow specific website scope
        elseif ($websiteCode = $this->_request->getParam('website')) {
            if ($website = Mage::app()->getWebsite($websiteCode)) {
                if ($this->_helper->hasWebsiteAccess($website->getId(), true)) {
                    return;
                }
            }
        }

        // redirect to first allowed website or store scope
        if ($this->_helper->getWebsiteIds()) {
            return $this->_redirect($controller, Mage::getUrl('adminhtml/system_config/edit',
                array('website' => Mage::app()->getAnyStoreView()->getWebsite()->getCode()))
            );
        }
        $this->_redirect($controller, Mage::getUrl('adminhtml/system_config/edit',
            array('website' => Mage::app()->getAnyStoreView()->getWebsite()->getCode(), 'store' => Mage::app()->getAnyStoreView()->getCode()))
        );
    }

    /**
     * Validate catalog product requests
     */
    public function validateCatalogProduct()
    {
        // don't allow to create products, if there are no website permissions
        if ((!$this->_helper->getWebsiteIds())
            && ('new' === $this->_request->getActionName() || ('save' === $this->_request->getActionName() && !$this->_request->getParam('id')))) {
            return $this->_forward();
        }

        // allow specific store view scope
        if ($storeId = $this->_request->getParam('store')) {
            if ($store = Mage::app()->getStore($storeId)) {
                if ($this->_helper->hasStoreAccess($store->getId())) {
                    return;
                }
            }
        }
        else {
            return;
        }
        $this->_forward();
    }

    /**
     * Validate catalog product edit page
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    public function validateCatalogProductEdit($controller)
    {
        if (!$id = $this->_request->getParam('id')) {
            return $this->_redirect($controller, '*/*/');
        }
        if (!$store = Mage::app()->getStore($this->_request->getParam('store', 0))) {
            return $this->_redirect($controller, '*/*/');
        }
        $product = Mage::getModel('catalog/product')->load($id);
        if (!$product->getId()) {
            return $this->_redirect($controller, '*/*/');
        }
        if ($store->isAdmin() && $this->_helper->getProductDisallowedWebsiteIds($product)) {
            return $this->_redirect($controller, array('*/*/*', 'id' => $product->getId(), 'store' => Mage::app()->getAnyStoreView()->getId()));
        }
    }

    /**
     * Redirect to a specific page
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     */
    protected function _redirect($controller, $url = null)
    {
        $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        if (null === $url) {
            $url = Mage::getUrl('*/*/denied');
        }
        elseif (is_array($url)) {
            $url = Mage::getUrl(array_shift($url), $url);
        }
        elseif (false === strpos($url, 'http', 0)) {
            $url = Mage::getUrl($url);
        }
        Mage::app()->getResponse()->setRedirect($url);
    }

    /**
     * Forward current request
     */
    protected function _forward($action = 'denied', $module = null, $controller = null)
    {
        if ($this->_request->getActionName() === $action
            && (null === $module || $this->_request->getModuleName() === $module)
            && (null === $controller || $this->_request->getControllerName() === $controller)) {
            return;
        }

        if ($module) {
            $this->_request->setModuleName($module);
        }
        if ($controller) {
            $this->_request->setControllerName($controller);
        }
        $this->_request->setActionName($action)->setDispatched(false);
    }
}
