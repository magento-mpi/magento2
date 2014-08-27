<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data;

class CartMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Checkout\Service\V1\Data\CartMapper
     */
    protected $mapper;

    protected function setUp()
    {
        $this->mapper = new \Magento\Checkout\Service\V1\Data\CartMapper();
    }

    public function testMap()
    {
        $methods = ['getId', 'getStoreId', 'getCreatedAt','getUpdatedAt', 'getConvertedAt', 'getIsActive',
            'getIsVirtual', 'getItemsCount', 'getItemsQty', 'getCheckoutMethod', 'getReservedOrderId', 'getOrigOrderId',
            '__wakeUp'];
        $quoteMock = $this->getMock('Magento\Sales\Model\Quote', $methods, [], '', false);
        $expected = [
            Cart::ID => 12,
            Cart::STORE_ID => 1,
            Cart::CREATED_AT => '2014-04-02 12:28:50',
            Cart::UPDATED_AT => '2014-04-02 12:28:50',
            Cart::CONVERTED_AT => '2014-04-02 12:28:50',
            Cart::IS_ACTIVE => true,
            Cart::IS_VIRTUAL => false,
            Cart::ITEMS_COUNT => 10,
            Cart::ITEMS_QUANTITY => 15,
            Cart::CHECKOUT_METHOD => 'check mo',
            Cart::RESERVED_ORDER_ID => 'order_id',
            Cart::ORIG_ORDER_ID => 'orig_order_id'
        ];
        $expectedMethods = [
            'getId' => 12,
            'getStoreId' => 1,
            'getCreatedAt' => '2014-04-02 12:28:50',
            'getUpdatedAt' => '2014-04-02 12:28:50',
            'getConvertedAt' => '2014-04-02 12:28:50',
            'getIsActive' => true,
            'getIsVirtual' => false,
            'getItemsCount' => 10,
            'getItemsQty' => 15,
            'getCheckoutMethod' => 'check mo',
            'getReservedOrderId' => 'order_id',
            'getOrigOrderId' => 'orig_order_id'
        ];
        foreach ($expectedMethods as $method => $value) {
            $quoteMock->expects($this->once())->method($method)->will($this->returnValue($value));
        }

        $this->assertEquals($expected, $this->mapper->map($quoteMock));
    }
}
