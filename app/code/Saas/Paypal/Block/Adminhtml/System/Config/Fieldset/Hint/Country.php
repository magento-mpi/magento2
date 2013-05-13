<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Paypal_Block_Adminhtml_System_Config_Fieldset_Hint_Country
    extends Saas_Paypal_Block_Adminhtml_System_Config_Fieldset_Hint
{
    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $elementOriginalData = $element->getOriginalData();
        if (isset($elementOriginalData['help_url'])) {
            $this->setHelpUrl($elementOriginalData['help_url']);
            $this->setHtmlId($element->getId());
        }
        return $this->toHtml();
    }
}
