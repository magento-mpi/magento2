<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Field renderer for hidden fields
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Field_Hidden
    extends Mage_Backend_Block_System_Config_Form_Field
{
    /**
     * Decorate field row html to be invisible
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml($element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '" style="display: none;">' . $html . '</tr>';
    }
}
