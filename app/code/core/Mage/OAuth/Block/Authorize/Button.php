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
class Mage_OAuth_Block_Authorize_Button extends Mage_Core_Block_Template
{
    /**
     * Retrieve confirm authorization url
     *
     * @return string
     */
    public function getConfirmUrl()
    {
        if ($this->_getUserType()==Mage_Api2_Model_Auth::USER_TYPE_ADMIN) {
            $controller = 'adminhtml_authorize';
        } else {
            $controller = 'authorize';
        }

        return  $this->getUrl('oauth/' . $controller . '/confirm', array('oauth_token'=>$this->getToken()));
    }

    /**
     * Retrieve reject authorization url
     *
     * @return string
     */
    public function getRejectUrl()
    {
        if ($this->_getUserType()==Mage_Api2_Model_Auth::USER_TYPE_ADMIN) {
            $controller = 'adminhtml_authorize';
        } else {
            $controller = 'authorize';
        }
        return  $this->getUrl('oauth/' . $controller . '/reject', array('oauth_token'=>$this->getToken()));
    }

    /**
     * Get the temporary credentials identifier received from the client.
     *
     * @return string
     */
    public function getToken()
    {
        $oAuthToken = $this->getData('token');
        if ($oAuthToken==null) {
            throw new Exception('OAuth token is not set.');
        }

        return $oAuthToken;
    }

    /**
     * Retrieve user type which set in Mage_OAuth_AuthorizeController or Mage_OAuth_Adminhtml_AuthorizeController
     *
     * @return string
     * @throws Exception
     */
    protected function _getUserType()
    {
        $userType = $this->getData('user_type');
        if ($userType==null) {
            throw new Exception('User type is not set.');
        }

        $types = array(Mage_Api2_Model_Auth::USER_TYPE_ADMIN, Mage_Api2_Model_Auth::USER_TYPE_CUSTOMER);
        if (!in_array($userType, $types)) {
            throw new Exception(sprintf('Invalid user type "%s".', $userType));
        }

        return $userType;
    }






    /**
     * Retrieve authorize url
     *
     * @return string
     */
    public function getAuthorizeUrl()
    {
        return $this->getUrl('*/authorize/index', array('oauth_token' => $this->escapeHtml($this->getToken())));
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
