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
 * @package     Mage_OAuth
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth authorization block
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Block_Authorize extends Mage_Core_Block_Template
{
    /**
     * Set template
     */
    public function _construct()
    {
        parent::_construct();

        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getModel('customer/session');

        /** @var $adminSession Mage_Admin_Model_Session */
        $adminSession = Mage::getSingleton('admin/session');
        if ($session->isLoggedIn() || $adminSession->isLoggedIn()) {
            $template = 'oauth/authorize/form/button.phtml';
        } else {
            $displayType = $this->getRequest()->getParam('display');

            /** @var $helper Mage_OAuth_Helper_Data */
            $helper = Mage::helper('oauth');
            $template = $helper->isValidDisplayType($displayType) ? $displayType :
                Mage_OAuth_Helper_Data::DISPLAY_TYPE_CUSTOMER;

            $template = 'oauth/authorize/form/' . $template . '/login.phtml';
        }
        $this->setTemplate($template);
    }

    /**
     * Get the temporary credentials identifier received from the client.
     *
     * @return string
     */
    public function getOauthToken()
    {
        return $this->getRequest()->getQuery('oauth_token', null);
    }

    /**
     * Retrieve customer form posting url
     *
     * @return string
     */
    public function getCustomerPostActionUrl()
    {
        /** @var $helper Mage_Customer_Helper_Data */
        $helper = $this->helper('customer');
        return $helper->getLoginPostUrl();
    }

    /**
     * Retrieve admin form posting url
     *
     * @return string
     */
    public function getAdminPostActionUrl()
    {
        return $this->getUrl('adminhtml/index/login');
    }

    /**
     * Retrieve authorize url
     *
     * @return string
     */
    public function getAuthorizeUrl()
    {
        return $this->getUrl('*/authorize/index', array('oauth_token' => $this->escapeHtml($this->getOauthToken())));
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('core/session');
        return $session->getFormKey();
    }
}
