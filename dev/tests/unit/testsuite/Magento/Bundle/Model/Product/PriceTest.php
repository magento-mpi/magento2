<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Product;

class PriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\Bundle\Model\Product\Price
     */
    protected $model;

    protected function setUp()
    {
        $this->ruleFactoryMock = $this->getMock(
            '\Magento\CatalogRule\Model\Resource\RuleFactory', array(), array(), '', false
        );
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->localeDateMock = $this->getMock('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', array(), array(), '', false);
        $this->eventManagerMock = $this->getMock('\Magento\Framework\Event\ManagerInterface');
        $this->taxHelperMock = $this->getMock('\Magento\Tax\Helper\Data', array(), array(), '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', array(), array(), '', false);

        $this->model = new \Magento\Bundle\Model\Product\Price(
            $this->ruleFactoryMock,
            $this->storeManagerMock,
            $this->localeDateMock,
            $this->customerSessionMock,
            $this->eventManagerMock,
            $this->taxHelperMock
        );
    }

    /**
     * @param float $finalPrice
     * @param float $specialPrice
     * @param int $callsNumber
     * @param bool $dateInInterval
     * @param float $expected
     *
     * @covers \Magento\Bundle\Model\Product\Price::calculateSpecialPrice
     * @covers \Magento\Bundle\Model\Product\Price::__construct
     * @dataProvider calculateSpecialPrice
     */
    public function testCalculateSpecialPrice($finalPrice, $specialPrice, $callsNumber, $dateInInterval, $expected)
    {
        $this->localeDateMock->expects($this->exactly($callsNumber))
            ->method('isScopeDateInInterval')->will($this->returnValue($dateInInterval));

        $this->storeManagerMock->expects($this->any())
            ->method('getStore')->will($this->returnValue($this->storeMock));

        $this->storeMock->expects($this->any())
            ->method('roundPrice')->will($this->returnArgument(0));

        $this->assertEquals(
            $expected,
            $this->model->calculateSpecialPrice($finalPrice, $specialPrice, date('Y-m-d'), date('Y-m-d'))
        );
    }

    /**
     * @return array
     */
    public function calculateSpecialPrice()
    {
        return array(
            array(10, null, 0, true, 10),
            array(10, false, 0, true, 10),
            array(10, 50, 1, false, 10),
            array(10, 50, 1, true, 5),
            array(0, 50, 1, true, 0),
            array(10, 100, 1, true, 10),
        );
    }
}
