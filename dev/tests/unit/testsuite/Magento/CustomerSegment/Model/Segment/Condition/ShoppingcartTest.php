<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Model\Segment\Condition;


class ShoppingcartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Shoppingcart
     */
    protected $model;

    /**
     * @var \Magento\Rule\Model\Condition\Context
     */
    protected $context;

    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment
     */
    protected $resourceSegment;

    /**
     * @var \Magento\CustomerSegment\Model\ConditionFactory
     */
    protected $conditionFactory;

    /**
     * @var \Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart\Amount
     */
    protected $cartAmount;

    /**
     * @var \Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart\Itemsquantity
     */
    protected $cartItemsquantity;

    /**
     * @var \Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart\Productsquantity
     */
    protected $cartProductsquantity;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Magento\Rule\Model\Condition\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourceSegment = $this->getMock('Magento\CustomerSegment\Model\Resource\Segment', [], [], '', false);
        $this->conditionFactory = $this->getMock('Magento\CustomerSegment\Model\ConditionFactory', [], [], '', false);

        $this->cartAmount = $this->getMock(
            'Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart\Amount', [], [], '', false
        );
        $this->cartItemsquantity = $this->getMock(
            'Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart\Itemsquantity', [], [], '', false
        );
        $this->cartProductsquantity = $this->getMock(
            'Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart\Productsquantity', [], [], '', false
        );

        $this->model = new Shoppingcart(
            $this->context,
            $this->resourceSegment,
            $this->conditionFactory
        );
    }

    protected function tearDown()
    {
        unset(
            $this->model,
            $this->context,
            $this->resourceSegment,
            $this->conditionFactory,
            $this->cartAmount,
            $this->cartItemsquantity,
            $this->cartProductsquantity
        );
    }

    public function testGetNewChildSelectOptions()
    {
        $amountOptions = ['test_amount_options'];
        $itemsquantityOptions = ['test_itemsquantity_options'];
        $productsquantityOptions = ['test_productsquantity_options'];

        $this->cartAmount
            ->expects($this->once())
            ->method('getNewChildSelectOptions')
            ->will($this->returnValue($amountOptions));

        $this->cartItemsquantity
            ->expects($this->once())
            ->method('getNewChildSelectOptions')
            ->will($this->returnValue($itemsquantityOptions));

        $this->cartProductsquantity
            ->expects($this->once())
            ->method('getNewChildSelectOptions')
            ->will($this->returnValue($productsquantityOptions));

        $this->conditionFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap([
                ['Shoppingcart\Amount', [], $this->cartAmount],
                ['Shoppingcart\Itemsquantity', [], $this->cartItemsquantity],
                ['Shoppingcart\Productsquantity', [], $this->cartProductsquantity],
            ]));

        $result = $this->model->getNewChildSelectOptions();

        $this->assertTrue(is_array($result));
        $this->assertEquals(
            [
                'value' => [
                    $amountOptions,
                    $itemsquantityOptions,
                    $productsquantityOptions
                ],
                'label' => __('Shopping Cart'),
                'available_in_guest_mode' => true
            ],
            $result
        );
    }
}
