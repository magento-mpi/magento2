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
 * @package     Mage_Captcha
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Captcha image model
 *
 * @category   Mage
 * @package    Mage_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Helper_Data extends Mage_Core_Helper_Abstract
{
    // Used for "name" attribute of captcha's input field
    const INPUT_NAME_FIELD_VALUE = 'captcha';

    // Always show captcha
    const MODE_ALWAYS     = 'always';

    // Show captcha only after certain number of unsuccessful attempts
    const MODE_AFTER_FAIL = 'after_fail';

    const SESSION_FAILED_ATTEMPTS = 'failed_attempts';

    const XML_PATH_CAPTCHA_FONTS = 'default/captcha/fonts';

    const CAPTCHA_USER_CREATE_FORM_ID = 'user_create';

    const CAPTCHA_USER_LOGIN_FORM_ID = 'user_login';

    const CAPTCHA_USER_FORGOTPASSWORD_FORM_ID = 'user_forgotpassword';

    const CAPTCHA_GUEST_CHECKOUT_FORM_ID = 'guest_checkout';

    const CAPTCHA_REGISTER_DURING_CHECKOUT_FORM_ID = 'register_during_checkout';

    const CAPTCHA_BACKEND_FORGOTPASSWORD_FORM_ID = 'backend_forgotpassword';

    const CAPTCHA_BACKEND_LOGIN_FORM_ID = 'backend_login';

    /* @var $_captcha Mage_Captcha_Model_Interface */
    protected $_captcha;

    /**
     * log Attempt
     *
     * @param string $formId
     *
     * @return Mage_Captcha_Helper_Interface
     */
    public function logAttempt($formId)
    {
        $attemptCount = (int)$this->getSession($formId)->getData(Mage_Captcha_Helper_Data::SESSION_FAILED_ATTEMPTS);
        $attemptCount++;
        $this->getSession($formId)->setData(Mage_Captcha_Helper_Data::SESSION_FAILED_ATTEMPTS, $attemptCount);
        return $this;
    }

    /**
     * Resets counter for previously logged incorrect attempts
     *
     * @param string $formId
     * @return Mage_Captcha_Helper_Interface
     */
    protected function _resetFailedAttempts($formId)
    {
        $this->getSession($formId)->unsetData(Mage_Captcha_Helper_Data::SESSION_FAILED_ATTEMPTS);
        return $this;
    }

    /**
     * Returns number of unsuccessful attempts after which captcha is shown
     *
     * @return int
     */
    protected function _getShowAfterFailedAttemptsNum()
    {
        $showAfterFailedAttemptsNum = (int)$this->getConfigNode('failed_attempts');
        return $showAfterFailedAttemptsNum;
    }

    /**
     * Returns session where to save data between page refreshes
     *
     * @param string $formId
     * @return Mage_Captcha_Model_Session
     */
    public function getSession($formId)
    {
        // Own session implementation used to avoid data substitution in case several captchas used on same page
        return Mage::getSingleton('captcha/session', array('formId' => $formId));
    }

    /**
     * Whether to show captcha for this form every time
     *
     * @param string $formId
     * @return bool
     */
    protected function _isShowAlways($formId = '')
    {
        $node = $this->getConfigNode('mode');
        $isShowAlways = ((string)$node == Mage_Captcha_Helper_Data::MODE_ALWAYS);
        if (!$isShowAlways && $formId) {
            if ($node = $this->getConfigNode('always_for')) {
                foreach ($node->children() as $nodeFormId => $isAlwaysFor) {
                    if ((bool)(string)$isAlwaysFor && ($formId == $nodeFormId)) {
                        $isShowAlways = true;
                        break;
                    }
                }
            }
        }
        return $isShowAlways;
    }

    /**
     * Whether captcha is enabled at this area
     *
     * @return bool
     */
    protected function _isEnabled()
    {
        return (bool)(string)$this->getConfigNode('enable');
    }

    /**
     * Returns value of the node with respect to current area (frontend or backend)
     *
     * @param string $id The last part of XML_PATH_$area_CAPTCHA_ constant (case insensitive)
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfigNode($id)
    {
        $area = Mage::app()->getStore()->isAdmin() ? 'admin' : 'customer';
        return Mage::getConfig()->getNode('default/' . $area . '/captcha/' . $id);
    }

    /**
     * Whether captcha is required to be inserted to this form
     *
     * @param string $formId
     * @return bool
     */
    public function isRequired($formId)
    {
        $targetForms = $this->_getTargetForms();
        if (empty($formId) || !$this->_isEnabled() || !in_array($formId, $targetForms)) {
            return false;
        }
        if ($this->_isShowAlways($formId)) {
            return true;
        }
        $sessionFailedAttempts = Mage_Captcha_Helper_Data::SESSION_FAILED_ATTEMPTS;
        $loggedFailedAttempts = (int)$this->getSession($formId)->getDataIgnoreTtl($sessionFailedAttempts);
        $showAfterFailedAttempts = $this->_getShowAfterFailedAttemptsNum();
        $isRequired = ($loggedFailedAttempts >= $showAfterFailedAttempts);
        return $isRequired;
    }

    /**
     * Get list of available fonts
     * Return format:
     * [['arial'] => ['label' => 'Arial', 'path' => '/www/magento/fonts/arial.ttf']]
     *
     * @return array
     */
    public function getFonts()
    {
        $node = Mage::getConfig()->getNode(Mage_Captcha_Helper_Data::XML_PATH_CAPTCHA_FONTS);
        $fonts = array();
        if ($node) {
            foreach ($node->children() as $fontName => $fontNode) {
                if (!empty($fontNode->label) && !empty($fontNode->path)) {
                    $path = (string)$fontNode->path;
                    if (!realpath($path)) {
                        // Seems it is not full path - adding base dir
                        $path = realpath(Mage::getBaseDir() . DS . $path);
                    }
                    if ($path && file_exists($path) && (is_file($path) || is_link($path))) {
                        $fonts[$fontName] = array('label' => (string)$fontNode->label, 'path' => $path);
                    }
                }
            }
        }
        return $fonts;
    }

    /**
     * Retrieve list of forms where captcha must be shown
     *
     * For frontend this list is based on current website
     *
     * @return array
     */
    protected function _getTargetForms()
    {
        $formsString = (string) $this->getConfigNode('forms');
        return explode(',', $formsString);
    }

    /**
     * Returns URL to controller action which returns new captcha image
     *
     * @return string
     */
    public function getRefreshUrl()
    {
        return Mage::getUrl("captcha/refresh");
    }


    /**
     * Get Captcha
     *
     * @param string $formId
     * @return Mage_Captcha_Model_Interface
     */
    public function getCaptcha($formId)
    {
        if (!$this->_captcha) {
            $this->_captcha = Mage::getModel('captcha/captcha', array('formId' => $formId));
        }
        return $this->_captcha;
    }
}
