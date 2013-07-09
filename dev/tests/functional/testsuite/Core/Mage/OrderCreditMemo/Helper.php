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
            $productCount = $this->getControlCount('fieldset', 'product_line_to_refund');
            for ($i = 1; $i <= $productCount; $i++) {
                $this->addParameter('productNumber', $i);
                $qtyXpath = $this->_getControlXpath('field', 'product_qty');
                $prodSku = $this->getControlAttribute('field', 'product_sku', 'text');
                $pointer = 'SKU: ';
                $prodSku = substr($prodSku, strpos($prodSku, $pointer) + strlen($pointer));
                $this->addParameter('tableLineXpath', $qtyXpath);
                if ($this->controlIsPresent('pageelement', 'table_line_input')) {
                    $prodQty = $this->getControlAttribute('pageelement', 'table_line_input', 'selectedValue');
                } else {
                    $prodQty = $this->getControlAttribute('field', 'product_qty', 'text');
                }
                $verify[$prodSku] = $prodQty;
            }
        }
        $this->addParameter('elementXpath', $this->_getControlXpath('button', 'update_qty'));
        if ($this->controlIsPresent('pageelement', 'element_not_disabled')) {
            $this->clickButton('update_qty', false);
            $this->pleaseWait();
        }
        $this->clickButton($refundButton, false);
        $this->waitForPageToLoad();
        //@TODO
        //Remove workaround for getting fails, not skipping tests if payment methods are inaccessible
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