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
            $this->_redirect($controller, Mage::getUrl('adminhtml/system_config/edit',
                array('website' => Mage::app()->getAnyStoreView()->getWebsite()->getCode()))
            );
            return;
        }
        $this->_redirect($controller, Mage::getUrl('adminhtml/system_config/edit',
            array('website' => Mage::app()->getAnyStoreView()->getWebsite()->getCode(), 'store' => Mage::app()->getAnyStoreView()->getCode()))
        );
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
            $url = Mage::getUrl('adminhtml/index/denied');
        }
        Mage::app()->getResponse()->setRedirect($url);
    }
}
