<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_OrderCreditMemo
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_OrderCreditMemo_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Provides partial or full refund
     *
     * @param string $refundButton
     * @param array $creditMemoData
     * @param bool $validate
     */
    public function createCreditMemoAndVerifyProductQty($refundButton, $creditMemoData = array(), $validate = true)
    {
        $verify = array();
        $this->addParameter('invoice_id', $this->getParameter('id'));
        $this->clickButton('credit_memo');
        foreach ($creditMemoData as $options) {
            if (is_array($options)) {
                $sku = (isset($options['return_filter_sku'])) ? $options['return_filter_sku'] : null;
                $productQty = (isset($options['qty_to_refund'])) ? $options['qty_to_refund'] : '%noValue%';
                if ($sku) {
                    $verify[$sku] = $productQty;
                    $this->addParameter('sku', $sku);
                    $this->fillForm($options);
                }
            }
        }
        if (!$verify && $validate) {
            /** $var $productElement PHPUnit_Extensions_Selenium2TestCase_Element*/
            foreach ($this->getControlElements('fieldset', 'product_line_to_refund') as $productElement) {
                $prodSku = $this->getChildElement($productElement, "//*[strong='SKU:']")->text();
                if ($options = $this->childElementIsPresent($productElement, '//td[@class="col-product"]/dl')) {
                    $prodSku = str_replace($options->text(), '', $prodSku);
                }
                list(, $prodSku) = explode('SKU: ', $prodSku);
                list($prodSku) = explode("\n", $prodSku);
                $qtyElement = $this->getChildElement($productElement, '//td[5]');
                $qtyInput = $this->childElementIsPresent($qtyElement, 'input');
                $prodQty = $qtyInput ? $qtyInput->value() : $qtyElement->text();
                $verify[trim($prodSku)] = trim($prodQty);
            }
        }
        $this->addParameter('elementXpath', $this->_getControlXpath('button', 'update_qty'));
        if ($this->controlIsPresent('pageelement', 'element_not_disabled')) {
            $this->clickButton('update_qty', false);
            $this->pleaseWait();
        }
        $this->clickButton($refundButton, false);
        $this->waitForPageToLoad();
        $this->paypalHelper()->verifyMagentoPayPalErrors();
        $this->orderHelper()->defineOrderId();
        $this->validatePage();
        if ($validate) {
            $this->assertMessagePresent('success', 'success_creating_creditmemo');
            foreach ($verify as $productSku => $qty) {
                if ($qty == '%noValue%') {
                    continue;
                }
                $this->addParameter('sku', $productSku);
                $this->addParameter('refundedQty', $qty);
                $this->assertTrue($this->controlIsPresent('field', 'qty_refunded'),
                    'Qty of refunded products is incorrect at the orders form');
            }
        }
    }
}