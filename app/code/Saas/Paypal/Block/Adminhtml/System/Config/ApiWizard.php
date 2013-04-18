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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom renderer for PayPal API credentials wizard popup
 */
class Saas_Paypal_Block_Adminhtml_System_Config_ApiWizard extends Mage_Paypal_Block_Adminhtml_System_Config_ApiWizard
{
    /**
     * Set template to itself
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('saas/paypal/system/config/api_wizard.phtml');
        parent::_prepareLayout();

        return $this;
    }

    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $buttonUrl = Mage::helper('core')->jsonEncode(
            $element->getFieldConfig()->{'button_url'}->asCanonicalArray()
        );
        $sandboxButtonUrl = Mage::helper('core')->jsonEncode(
            $element->getFieldConfig()->{'sandbox_button_url'}->asCanonicalArray()
        );

        $originalData = $element->getOriginalData();
        $this->addData(array(
            'button_label' => Mage::helper('paypal')->__($originalData['button_label']),
            'button_url'   => $buttonUrl,
            'html_id' => $element->getHtmlId(),
            'sandbox_button_label' => Mage::helper('paypal')->__($originalData['sandbox_button_label']),
            'sandbox_button_url'   => $sandboxButtonUrl,
            'sandbox_html_id' => 'sandbox_' . $element->getHtmlId(),
        ));
        return $this->_toHtml();
    }
}
