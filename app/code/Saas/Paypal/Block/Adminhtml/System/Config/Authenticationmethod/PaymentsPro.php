<?php
/**
 * Magento Saas Edition
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
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Custom renderer for PayPal Payment Pro authentication method
 */
class Saas_Paypal_Block_Adminhtml_System_Config_Authenticationmethod_PaymentsPro
    extends Mage_Backend_Block_System_Config_Form_Field implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Set template to the block
     *
     * @return Saas_Paypal_Block_Adminhtml_System_Config_Authenticationmethod_PaymentsPro
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('saas/paypal/system/config/authenticationmethod/paymentspro.phtml');
        }
        return $this;
    }

    /**
     * Option value for Authentication Method select control
     */
    const AUTHENTICATION_METHOD_OPTION_DIRECT           = 0;

    /**
     * Option value for Authentication Method select control
     */
    const AUTHENTICATION_METHOD_OPTION_DIRECT_BOARDING  = 1;

    /**
     * Render element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if ($this->_isNeedForceBoarding()) {
            $element->setValue(self::AUTHENTICATION_METHOD_OPTION_DIRECT_BOARDING);
        }
        $element->setScopeLabel(Mage::helper('Mage_Backend_Helper_Data')->__('[GLOBAL]'));
        $render = parent::render($element);
        $this->addData(array(
            'render'   => $render,
            'html_id'  => $element->getHtmlId(),
        ));
        return $this->toHtml();
    }

    /**
     * Hide Authentication Method option in case when permissions was enabled
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml($element, $html)
    {
        if ($this->_isNeedForceBoarding()
            || Mage_Backend_Block_System_Config_Form::SCOPE_WEBSITES == $element->getScope()
        ) {
            return '<tr id="row_' . $element->getHtmlId() . '" style="display:none;">' . $html . '</tr>';
        }
        return parent::_decorateRowHtml($element, $html);
    }

    /**
     * Determine force using Payments Pro Permissions (aka Boarding)
     *
     * @return bool
     */
    protected function _isNeedForceBoarding()
    {
        $methodDirect = Mage_Paypal_Model_Config::METHOD_WPP_DIRECT;
        $methodDirectBoarding = Saas_Paypal_Model_Boarding_Config::METHOD_DIRECT_BOARDING;
        $wppAuthPath  = 'payment/'. $methodDirect .'/authentication_method';
        $authPermOpt = Saas_Paypal_Model_System_Config_Source_AuthenticationMethod::TYPE_PERMISSIONS;
        $isBoardingActive = Mage::getStoreConfigFlag("payment/{$methodDirectBoarding}/active");
        $isBoardingWasActivated = Mage::getModel('Saas_Paypal_Model_Boarding_Config')->isWasActivated(
            $methodDirectBoarding
        );
        $isAuthMethodPerm = Mage::getStoreConfig($wppAuthPath) == $authPermOpt;

        return $isBoardingActive || $isBoardingWasActivated || $isAuthMethodPerm;
    }

    /**
     * Get values of authentication method select field
     * 'api_credentials' aka 'direct'
     * 'permissions' aka 'direct boarding'
     *
     * @param string $opt
     * @return int|string
     */
    public function getAuthMethOpt($opt = 'api_credentials')
    {
        switch($opt) {
            case 'api_credentials':
                return self::AUTHENTICATION_METHOD_OPTION_DIRECT;
                break;
            case 'permissions':
                return self::AUTHENTICATION_METHOD_OPTION_DIRECT_BOARDING;
                break;
        }
        return '';
    }

    /**
     * Whether Website Payments Pro active or not
     *
     * @return bool
     */
    public function isWppApiCredActive()
    {
        $wppActivePath = 'payment/'.  Mage_Paypal_Model_Config::METHOD_WPP_DIRECT .'/active';
        return Mage::getStoreConfigFlag($wppActivePath);
    }

    /**
     * Return business account
     *
     * @return string
     */
    public function getBusinessAccount()
    {
        $businessAccountPath = 'paypal/general/business_account';
        return Mage::getStoreConfig($businessAccountPath);
    }
}
