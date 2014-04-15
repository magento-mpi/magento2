<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pricing;

use Magento\Pricing\Price\Factory as PriceFactory;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\GroupPrice;
use Magento\Catalog\Pricing\Price\SpecialPrice;

/**
 * Test class for \Magento\Pricing\PriceComposite
 */
class PriceCompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PriceComposite
     */
    protected $model;

    /**
     * @var PriceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceFactory;

    /**
     * @var array
     */
    protected $metadata;

    public function setUp()
    {
        $this->priceFactory = $this->getMockBuilder('Magento\Pricing\Price\Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadata = array(
            FinalPrice::PRICE_CODE => ['class' => 'Class\For\FinalPrice'],
            GroupPrice::PRICE_CODE => ['class' => 'Class\For\GroupPrice'],
            SpecialPrice::PRICE_CODE => ['class' => 'Class\For\SpecialPrice']
        );

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Pricing\PriceComposite', array(
            'priceFactory' => $this->priceFactory,
            'metadata' => $this->metadata
        ));
    }

    public function testGetPriceCodes()
    {
        $expectedCodes = [
            FinalPrice::PRICE_CODE,
            GroupPrice::PRICE_CODE,
            SpecialPrice::PRICE_CODE
        ];
        $this->assertEquals($expectedCodes, $this->model->getPriceCodes());
    }

    public function testGetMetadata()
    {
        $this->assertEquals($this->metadata, $this->model->getMetadata());
    }

    public function testCreatePriceObject()
    {
        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $priceCode = FinalPrice::PRICE_CODE;
        $quantity = 2.4;

        $price = $this->getMock('Magento\Pricing\Price\PriceInterface');

        $this->priceFactory->expects($this->once())
            ->method('create')
            ->with($saleable, $this->metadata[$priceCode]['class'], $quantity)
            ->will($this->returnValue($price));

        $this->assertEquals($price, $this->model->createPriceObject($saleable, $priceCode, $quantity));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage wrong_price is not registered in prices list
     */
    public function testCreatePriceObjectWithException()
    {
        $saleable = $this->getMock('Magento\Pricing\Object\SaleableInterface');
        $priceCode = 'wrong_price';
        $quantity = 2.4;

        $this->model->createPriceObject($saleable, $priceCode, $quantity);
    }
}
