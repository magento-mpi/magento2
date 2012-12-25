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
 * @package     Saas_PrintedTemplate
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
    extends Mage_Backend_Block_System_Config_Form_Field
{
    /**
     * Get html for "Enable Printed Template" dropdown
     *
     * Result html also contains of js which controlls accessibility of "put_order_id" options
     * On dropdown "change" event it toggles "disabled" attribute for "put_order_id" options
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
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

