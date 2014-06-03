<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Model\ActionValidator;

class RemoveActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $modelToCheck
     * @param string $protectedModel
     * @param bool $secureArea
     * @param bool $expectedResult
     *
     * @dataProvider isAllowedDataProvider
     * @covers \Magento\Framework\Model\ActionValidator\RemoveAction::isAllowed
     * @covers \Magento\Framework\Model\ActionValidator\RemoveAction::getBaseClassName
     */
    public function testIsAllowed($modelToCheck, $protectedModel, $secureArea, $expectedResult)
    {
        $registryMock = $this->getMock('\Magento\Framework\Registry', array(), array(), '', false);
        $registryMock->expects($this->once())
            ->method('registry')->with('isSecureArea')->will($this->returnValue($secureArea));

        $model = new \Magento\Framework\Model\ActionValidator\RemoveAction(
            $registryMock,
            array('class' => $protectedModel)
        );
        $this->assertEquals($expectedResult, $model->isAllowed($modelToCheck));
    }

    /**
     * return array
     */
    public function isAllowedDataProvider()
    {
        $productMock = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);
        $bannerMock = $this->getMock('\Magento\Wishlist\Model\Wishlist', array(), array(), '', false);

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
