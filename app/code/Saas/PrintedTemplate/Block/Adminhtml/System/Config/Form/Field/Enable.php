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
 * Configuration enable/disable PrintedTemplate module field.
 * When it's enabled all "put_order_id" options from sales module should be disabled.
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Adminhtml_System_Config_Form_Field_Enable
    extends Magento_Backend_Block_System_Config_Form_Field
{
    /**
     * Get html for "Enable Printed Template" dropdown
     *
     * Result html also contains of js which controlls accessibility of "put_order_id" options
     * On dropdown "change" event it toggles "disabled" attribute for "put_order_id" options
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);
        $html .= "<script type=\"text/javascript\">
            Event.observe(window, 'load', function() {
                // 'put_order_id' ids
                var dependant = ['sales_pdf_invoice_put_order_id', 'sales_pdf_shipment_put_order_id',
                    'sales_pdf_creditmemo_put_order_id'
                ];

                Event.observe('{$element->getHtmlId()}', 'change', function(){
                    // check if Printed Template is enabled and update 'put_order_id' options accessibility
                    var enabled = parseInt($('{$element->getHtmlId()}').value);
                    if (enabled) {
                        dependant.each(function(elementId) {
                            disableElement($(elementId));
                        });
                    } else {
                        dependant.each(function(elementId) {
                            enableElement($(elementId));
                        });
                    }
                });
            });
        </script>";
        return $html;
    }
}
