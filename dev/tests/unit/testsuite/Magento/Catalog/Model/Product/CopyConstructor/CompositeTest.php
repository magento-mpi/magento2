<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Catalog\Model\Product\CopyConstructor;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $factoryMock = $this->getMock(
            '\Magento\Catalog\Model\Product\CopyConstructorFactory',
            [],
            [],
            '',
            false
        );

        $constructorMock = $this->getMock('\Magento\Catalog\Model\Product\CopyConstructorInterface');

        $factoryMock->expects(
            $this->exactly(2)
        )->method(
            'create'
        )->with(
            'constructorInstance'
        )->will(
            $this->returnValue($constructorMock)
        );

        $productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $duplicateMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);

        $constructorMock->expects($this->exactly(2))->method('build')->with($productMock, $duplicateMock);

        $model = new \Magento\Catalog\Model\Product\CopyConstructor\Composite(
            $factoryMock,
            ['constructorInstance', 'constructorInstance']
        );

        $model->build($productMock, $duplicateMock);
    }
}
