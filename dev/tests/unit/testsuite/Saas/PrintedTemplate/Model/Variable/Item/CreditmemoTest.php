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

class Saas_PrintedTemplate_Model_Variable_Item_CreditmemoTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Get order Item method
     * @dataProvider initVariableProvider
     */
    public function testInitVariable($settings, $expectedValues)
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

        $creditmemo = new Magento_Object();
        $creditmemo->setOrder($order);
        $valueModel->setCreditmemo($creditmemo);

        $creditmemoVariable = new Saas_PrintedTemplate_Model_Variable_Item_FakeCreditmemo($valueModel);

        foreach ($expectedValues as $key => $expectedValue) {
            $this->assertEquals($expectedValue, $creditmemoVariable->getData($key));
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
     */
    public function initVariableProvider()
    {
        return array(
            array(
                array(),
                array(
                    'discount_amount' => $this->formatPriceTxt('0'),
                    'row_total_inc' => $this->formatPriceTxt('0'),
                    'discount_rate' => '0%',
                    'price_incl_tax' => '',
                    'row_total_incl_discount_and_tax' => '',
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
                    'discount_amount' => $this->formatPriceTxt('2'),
                    'row_total_inc' => $this->formatPriceTxt('1'),
                    'discount_rate' => '0%',
                    'price_incl_tax' => '',
                    'row_total_incl_discount_and_tax' => $this->formatPriceTxt('-2')
                )
            ),
            array(
                array(
                    'discount_amount' => 2,
                    'qty' => 1,
                    'weee_tax_applied_row_amount' => 4,
                    'row_total_incl_tax' => 6,
                    'tax_amount' => 3,
                    'row_total' => 10
                ),
                array(
                    'discount_amount' => $this->formatPriceTxt('2'),
                    'row_total_inc' => $this->formatPriceTxt('11'),
                    'discount_rate' => '20%',
                    'price_incl_tax' => $this->formatPriceTxt('17'),
                    'row_total_incl_discount_and_tax' => $this->formatPriceTxt('4')
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
                    'price_incl_tax' => 16
                ),
                array(
                    'discount_amount' => $this->formatPriceTxt('2'),
                    'row_total_inc' => $this->formatPriceTxt('11'),
                    'discount_rate' => '20%',
                    'price_incl_tax' => $this->formatPriceTxt('16'),
                    'row_total_incl_discount_and_tax' => $this->formatPriceTxt('4')
                )
            )
        );
    }

}
