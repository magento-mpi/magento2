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
 * @package     Mage_Admin
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin observer model
 *
 * @category    Mage
 * @package     Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_Observer
{
    const CAPTCHA_FORM_ID = 'backend_login';
    /**
     * Handler for controller_action_predispatch event
     *
     * @param Varien_Event_Observer $observer
     * @return boolean
     */
    public function actionPreDispatchAdmin($observer)
    {
        $session = Mage::getSingleton('admin/session');
        /** @var $session Mage_Admin_Model_Session */
        $request = Mage::app()->getRequest();
        $user = $session->getUser();

        $requestedActionName = $request->getActionName();
        $openActions = array(
            'forgotpassword',
            'resetpassword',
            'resetpasswordpost',
            'logout',
            'refresh' // captcha refresh
        );
        if (in_array($requestedActionName, $openActions)) {
            $request->setDispatched(true);
        } else {
            if($user) {
                $user->reload();
            }
            if (!$user || !$user->getId()) {
                if ($request->getPost('login')) {
                    $postLogin  = $request->getPost('login');
                    $isCaptchaOk = true;
                    /* @var $captcha Mage_Core_Model_Captcha_Zend */
                    $captcha = Mage::getModel('core/captcha_zend', self::CAPTCHA_FORM_ID);
                    if (!$captcha->isCorrect($request->getPost(Mage_Core_Helper_Captcha::INPUT_NAME_FIELD_VALUE))) {
                        $msg = Mage::helper('core/captcha')->__('Incorrect CAPTCHA.');
                        Mage::getSingleton('adminhtml/session')->addError($msg);
                        $isCaptchaOk = false;
                    }
                    if ($isCaptchaOk) {
                        /* @var $captchaHelper Mage_Core_Helper_Captcha */
                        $captchaHelper = Mage::helper('core/captcha');
                        $username   = isset($postLogin['username']) ? $postLogin['username'] : '';
                        $password   = isset($postLogin['password']) ? $postLogin['password'] : '';
                        /* @var $user Mage_Admin_Model_User */
                        $user = $session->login($username, $password, $request);
                        $captchaHelper->checkAttempt($user->getId(), self::CAPTCHA_FORM_ID);
                    }
                    $request->setPost('login', null);
                }
                if (!$request->getParam('forwarded')) {
                    if ($request->getParam('isIframe')) {
                        $request->setParam('forwarded', true)
                            ->setControllerName('index')
                            ->setActionName('deniedIframe')
                            ->setDispatched(false);
                    } elseif($request->getParam('isAjax')) {
                        $request->setParam('forwarded', true)
                            ->setControllerName('index')
                            ->setActionName('deniedJson')
                            ->setDispatched(false);
                    } else {
                        $request->setParam('forwarded', true)
                            ->setRouteName('adminhtml')
                            ->setControllerName('index')
                            ->setActionName('login')
                            ->setDispatched(false);
                    }
                    return false;
                }
            }
        }

        $session->refreshAcl();
    }

    /**
     * Unset session first visit flag after displaying page
     *
     * @deprecated after 1.4.0.1, logic moved to admin session
     * @param Varien_Event_Observer $event
     */
    public function actionPostDispatchAdmin($event)
    {
    }
}
