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
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $inputValidator;

    /**
     * @var int
     */
    protected $typeId = 4;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->attributeMock = $this->getMock('\Magento\Catalog\Model\Resource\Eav\Attribute', [], [], '', false);
        $attributeFactory =
            $this->getMock('\Magento\Catalog\Model\Resource\Eav\AttributeFactory', ['create'], [], '', false);
        $attributeFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->attributeMock));

        $this->eavConfig = $this->getMock('\Magento\Eav\Model\Config', [], [], '', false);
        $this->inputValidator =
            $this->getMock('\Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator', [], [], '', false);

        /** @var $model \Magento\Catalog\Service\V1\ProductAttributeWriteService */
        $this->model = $this->objectManager->getObject(
            '\Magento\Catalog\Service\V1\ProductAttributeWriteService',
            [
                'eavConfig' => $this->eavConfig,
                'attributeFactory' => $attributeFactory,
                'inputValidator' => $this->inputValidator,
            ]);
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

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate($attrCode)
    {
        $dataMock = $this->getMock('\Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata', [], [], '', false);
        $dataMock->expects($this->any())->method('getFrontendLabel')->will($this->returnValue('label_111'));
        $dataMock->expects($this->any())->method('__toArray')->will($this->returnValue(array()));
        $dataMock->expects($this->any())->method('getAttributeCode')->will($this->returnValue($attrCode));
        $dataMock->expects($this->any())->method('getFrontendInput')->will($this->returnValue('textarea'));
        $this->inputValidator->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $this->attributeMock->expects($this->any())->method('setEntityTypeId')->will($this->returnSelf());
        $this->attributeMock->expects($this->any())->method('save')->will($this->returnSelf());
        $this->eavConfig
            ->expects($this->once())
            ->method('getEntityType')
            ->with(\Magento\Catalog\Model\Product::ENTITY)
            ->will($this->returnValue(new \Magento\Framework\Object()));

        $this->model->create($dataMock);
    }

    public function createDataProvider()
    {
        return array(
            ['code_111'],
            [''] //cover generateCode()
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testCreateEmptyLabel()
    {
        $dataMock = $this->getMock('\Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata', [], [], '', false);
        $dataMock->expects($this->at(0))->method('getFrontendLabel')->will($this->returnValue(false));
        $this->model->create($dataMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testCreateInvalidCode()
    {
        $dataMock = $this->getMock('\Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata', [], [], '', false);
        $dataMock->expects($this->at(0))->method('getFrontendLabel')->will($this->returnValue('label_111'));
        $dataMock->expects($this->at(1))->method('__toArray')->will($this->returnValue(array()));
        $dataMock->expects($this->at(2))->method('getAttributeCode')->will($this->returnValue('111'));
        $this->model->create($dataMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testCreateInvalidInput()
    {
        $dataMock = $this->getMock('\Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata', [], [], '', false);
        $dataMock->expects($this->at(0))->method('getFrontendLabel')->will($this->returnValue('label_111'));
        $dataMock->expects($this->at(1))->method('__toArray')->will($this->returnValue(array()));
        $dataMock->expects($this->at(2))->method('getAttributeCode')->will($this->returnValue('code_111'));
        $dataMock->expects($this->at(3))->method('getFrontendInput')->will($this->returnValue('textarea'));
        $this->inputValidator->expects($this->at(0))->method('isValid')->will($this->returnValue(false));
        $this->model->create($dataMock);
    }
}
