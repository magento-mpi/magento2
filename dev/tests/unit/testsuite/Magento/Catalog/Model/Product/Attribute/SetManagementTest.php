<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute;

class SetManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\SetManagement
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrSetManagementMock;

    protected function setUp()
    {
        $this->attrSetManagementMock = $this->getMock('\Magento\Eav\Api\AttributeSetManagementInterface');
        $this->model = new \Magento\Catalog\Model\Product\Attribute\SetManagement($this->attrSetManagementMock);
    }

    public function testCreate()
    {
        $skeletonId = 1;
        $attributeSetMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $this->attrSetManagementMock->expects($this->once())
            ->method('create')
            ->with(
                \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
                $attributeSetMock,
                $skeletonId
            )->willReturn($attributeSetMock);
        $this->assertEquals($attributeSetMock, $this->model->create($attributeSetMock, $skeletonId));
    }
}
