<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

class TypeTransitionManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\TypeTransitionManager
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    protected function setUp()
    {
        $this->model = new TypeTransitionManager(array(
            'simple' => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            'virtual' => \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
        ));
        $this->productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('hasIsVirtual', 'getTypeId', 'setTypeId', 'setTypeInstance', '__wakeup'),
            array(),
            '',
            false
        );
    }

    /**
     * @param bool $isVirtual
     * @param string $currentTypeId
     * @param string $expectedTypeId
     * @dataProvider processProductDataProvider
     */
    public function testProcessProduct($isVirtual, $currentTypeId, $expectedTypeId)
    {
        $this->productMock->expects($this->any())->method('hasIsVirtual')->will($this->returnValue($isVirtual));
        $this->productMock->expects($this->once())->method('getTypeId')->will($this->returnValue($currentTypeId));
        $this->productMock->expects($this->once())->method('setTypeInstance')->with(null);
        $this->productMock->expects($this->once())->method('setTypeId')->with($expectedTypeId);
        $this->model->processProduct($this->productMock);
    }

    /**
     * @return array
     */
    public function processProductDataProvider()
    {
        return array(
            array(
                false,
                \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            ),
            array(
                false,
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            ),
            array(
                true,
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            ),
            array(
                true,
                \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
                \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            ),
        );
    }
}
