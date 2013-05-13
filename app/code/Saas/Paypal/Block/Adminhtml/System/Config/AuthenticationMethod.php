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
 * Custom renderer for PayPal EnterBoarding button
 */
class Saas_Paypal_Block_Adminhtml_System_Config_AuthenticationMethod
    extends Mage_Backend_Block_System_Config_Form_Field
{
    /**
     * Set template to the block
     *
     * @return Saas_Paypal_Block_Adminhtml_System_Config_AuthenticationMethod
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('Saas_Paypal::system/config/authentication_method.phtml');
        }
        return $this;
    }

    /**
     * Render element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var $paypalHelper Saas_Paypal_Helper_Data */
        $paypalHelper = Mage::helper('Saas_Paypal_Helper_Data');
        $isAccelerated  = $paypalHelper->isEcAcceleratedBoarding();
        $isEcCredentials = $paypalHelper->isEcCredentials();
        $isEcPermissions = $paypalHelper->isEcPermissions();

        $element->setValue($isEcCredentials
            ? Saas_Paypal_Model_System_Config_Source_AuthenticationMethod::TYPE_API_CREDENTIALS
            : Saas_Paypal_Model_System_Config_Source_AuthenticationMethod::TYPE_PERMISSIONS
        );
        $element->setDisabled(!$isEcCredentials);
        $forceFieldsetHide = Mage_Backend_Block_System_Config_Form::SCOPE_STORES == $element->getScope();
        $forceFieldHide = Mage_Backend_Block_System_Config_Form::SCOPE_WEBSITES == $element->getScope();
        $element->setScopeLabel(Mage::helper('Mage_Backend_Helper_Data')->__('[GLOBAL]'));
        $render = parent::render($element);
        $this->addData(array(
            'force_fieldset_hide'           => $forceFieldsetHide,
            'grandfather_id'                => $element->getContainer()->getContainer()->getHtmlId(),
            'render'                        => $render,
            'html_id'                       => $element->getHtmlId(),
            'payment_method'                => Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING,
            'is_accelerated_boarding'       => $isAccelerated,
            'is_ec_permissions'             => $isEcPermissions,
            'is_ec_credentials'             => $isEcCredentials,
            'is_hidden'                     => $forceFieldHide,
            'is_default_scope'              => $element->getScope() == 'default',
        ));

        $fieldConfig = $element->getFieldConfig();
        if (!empty($fieldConfig['create_account_link'])) {
            $this->setCreateAccountLink($fieldConfig['create_account_link']);
        }

        return $this->_toHtml();
    }

    /**
     * Override parent method to avoid decoration
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml($element, $html)
    {
        return $html;
    }
}
