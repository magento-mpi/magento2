<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Catalog\Model\Product;

class PriceModifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\PriceModifier
     */
    protected $priceModifier;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepositoryMock;

    /**
     * @var array
     */
    protected $prices = [];

    protected function setUp()
    {
        $this->productMock =
            $this->getMock('Magento\Catalog\Model\Product',
                ['getData', 'setData', '__wakeup'], [], '', false);
        $this->productRepositoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductRepository',
            [],
            [],
            '',
            false
        );
        $this->priceModifier = new \Magento\Catalog\Model\Product\PriceModifier(
            $this->productRepositoryMock
        );
        $this->prices = [
            0 => [
                'all_groups' => 0,
                'cust_group' => 1,
                'price_qty' => 15,
                'website_id' => 1,
            ],
            1 => [
                'all_groups' => 1,
                'cust_group' => 0,
                'price_qty' => 10,
                'website_id' => 1,
            ],
        ];
    }

    public function testSuccessfullyRemoveGroupPriceSpecifiedForOneGroup()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('group_price')
            ->will($this->returnValue($this->prices));
        $expectedPrices = [1 => $this->prices[1]];
        $this->productMock->expects($this->once())->method('setData')->with('group_price', $expectedPrices);
        $this->productRepositoryMock->expects($this->once())->method('save')->with($this->productMock);
        $this->priceModifier->removeGroupPrice($this->productMock, 1, 1);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedMessage This product doesn't have group price
     */
    public function testRemoveWhenGroupPricesNotExists()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('group_price')
            ->will($this->returnValue([]));
        $this->productMock->expects($this->never())->method('setData');
        $this->productRepositoryMock->expects($this->never())->method('save');
        $this->priceModifier->removeGroupPrice($this->productMock, 1, 1);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedMessage For current  customerGroupId = '10' any group price exist'.
     */
    public function testRemoveGroupPriceForNonExistingCustomerGroup()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('group_price')
            ->will($this->returnValue($this->prices));
        $this->productMock->expects($this->never())->method('setData');
        $this->productRepositoryMock->expects($this->never())->method('save');
        $this->priceModifier->removeGroupPrice($this->productMock, 10, 1);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedMessage This product doesn't have tier price
     */
    public function testRemoveWhenTierPricesNotExists()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('tier_price')
            ->will($this->returnValue([]));
        $this->productMock->expects($this->never())->method('setData');
        $this->productRepositoryMock->expects($this->never())->method('save');
        $this->priceModifier->removeTierPrice($this->productMock, 1, 3, 1);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedMessage For current  customerGroupId = '10' with 'qty' = 15 any tier price exist'.
     */
    public function testRemoveTierPriceForNonExistingCustomerGroup()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('tier_price')
            ->will($this->returnValue($this->prices));
        $this->productMock->expects($this->never())->method('setData');
        $this->productRepositoryMock->expects($this->never())->method('save');
        $this->priceModifier->removeTierPrice($this->productMock, 10, 15, 1);
    }

    public function testSuccessfullyRemoveTierPriceSpecifiedForAllGroups()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('tier_price')
            ->will($this->returnValue($this->prices));
        $expectedPrices = [$this->prices[0]];
        $this->productMock->expects($this->once())->method('setData')->with('tier_price', $expectedPrices);
        $this->productRepositoryMock->expects($this->once())->method('save')->with($this->productMock);
        $this->priceModifier->removeTierPrice($this->productMock, 'all', 10, 1);
    }

    public function testSuccessfullyRemoveTierPriceSpecifiedForSpecificGroups()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('tier_price')
            ->will($this->returnValue($this->prices));
        $expectedPrices = [1 => $this->prices[1]];
        $this->productMock->expects($this->once())->method('setData')->with('tier_price', $expectedPrices);
        $this->productRepositoryMock->expects($this->once())->method('save')->with($this->productMock);
        $this->priceModifier->removeTierPrice($this->productMock, 1, 15, 1);
    }
}
