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
     * @param null|string $appStateArea
     * @param string $expectedName
     * @dataProvider createDataProvider
     */
    public function testCreate($area, $appStateArea, $expectedName)
    {
        $result = 'result';
        $objectManager = $this->getMockForAbstractClass('Magento\ObjectManager');
        $objectManager
            ->expects($this->once())
            ->method('create')
            ->with($this->equalTo($expectedName))
            ->will($this->returnValue($result));
        $factory = new ConfigFactory($objectManager, $this->getAppState($appStateArea));
        $this->assertEquals($result, $factory->create($area));
    }

    public function createDataProvider()
    {
        return array(
            array(null, 'some area', 'Magento\Core\Model\Translate\Inline\Config'),
            array(
                null,
                \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                'Magento\Backend\Model\Translate\Inline\Config'
            ),
            array('some area', null, 'Magento\Core\Model\Translate\Inline\Config'),
            array(
                \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                null,
                'Magento\Backend\Model\Translate\Inline\Config'
            ),
        );
    }

    /**
     * Get mock of app state
     *
     * @param null|string $area
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAppState($area)
    {
        $appState = $this->getMock('Magento\App\State', array('getAreaCode'), array(), '', false);
        if (isset($area)) {
            $appState->expects($this->once())->method('getAreaCode')->will($this->returnValue($area));
        } else {
            $appState->expects($this->never())->method('getAreaCode');
        }
        return $appState;
    }
}
