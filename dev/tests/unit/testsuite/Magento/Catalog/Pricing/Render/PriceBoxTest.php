<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Render;

/**
 * Class PriceBoxTest
 */
class PriceBoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Pricing\Render\PriceBox
     */
    protected $object;

    /**
     * @var \Magento\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreHelper;

    /**
     * @var \Magento\Math\Random|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mathRandom;


    protected function setUp()
    {
        $this->coreHelper = $this->getMock('Magento\Core\Helper\Data', ['jsonEncode'], [], '', false);
        $this->mathRandom = $this->getMock('Magento\Math\Random', [], [], '', false);

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->object = $objectManager->getObject(
            'Magento\Catalog\Pricing\Render\PriceBox',
            [
                'coreDataHelper' => $this->coreHelper,
                'mathRandom' => $this->mathRandom,
            ]
        );
    }

    public function testJsonEncode()
    {
        $expectedValue = 'string';

        $this->coreHelper->expects($this->once())
            ->method('jsonEncode')
            ->with($this->equalTo($expectedValue))
            ->will($this->returnValue($expectedValue));


        $result = $this->object->jsonEncode($expectedValue);

        $this->assertEquals($expectedValue, $result);
    }

    public function testGetRandomString()
    {
        $expectedValue = 20;

        $expectedTestValue = 'test_value';
        $this->mathRandom->expects($this->once())
            ->method('getRandomString')
            ->with($this->equalTo($expectedValue))
            ->will($this->returnValue('test_value'));


        $result = $this->object->getRandomString($expectedValue);

        $this->assertEquals($expectedTestValue, $result);
    }
}
