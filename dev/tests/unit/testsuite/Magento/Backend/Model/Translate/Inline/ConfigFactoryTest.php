<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Translate\Inline;

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string|null $area
     * @param string $expectedName
     * @dataProvider createDataProvider
     */
    public function testCreate($area, $expectedName)
    {
        $result = 'result';
        $objectManager = $this->getMockForAbstractClass('Magento\ObjectManager');
        $objectManager
            ->expects($this->once())
            ->method('create')
            ->with($this->equalTo($expectedName))
            ->will($this->returnValue($result));
        $factory = new ConfigFactory($objectManager);
        $this->assertEquals($result, $factory->create($area));
    }

    public function createDataProvider()
    {
        return array(
            array(null, 'Magento\Backend\Model\Translate\Inline\Config'),
            array('some area', 'Magento\Backend\Model\Translate\Inline\Config'),
            array(
                \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                'Magento\Backend\Model\Translate\Inline\Config'
            )
        );
    }
}
