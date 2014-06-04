<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

class ProductAttributeWriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProductAttributeWriteService
     */
    protected $model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Model\Resource\Eav\Attribute | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;

    /**
     * @var \Magento\Eav\Model\Config | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfig;

    /**
     * @var int
     */
    protected $typeId = 4;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->attributeMock = $this->getMock('\Magento\Catalog\Model\Resource\Eav\Attribute', [], [], '', false);

        $this->eavConfig = $this->getMock('\Magento\Eav\Model\Config', [], [], '', false);

        /** @var $model \Magento\Catalog\Service\V1\ProductAttributeWriteService */
        $this->model = $this->objectManager->getObject(
            '\Magento\Catalog\Service\V1\ProductAttributeWriteService',
            ['eavConfig' => $this->eavConfig]);
    }

    /**
     * Test for remove attribute
     */
    public function testRemove()
    {
        $id = 1;
        $this->eavConfig
            ->expects($this->once())
            ->method('getAttribute')
            ->with(ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT, $id)
            ->will($this->returnValue($this->attributeMock));
        $this->attributeMock->expects($this->at(0))->method('delete');
        $this->assertTrue($this->model->remove($id));
    }

    /**
     * Test for remove attribute
     */
    public function testRemoveNoSuchEntityException()
    {
        $id = -1;
        $this->eavConfig
            ->expects($this->once())
            ->method('getAttribute')
            ->with(ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT, $id)
            ->will($this->returnValue(false));
        $this->setExpectedException(
            'Magento\Framework\Exception\NoSuchEntityException',
            "No such entity with attribute_id = $id"
        );
        $this->assertTrue($this->model->remove($id));
    }
}
