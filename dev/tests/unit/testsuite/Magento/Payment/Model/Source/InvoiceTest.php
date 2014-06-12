<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Source;

class InvoiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Invoice
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Invoice();
    }

    public function testToOptionArray()
    {
        $expectedResult = array(
            array(
                'value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Yes')
            ),
            array('value' => '', 'label' => __('No'))
        );

        $this->assertEquals($expectedResult, $this->_model->toOptionArray());
    }
}
