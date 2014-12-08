<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Model\Cart\SalesModel;

class QuoteTest extends \Magento\Payment\Model\Cart\SalesModel\QuoteTest
{
    /** @var \Magento\CustomerBalance\Model\Cart\SalesModel\Quote */
    protected $_model;

    /** @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject */
    protected $_quoteMock;

    protected function setUp()
    {
        $this->_quoteMock = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $this->_model = new \Magento\CustomerBalance\Model\Cart\SalesModel\Quote($this->_quoteMock);
    }

    public function testGetDataUsingMethod()
    {
        $this->_quoteMock->expects(
            $this->exactly(2)
        )->method(
            'getDataUsingMethod'
        )->with(
            $this->anything(),
            'any args'
        )->will(
            $this->returnCallback(
                function ($key) {
                    return $key == 'base_customer_bal_amount_used' ? 'customer_balance_amount result' : 'some value';
                }
            )
        );
        $this->assertEquals('some value', $this->_model->getDataUsingMethod('any key', 'any args'));
        $this->assertEquals(
            'customer_balance_amount result',
            $this->_model->getDataUsingMethod('customer_balance_base_amount', 'any args')
        );
    }
}
