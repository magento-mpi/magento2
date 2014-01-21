<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\CopyConstructor;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $factoryMock = $this->getMock(
            '\Magento\Catalog\Model\Product\CopyConstructorFactory', array(), array(), '', false
        );

        $constructorMock = $this->getMock('\Magento\Catalog\Model\Product\CopyConstructorInterface');

        $factoryMock->expects($this->exactly(2))
            ->method('create')
            ->with('constructorInstance')
            ->will($this->returnValue($constructorMock));

        $productMock = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);
        $duplicateMock = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);

        $constructorMock->expects($this->exactly(2))->method('build')->with($productMock, $duplicateMock);

        $model = new \Magento\Catalog\Model\Product\CopyConstructor\Composite(
            $factoryMock, array('constructorInstance', 'constructorInstance')
        );

        $model->build($productMock, $duplicateMock);
    }
}
