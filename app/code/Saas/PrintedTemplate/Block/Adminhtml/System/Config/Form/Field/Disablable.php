<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Disablable frontend model
 * Checks if Printed Template functionality is enabled and returns disabled html element
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Adminhtml_System_Config_Form_Field_Disablable
    extends Mage_Backend_Block_System_Config_Form_Field
{
    /**
     * Get configuration option html
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $javascript = '';
        $moduleEnabled = Mage::getStoreConfig('sales_pdf/general/enable_printed_templates');

        if ($moduleEnabled) {
            $element->setDisabled(true);
            $element->addClass('disabled');

            // when option is disabled also disable "inherit" checkbox if exists
            $javascript = "<script type=\"text/javascript\">
            Event.observe(window, 'load', function() {
                var inheritCheckbox = $('{$element->getHtmlId()}_inherit');
                if (inheritCheckbox) {
                    disableElement(inheritCheckbox);
                }
            });
            </script>";
        } else {
            $element->setDisabled(false);
            $element->removeClass('disabled');
        }

        $html = parent::_getElementHtml($element);
        $html .= $javascript;

        return $html;
    }
}
