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
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

require_once  'Enterprise/Enterprise/controllers/Customer/AccountController.php';

/**
 * GiftRegistry customer account frontend controller
 *
 * @category   Enterprise
 * @package    Enterprise_GiftRegistry
 */
class Enterprise_GiftRegistry_Customer_AccountController extends Enterprise_Enterprise_Customer_AccountController
{
    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('createPost');

    /**
     * Predispatch custom logic
     *
     * Bypassing direct parent predispatch
     * Allowing only specific actions
     * Checking whether giftregistry functionality is enabled
     * Checking whether registration is allowed at all
     * No way to logged in customers
     */
    public function preDispatch()
    {
        Mage::log('Pre Dispatch ');
        Mage_Core_Controller_Front_Action::preDispatch();
        return $this;
    }

    /**
     * Hack real module name in order to make translations working correctly
     *
     * @return string
     */
    protected function _getRealModuleName()
    {
        return 'Mage_Customer';
    }

    /**
     * Initialize giftregistry from request
     *
     * @return Enterprise_GiftRegistry_Model_GiftRegistry
     */
    protected function _initGiftRegistry()
    {
        Mage::log('Init Customer');
    }

    /**
     * Customer register form page
     */
    public function createAction()
    {
	    Mage::log('Create aCtion');
        try {
//            $giftregistry = $this->_initGiftRegistry();
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->renderLayout();
            return;
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('customer/account/login');
    }

    /**
     * Create customer account action
     */
    public function createPostAction()
    {
        Mage::log('Create PostACtion');
        return $this;
    }

    /**
     * Make success redirect constant
     *
     * @param string $defaultUrl
     * @return Enterprise_GiftRegistry_Customer_AccountController
     */
    protected function _redirectSuccess($defaultUrl)
    {
        return $this->_redirect('customer/account/');
    }
}
