<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Custom renderer for PayPal API credentials wizard popup
 */
class Magento_Paypal_Block_Adminhtml_System_Config_ApiWizard extends Magento_Backend_Block_System_Config_Form_Field
{
    /**
     * Set template to itself
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/api_wizard.phtml');
        }
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(array(
            'button_label' => __($originalData['button_label']),
            'button_url'   => $originalData['button_url'],
            'html_id' => $element->getHtmlId(),
            'sandbox_button_label' => __($originalData['sandbox_button_label']),
            'sandbox_button_url'   => $originalData['sandbox_button_url'],
            'sandbox_html_id' => 'sandbox_' . $element->getHtmlId(),
        ));
        return $this->_toHtml();
    }
}
