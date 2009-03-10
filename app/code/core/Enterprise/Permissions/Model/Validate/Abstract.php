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
 * @category   Enterprise
 * @package    Enterprise_Permissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_Permissions_Model_Validate_Abstract
{
    protected $_observer;

    public function setObserver($observer)
    {
        $this->_observer = $observer;
    }

    /**
     * @return Varien_Event_Observer
     */
    protected function _getObserver()
    {
        return $this->_observer;
    }

    protected function _validateScope($redirectUri=false, $urlParams=false)
    {
        if( !Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            $store = $this->_getRequest()->getParam('store');

            if( !Mage::helper('enterprise_permissions')->hasScopeAccess(null, $store) ) {
                $this->_redirect($redirectUri, $urlParams);
            }
        }
        return $this;
    }

    protected function _redirect($redirectUri=false, $urlParams=false)
    {
        $allowedStores = Mage::helper('enterprise_permissions')->getAllowedStoreViews();

        if( sizeof($allowedStores) > 0 ) {
            $store = Mage::getModel('core/store')->load(array_shift($allowedStores));
            $params = array(
                'store' => $store->getId(),
                'id' => $this->_getRequest()->getParam('id'),
                '_current' => true
            );

            if( $urlParams && is_array($urlParams) ) {
                $params = array_merge($params, $urlParams);
            }

            $url = Mage::getUrl( $redirectUri ? $redirectUri : '*/*/*', $params);
        } else {
            $url = false;
        }
        if( $url ) {
            $this->_getObserver()->getEvent()->getControllerAction()->getResponse()->setRedirect($url);
        } else {
            $this->_raiseDenied();
        }
    }

    protected function _raiseDenied()
    {
        $this->_getObserver()
             ->getEvent()
             ->getControllerAction()
             ->getResponse()
             ->setRedirect(Mage::getUrl('adminhtml/index/denied'));
    }

    protected function _getRequest()
    {
        return $this->_getObserver()->getEvent()->getControllerAction()->getRequest();
    }
}