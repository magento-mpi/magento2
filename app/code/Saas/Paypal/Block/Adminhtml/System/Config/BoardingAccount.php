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
class Saas_Paypal_Block_Adminhtml_System_Config_BoardingAccount
    extends Mage_Backend_Block_System_Config_Form_Field
{
    /**
     * Hotfix for switching authentication method
     * TODO: remove this code and invent better solution
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var $paypalConfig Mage_Paypal_Model_Config */
        $paypalConfig = Mage::getModel('Mage_Paypal_Model_Config')
            ->setMethod(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);
        if ($paypalConfig->isMethodActive(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS) &&
            !$element->hasValue()
        ) {
            $element->setValue($paypalConfig->businessAccount);
        }
        return parent::render($element);
    }

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
        if (!Mage::helper('Saas_Paypal_Helper_Data')->isEcAcceleratedBoarding()) {
            $additionalAttributes = ' style="display: none;"';
        }
        // TODO: invent better way to hide element.
        $html = '<tr id="row_' . $element->getHtmlId() . '"' . $additionalAttributes . '>' . $html . '</tr>';
        if (!empty($additionalAttributes)) {
            $html .= '
            <script type="text/javascript">
                $$("#row_' . $element->getHtmlId() . ' td").invoke("hide");
                $$("#row_' . $element->getHtmlId() . ' td input[type=text]")[0].addClassName("not-required");
            </script>
            ';
        }
        return $html;
    }
}
