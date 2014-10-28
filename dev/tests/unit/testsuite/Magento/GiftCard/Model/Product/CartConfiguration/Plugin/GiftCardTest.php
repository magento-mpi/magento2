<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Product\CartConfiguration\Plugin;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class ConfigurationTest
 */
class GiftCardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Model\Product\CartConfiguration\Plugin\GiftCard
     */
    protected $model;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\GiftCard\Model\Product\CartConfiguration\Plugin\GiftCard');
    }

    /**
     * @param $productType
     * @param $expected
     * @dataProvider aroundIsProductConfiguredDataProvider
     */
    public function testAroundIsProductConfigured($productType, $expected)
    {
        $config = ['giftcard_amount' => true];

        $subject = $this->getMockBuilder('Magento\Catalog\Model\Product\CartConfiguration')
            ->disableOriginalConstructor()
            ->getMock();

        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $proceed = function (\Magento\Catalog\Model\Product $productParam, array $configParam) use ($product, $config) {
            $this->assertEquals($productParam, $product);
            $this->assertEquals($configParam, $config);
            return false;
        };

        $product->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue($productType));

        $this->assertEquals($expected, $this->model->aroundIsProductConfigured($subject, $proceed, $product, $config));
    }

    /**
     * @return array
     */
    public function aroundIsProductConfiguredDataProvider()
    {
        return [
            ['giftcard', true],
            ['simple', false]
        ];
    }
}
