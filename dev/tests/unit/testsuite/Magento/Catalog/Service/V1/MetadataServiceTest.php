<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

class MetadataServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\TestFramework\Helper\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    private $objectManager;
    /** @var  \Magento\Catalog\Service\V1\MetadataService|\PHPUnit_Framework_MockObject_MockObject */
    private $metadataService;
    /** @var  \Magento\Framework\Object|\PHPUnit_Framework_MockObject_MockObject */
    private $attributeMock;
    /** @var  \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject */
    private $eavConfigMock;

    /**
     * Initialization
     */
    protected function setUp()
    {
        // eavConfigMock
        $this->eavConfigMock = $this->getMock('Magento\Eav\Model\Config', array('getAttribute'), array(), '', false);
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }
    /**
     * Test getAttributeMetadata
     */
    public function testGetAttributeMetadata()
    {

        $data = array(
            'id' => 1,
            'attribute_id' => 1,
            'attribute_code' => 'description',
            'frontend_label' => 'English',
            'store_labels' => array(1 => 'France'),
            'frontend_input' => 'textarea',
        );

        $this->createAttributeMock($data);
        $this->attributeMock->expects($this->once())->method('isScopeGlobal')->will($this->returnValue(true));
        $this->attributeMock->expects($this->once())->method('usesSource')->will($this->returnValue(true));
        $this->attributeMock->expects($this->once())->method('getSource')
            ->will($this->returnValue(new \Magento\Framework\Object()));

        $this->eavConfigMock
            ->expects($this->once())
            ->method('getAttribute')
            ->will($this->returnValue($this->attributeMock));

        $validationRuleBuilder = $this->objectManager->getObject(
            '\Magento\Catalog\Service\V1\Data\Eav\ValidationRuleBuilder'
        );
        $optionBuilder = $this->objectManager->getObject('\Magento\Catalog\Service\V1\Data\Eav\OptionBuilder');
        $frontendLabelBuilder = $this->objectManager
            ->getObject('\Magento\Catalog\Service\V1\Data\Eav\Product\Attribute\FrontendLabelBuilder');
        /** @var \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder $attrMetadataBuilder */
        $attrMetadataBuilder = $this->objectManager->getObject(
            'Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder',
            [
                'optionBuilder' => $optionBuilder,
                'validationRuleBuilder' => $validationRuleBuilder,
                'frontendLabelBuilder' => $frontendLabelBuilder,
            ]
        );

        // create service
        /** @var \Magento\Catalog\Service\V1\MetadataService $service */
        $this->metadataService = $this->objectManager->getObject(
            'Magento\Catalog\Service\V1\MetadataService',
            array(
                'eavConfig' => $this->eavConfigMock,
                'attributeMetadataBuilder' => $attrMetadataBuilder
            )
        );

        $dto = $this->metadataService->getAttributeMetadata('entity_type', 'attr_code');
        $this->assertInstanceOf('Magento\Framework\Service\Data\AbstractObject', $dto);
        $this->assertEquals($this->attributeMock->getFrontendInput(), $dto->getFrontendInput());

        $this->assertEquals(0, $dto->getFrontendLabel()[0]->getStoreId());
        $this->assertEquals(1, $dto->getFrontendLabel()[1]->getStoreId());
        $this->assertEquals('English', $dto->getFrontendLabel()[0]->getLabel());
        $this->assertEquals('France', $dto->getFrontendLabel()[1]->getLabel());
    }

    /**
     * Test get NoSuchEntityException for attribute withoud id
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with attributeCode = attr_code
     */
    public function testGetAttributeMetadataNoSuchEntityExpection()
    {
        $this->createAttributeMock(array('id' => null));

        // create service
        /** @var \Magento\Catalog\Service\V1\MetadataService $service */
        $this->metadataService = $this->objectManager->getObject(
            'Magento\Catalog\Service\V1\MetadataService',
            array(
                'eavConfig' => $this->eavConfigMock
            )
        );
        $this->eavConfigMock
            ->expects($this->once())
            ->method('getAttribute')
            ->will($this->returnValue($this->attributeMock));
        $this->metadataService->getAttributeMetadata('entity_type', 'attr_code');
    }

    /**
     * @param array $data
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createAttributeMock(array $data)
    {
        $this->attributeMock = $this->getMockBuilder('Magento\Framework\Object')
            ->setMethods(['usesSource', 'getSource', 'isScopeGlobal'])
            ->setConstructorArgs(array('data' => $data))
            ->getMock();
        return $this->attributeMock;
    }
}
