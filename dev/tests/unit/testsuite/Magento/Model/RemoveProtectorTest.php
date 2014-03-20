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
     * @param bool $expectedResult
     *
     * @dataProvider canBeRemovedDataProvider
     * @covers \Magento\Model\RemoveProtector::canBeRemoved
     * @covers \Magento\Model\RemoveProtector::getBaseClassName
     */
    public function testCanBeRemoved($modelToCheck, $protectedModel, $expectedResult)
    {
        $model = new \Magento\Model\RemoveProtector(array('class' => $protectedModel));
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
                'expectedResult' => true
            ),
            array(
                'modelToCheck' => $bannerMock,
                'protectedModel' => get_class($bannerMock),
                'expectedResult' => false
            ),
        );
    }
}
