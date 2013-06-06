<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Custom renderer for PayPal boarding account field, hide it if necessary
 */
class Saas_Paypal_Block_Adminhtml_System_Config_BoardingSandbox
    extends Mage_Backend_Block_System_Config_Form_Field
{
    /**
     * Show/hide or skips it
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml($element, $html)
    {
        $additionalAttributes = '';
        if (!Mage::helper('Saas_Paypal_Helper_Data')->isEcAcceleratedBoarding() || true) {
            $additionalAttributes = ' style="display: none;"';
        }
        // TODO: invent better way to hide element.
        $html = '<tr id="row_' . $element->getHtmlId() . '"' . $additionalAttributes . '>' . $html . '</tr>';
        if (!empty($additionalAttributes)) {
            $html .= '
            <script type="text/javascript">
                $$("#row_' . $element->getHtmlId() . ' td").invoke("hide");
            </script>
            ';
        }
        return $html;
    }
}
