<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Model;

class RemoveProtectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $modelToCheck
     * @param string $protectedModel
     * @param bool $secureArea
     * @param bool $expectedResult
     *
     * @dataProvider canBeRemovedDataProvider
     * @covers \Magento\Model\RemoveProtector::canBeRemoved
     * @covers \Magento\Model\RemoveProtector::getBaseClassName
     */
    public function testCanBeRemoved($modelToCheck, $protectedModel, $secureArea, $expectedResult)
    {
        $registryMock = $this->getMock('\Magento\Registry', array(), array(), '', false);
        $registryMock->expects($this->once())
            ->method('registry')->with('isSecureArea')->will($this->returnValue($secureArea));

        $model = new \Magento\Model\RemoveProtector($registryMock, array('class' => $protectedModel));
        $this->assertEquals($expectedResult, $model->canBeRemoved($modelToCheck));
    }

    /**
     * return array
     */
    public function canBeRemovedDataProvider()
    {
        $productMock = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);
        $bannerMock = $this->getMock('\Magento\Banner\Model\Banner', array(), array(), '', false);

        return array(
            array(
                'modelToCheck' => $productMock,
                'protectedModel' => 'Model',
                'secureArea' => false,
                'expectedResult' => true
            ),
            array(
                'modelToCheck' => $bannerMock,
                'protectedModel' => get_class($bannerMock),
                'secureArea' => false,
                'expectedResult' => false
            ),
            array(
                'modelToCheck' => $bannerMock,
                'protectedModel' => get_class($bannerMock),
                'secureArea' => true,
                'expectedResult' => true
            ),
        );
    }
}
