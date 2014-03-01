<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Model\Rule\Action\Discount;

class CartFixedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rule;

    /**
     * @var \Magento\Sales\Model\Quote\Item\AbstractItem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $item;

    /**
     * @var \Magento\SalesRule\Model\Validator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $validator;

    /**
     * @var \Magento\SalesRule\Model\Rule\Action\Discount\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $data;

    /**
     * @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quote;

    /**
     * @var \Magento\Sales\Model\Quote\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $address;

    /**
     * @var CartFixed
     */
    protected $model;

    protected function setUp()
    {
        $this->rule = $this->getMock('Magento\Object', null, [], 'Rule', true);
        $this->item = $this->getMock('Magento\Sales\Model\Quote\Item\AbstractItem', [], [], '', false);
        $this->data = $this->getMock('Magento\SalesRule\Model\Rule\Action\Discount\Data', null);

        $this->quote = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $this->address = $this->getMock('Magento\Sales\Model\Quote\Address',
            ['getCartFixedRules', 'setCartFixedRules', '__wakeup'], [], '', false);
        $this->item->expects($this->any())->method('getQuote')->will($this->returnValue($this->quote));
        $this->item->expects($this->any())->method('getAddress')->will($this->returnValue($this->address));

        $this->validator = $this->getMock('Magento\SalesRule\Model\Validator', [], [], '', false);
        $dataFactory = $this->getMock('Magento\SalesRule\Model\Rule\Action\Discount\DataFactory',
            ['create'], [], '', false);
        $dataFactory->expects($this->any())->method('create')->will($this->returnValue($this->data));
        $this->model = new CartFixed($this->validator, $dataFactory);
    }

    /**
     * @covers \Magento\SalesRule\Model\Rule\Action\Discount\CartFixed::_construct
     * @covers \Magento\SalesRule\Model\Rule\Action\Discount\CartFixed::calculate
     */
    public function testCalculate()
    {
        $this->rule->setData([
            'id' => 1,
            'discount_amount' => 10.0
        ]);

        $this->address->expects($this->any())->method('getCartFixedRules')->will($this->returnValue([]));
        $store = $this->getMock('Magento\Core\Model\Store', [], [], '', false);
        $store->expects($this->atLeastOnce())->method('convertPrice')->will($this->returnArgument(0));
        $store->expects($this->atLeastOnce())->method('roundPrice')->will($this->returnArgument(0));
        $this->quote->expects($this->any())->method('getStore')->will($this->returnValue($store));

        /** validators data */
        $this->validator->expects($this->once())->method('getItemPrice')->with($this->item)
            ->will($this->returnValue(100));
        $this->validator->expects($this->once())->method('getItemBasePrice')->with($this->item)
            ->will($this->returnValue(100));
        $this->validator->expects($this->once())->method('getItemOriginalPrice')->with($this->item)
            ->will($this->returnValue(100));
        $this->validator->expects($this->once())->method('getItemBaseOriginalPrice')->with($this->item)
            ->will($this->returnValue(100));

        $this->address->expects($this->once())->method('setCartFixedRules')->with([1 => 0.0]);
        $this->model->calculate($this->rule, $this->item, 1);

        $this->assertEquals($this->data->getAmount(), 10);
        $this->assertEquals($this->data->getBaseAmount(), 10);
        $this->assertEquals($this->data->getOriginalAmount(), 10);
        $this->assertEquals($this->data->getBaseOriginalAmount(), 100);
    }
}
