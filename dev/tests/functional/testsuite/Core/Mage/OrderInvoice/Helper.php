<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_OrderInvoice
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
class Core_Mage_OrderInvoice_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Provides partial or full invoice
     *
     * @param string $captureType
     * @param array $invoiceData
     */
    public function createInvoiceAndVerifyProductQty($captureType = null, $invoiceData = array())
    {
        $verify = array();
        $this->clickButton('invoice');
        foreach ($invoiceData as $options) {
            if (!is_array($options)) {
                continue;
            }
            $productQty = (isset($options['qty_to_invoice'])) ? $options['qty_to_invoice'] : '%noValue%';
            if (isset($options['invoice_product_sku'])) {
                $sku = $options['invoice_product_sku'];
                $verify[$sku] = $productQty;
                $this->addParameter('sku', $sku);
                $this->fillField('qty_to_invoice', $productQty);
            }
        }
        if ($captureType) {
            $this->fillDropdown('amount', $captureType);
        }
        if (!$verify) {
            $productCount = $this->getControlCount('fieldset', 'product_line_to_invoice');
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
        $this->clickButton('submit_invoice', false);
        $this->waitForNewPage();
        $this->validatePage();
        //@TODO
        //Remove workaround for getting fails, not skipping tests if payment methods are inaccessible
        $this->paypalHelper()->verifyMagentoPayPalErrors();
        $this->assertMessagePresent('success', 'success_creating_invoice');
        foreach ($verify as $productSku => $qty) {
            if ($qty == '%noValue%') {
                continue;
            }
            $this->addParameter('sku', $productSku);
            $this->addParameter('invoicedQty', $qty);
            $this->assertTrue($this->controlIsPresent('field', 'qty_invoiced'),
                'Qty of invoiced products is incorrect at the orders form');
        }
    }

    /**
     * Opens invoice
     *
     * @param array|string $searchData
     */
    public function openInvoice($searchData)
    {
        //Search Invoice
        $searchData = $this->fixtureDataToArray($searchData);
        $searchData = $this->_prepareDataForSearch($searchData);
        $invoiceLocator = $this->search($searchData, 'sales_invoice_grid');
        $this->assertNotNull($invoiceLocator, 'Invoice is not found with data: ' . print_r($searchData, true));
        $invoiceRowElement = $this->getElement($invoiceLocator);
        $invoiceUrl = $invoiceRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Invoice');
        $cellElement = $this->getChildElement($invoiceRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($invoiceUrl));
        //Open Invoice
        $this->url($invoiceUrl);
        $this->validatePage('view_invoice');
    }
}