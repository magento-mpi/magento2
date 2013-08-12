<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage unit_tests
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Saas_PrintedTemplate_Model_Variable_Item_InvoiceTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test Get order Item method
     * @dataProvider initVariableProvider
     */
    public function testInitVariable($settings, $taxRatesSettings, $expectedValues)
    {
        $valueModel = new Magento_Object();
        foreach ($settings as $key => $setting) {
            $valueModel->setData($key, $setting);
        }

        $order = $this->getMockBuilder('Magento_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array('formatPriceTxt'))
            ->getMock();

        $order->expects($this->any())
            ->method('formatPriceTxt')
            ->will($this->returnCallback(array($this, 'formatPriceTxt')));

        $invoice = new Magento_Object();
        $invoice->setOrder($order);
        $valueModel->setInvoice($invoice);

        $taxRates = array();
        foreach ($taxRatesSettings as $taxRateSettings) {
            $taxRate = new Magento_Object();
            if (isset($taxRateSettings['is_discount_on_incl_tax'])) {
                $taxRate->setIsDiscountOnInclTax($taxRateSettings['is_discount_on_incl_tax']);
            }
            if (isset($taxRateSettings['real_percent'])) {
                $taxRate->setRealPercent($taxRateSettings['real_percent']);
            }

            $taxRates[] = $taxRate;
        }
        $valueModel->setTaxRates($taxRates);

        $invoiceVariable = new Saas_PrintedTemplate_Model_Variable_Item_FakeInvoice($valueModel);

        foreach ($expectedValues as $key => $expectedValue) {
            $this->assertEquals($expectedValue, $invoiceVariable->getData($key));
        }
    }

    /**
     * Callback for price text formatting
     *
     * @param string $value
     * @return string
     */
    public function formatPriceTxt($value)
    {
        return $value . ' formatted_price_txt';
    }

    /**
     * Data provider for init variable test
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD)
     */
    public function initVariableProvider()
    {
        return array(
            array(
                array(),
                array(),
                array(
                    'discount' => '',
                    'discount_amount' => $this->formatPriceTxt('0'),
                    'discount_amount_excl_tax' => $this->formatPriceTxt('0'),
                    'discount_excl_tax' => '',
                    'price_incl_discount' => '',
                    'price_incl_discount_excl_tax' => '',
                    'row_total_incl_discount' => '',
                    'row_total_incl_discount_excl_tax' => '',
                    'row_total_incl_discount_and_tax' => '',
                    'discount_rate' => '0%',
                    'price_incl_tax' => ''
                )
            ),

            array(
                array(
                    'discount_amount' => 2,
                    'qty' => 0,
                    'weee_tax_applied_row_amount' => 0,
                    'row_total_incl_tax' => 0,
                    'tax_amount' => 3
                ),
                array(),
                array(
                    'discount' => '',
                    'discount_amount' => $this->formatPriceTxt('2'),
                    'discount_amount_excl_tax' => $this->formatPriceTxt('2'),
                    'discount_excl_tax' => '',
                    'price_incl_discount' => '',
                    'price_incl_discount_excl_tax' => '',
                    'row_total_incl_discount' => '',
                    'row_total_incl_discount_excl_tax' => '',
                    'row_total_incl_discount_and_tax' =>  $this->formatPriceTxt('-2'),
                    'discount_rate' => '0%',
                    'price_incl_tax' => ''
                )
            ),

            array(
                array(
                    'discount_amount' => 2,
                    'qty' => 3,
                    'weee_tax_applied_row_amount' => 0,
                    'row_total_incl_tax' => 0,
                    'tax_amount' => 3
                ),
                array(),
                array(
                    'discount' => '0.66666666666667 formatted_price_txt',
                    'discount_amount' => $this->formatPriceTxt('2'),
                    'discount_amount_excl_tax' => $this->formatPriceTxt('2'),
                    'discount_excl_tax' => '0.66666666666667 formatted_price_txt',
                    'price_incl_discount' => '',
                    'price_incl_discount_excl_tax' => '',
                    'row_total_incl_discount' => '',
                    'row_total_incl_discount_excl_tax' => '',
                    'row_total_incl_discount_and_tax' =>  $this->formatPriceTxt('-2'),
                    'discount_rate' => '0%',
                    'price_incl_tax' => $this->formatPriceTxt('1')
                )
            ),

            array(
                array(
                    'discount_amount' => 2,
                    'qty' => 3,
                    'discount' => '1',
                    'weee_tax_applied_row_amount' => 0,
                    'row_total_incl_tax' => 0,
                    'tax_amount' => 3
                ),
                array(),
                array(
                    'discount' => '1 formatted_price_txt',
                    'discount_amount' => $this->formatPriceTxt('2'),
                    'discount_amount_excl_tax' => $this->formatPriceTxt('2'),
                    'discount_excl_tax' => '0.66666666666667 formatted_price_txt',
                    'price_incl_discount' => '',
                    'price_incl_discount_excl_tax' => '',
                    'row_total_incl_discount' => '',
                    'row_total_incl_discount_excl_tax' => '',
                    'row_total_incl_discount_and_tax' =>  $this->formatPriceTxt('-2'),
                    'discount_rate' => '0%',
                    'price_incl_tax' => $this->formatPriceTxt('1')
                )
            ),

            array(
                array(
                    'discount_amount' => 2,
                    'qty' => 1,
                    'weee_tax_applied_row_amount' => 4,
                    'row_total_incl_tax' => 6,
                    'tax_amount' => 3,
                    'row_total' => 10,
                    'price' => 12
                ),
                array(),
                array(
                    'discount' => '2 formatted_price_txt',
                    'discount_amount' => $this->formatPriceTxt('2'),
                    'discount_amount_excl_tax' => $this->formatPriceTxt('2'),
                    'discount_excl_tax' => '2 formatted_price_txt',
                    'price_incl_discount' => $this->formatPriceTxt('10'),
                    'price_incl_discount_excl_tax' => $this->formatPriceTxt('10'),
                    'row_total_incl_discount' => $this->formatPriceTxt('8'),
                    'row_total_incl_discount_excl_tax' => $this->formatPriceTxt('8'),
                    'row_total_incl_discount_and_tax' =>  $this->formatPriceTxt('4'),
                    'discount_rate' => '20%',
                    'price_incl_tax' => $this->formatPriceTxt('17')
                )
            ),

            ////////////////////////////////////////////

            array(
                array(
                    'discount_amount' => 2,
                    'qty' => 0,
                    'weee_tax_applied_row_amount' => 0,
                    'row_total_incl_tax' => 0,
                    'tax_amount' => 3
                ),
                array(
                    array('real_percent' => 0.1), array('real_percent' => 0.2)
                ),
                array(
                    'discount' => '',
                    'discount_amount' => $this->formatPriceTxt('2'),
                    'discount_amount_excl_tax' => $this->formatPriceTxt('2'),
                    'discount_excl_tax' => '',
                    'price_incl_discount' => '',
                    'price_incl_discount_excl_tax' => '',
                    'row_total_incl_discount' => '',
                    'row_total_incl_discount_excl_tax' => '',
                    'row_total_incl_discount_and_tax' =>  $this->formatPriceTxt('-2'),
                    'discount_rate' => '0%',
                    'price_incl_tax' => ''
                )
            ),

            array(
                array(
                    'discount_amount' => 2,
                    'qty' => 0,
                    'weee_tax_applied_row_amount' => 0,
                    'row_total_incl_tax' => 0,
                    'tax_amount' => 3
                ),
                array(
                    array('is_discount_on_incl_tax' => true, 'real_percent' => 0.1), array('real_percent' => 0.2)
                ),
                array(
                    'discount' => '',
                    'discount_amount' => $this->formatPriceTxt('2'),
                    'discount_amount_excl_tax' => $this->formatPriceTxt('1.9940179461615'),
                    'discount_excl_tax' => '',
                    'price_incl_discount' => '',
                    'price_incl_discount_excl_tax' => '',
                    'row_total_incl_discount' => '',
                    'row_total_incl_discount_excl_tax' => '',
                    'row_total_incl_discount_and_tax' =>  $this->formatPriceTxt('-2'),
                    'discount_rate' => '0%',
                    'price_incl_tax' => ''
                )
            ),

            array(
                array(
                    'discount_amount' => 2,
                    'qty' => 0,
                    'weee_tax_applied_row_amount' => 0,
                    'row_total_incl_tax' => 0,
                    'tax_amount' => 3
                ),
                array(
                    array('is_discount_on_incl_tax' => true, 'real_percent' => 0.1)
                ),
                array(
                    'discount' => '',
                    'discount_amount' => $this->formatPriceTxt('2'),
                    'discount_amount_excl_tax' => $this->formatPriceTxt('1.998001998002'),
                    'discount_excl_tax' => '',
                    'price_incl_discount' => '',
                    'price_incl_discount_excl_tax' => '',
                    'row_total_incl_discount' => '',
                    'row_total_incl_discount_excl_tax' => '',
                    'row_total_incl_discount_and_tax' =>  $this->formatPriceTxt('-2'),
                    'discount_rate' => '0%',
                    'price_incl_tax' => ''
                )
            )
        );
    }

}
