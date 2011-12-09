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
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle Products Observer
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Captcha_Model_Observer_AbstractCustomer
{
    /**
     * Check Captcha
     *
     * @param Varien_Object $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function checkCaptcha($observer)
    {
        Mage::helper('captcha')->logAttempt($this->_getFormId());

        if (Mage::helper('captcha')->isRequired($this->_getFormId())){
            $captchaModel = Mage::helper('captcha')->getCaptcha($this->_getFormId());
            if (!$captchaModel->isCorrect($this->_getCaptchaString($observer))) {
                $this->_setupRedirect($observer->getControllerAction());
            }
        }
        return $this;
    }

    /**
     * Get FormId
     *
     * @abstract
     * @return string
     */
    abstract protected function _getFormId();


    /**
     * Setup Redirect if Captcha Wrong
     *
     * @param Mage_Core_Controller_Varien_Action $controller
     */
    abstract protected function _setupRedirect($controller);


    /**
     * Get Captcha String
     *
     * @param Varien_Object $observer
     * @return string
     */
    protected function _getCaptchaString($observer)
    {
        $request = $observer->getControllerAction()->getRequest();
        return $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
    }



    /**
     * Get Session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
}
