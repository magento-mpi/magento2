<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Service\V1\Data\ProductAttributeTypeBuilder;
use Magento\Catalog\Service\V1\ProductMetadataServiceInterface;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;

class ProductAttributeReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for retrieving product attributes types
     */
    public function testTypes()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $inputtypeFactoryMock = $this->getMock(
            'Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory',
            array('create')
        );
        $inputtypeFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue(
                $objectManager->getObject('Magento\Catalog\Model\Product\Attribute\Source\Inputtype')
            ));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $attributeTypeBuilder = $helper->getObject('\Magento\Catalog\Service\V1\Data\ProductAttributeTypeBuilder');
        $productAttributeReadService = $objectManager->getObject(
            'Magento\Catalog\Service\V1\ProductAttributeReadService',
            [
                'metadataService' => $objectManager->getObject('Magento\Catalog\Service\V1\ProductMetadataService'),
                'inputTypeFactory' => $inputtypeFactoryMock,
                'attributeTypeBuilder' => $attributeTypeBuilder
            ]
        );
        $types = $productAttributeReadService->types();
        $this->assertTrue(is_array($types));
        $this->assertNotEmpty($types);
        $this->assertInstanceOf('Magento\Catalog\Service\V1\Data\ProductAttributeType', current($types));
    }

    /**
     * Test for retrieving product attribute
     */
    public function testInfo()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $attributeCode = 'attr_code';
        $metadataServiceMock = $this->getMock(
            'Magento\Catalog\Service\V1\ProductMetadataService', array('getAttributeMetadata'),
            array(),
            '',
            false
        );
        $metadataServiceMock->expects($this->once())
            ->method('getAttributeMetadata')
            ->with(
                ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT,
                $attributeCode
            );

        /** @var \Magento\Catalog\Service\V1\ProductAttributeReadServiceInterface $service */
        $service = $objectManager->getObject(
            'Magento\Catalog\Service\V1\ProductAttributeReadService',
            array(
               'metadataService' => $metadataServiceMock
            )
        );
        $service->info($attributeCode);
    }

    public function testSearch()
    {
        $attrCollectionMock = $this->getMock('\Magento\Eav\Model\Resource\Entity\Attribute\Collection',
            array(), array(), '', false);
        $attrCollectionMock->expects($this->any())->method('addOrder')->with(
            $this->equalTo('attribute_id'),
            $this->equalTo('DESC')
        );
        $attrCollectionMock->expects($this->once())->method('getTable')->with(
            $this->equalTo('catalog_eav_attribute')
        )->will(
            $this->returnValue('catalog_eav_attribute')
        );
        $attrCollectionMock->expects($this->once())->method('join')->with(
            $this->equalTo(array('additional_table' => 'catalog_eav_attribute')),
            $this->equalTo('main_table.attribute_id = additional_table.attribute_id')
        );
        $attrCollectionMock->expects($this->once())->method('getSize')->will($this->returnValue(1));
        $attrCollectionMock->expects($this->once())->method('addFieldToFilter')->with(
            $this->equalTo(array('frontend_input')),
            $this->equalTo(array(array('eq' => 'text')))
        );

        $attributeData = array(
            'attribute_id' => 1,
            'attribute_code' => 'status',
            'frontend_label' => 'Status',
            'default_value' => '1',
            'is_required' => false,
            'is_user_defined' => false,
            'frontend_input' => 'text'
        );
        // Use magento object for simplicity
        $attributeElement = new \Magento\Framework\Object($attributeData);
        $this->_mockReturnValue(
            $attrCollectionMock,
            array(
                'getSize' => 1,
                '_getItems' => array($attributeElement),
                'getIterator' => new \ArrayIterator(array($attributeElement))
            )
        );

        $attributeBuilderMock = $this->getMock('\Magento\Catalog\Service\V1\Data\Eav\AttributeBuilder',
            array(), array(), '', false);

        $attributeBuilderMock->expects($this->once())
            ->method('setId')
            ->with($attributeData['attribute_id'])
            ->will($this->returnSelf());
        $attributeBuilderMock->expects($this->once())
            ->method('setCode')
            ->with($attributeData['attribute_code'])
            ->will($this->returnSelf());
        $attributeBuilderMock->expects($this->once())
            ->method('setFrontendLabel')
            ->with($attributeData['frontend_label'])
            ->will($this->returnSelf());
        $attributeBuilderMock->expects($this->once())
            ->method('setDefaultValue')
            ->with($attributeData['default_value'])
            ->will($this->returnSelf());
        $attributeBuilderMock->expects($this->once())
            ->method('setIsRequired')
            ->with($attributeData['is_required'])
            ->will($this->returnSelf());
        $attributeBuilderMock->expects($this->once())
            ->method('setIsUserDefined')
            ->with($attributeData['is_user_defined'])
            ->will($this->returnSelf());
        $attributeBuilderMock->expects($this->once())
            ->method('setFrontendInput')
            ->with($attributeData['frontend_input'])
            ->will($this->returnSelf());

        $dataObjectMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Data\Eav\Attribute',
            array(),
            array(),
            '',
            false
        );
        $attributeBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($dataObjectMock));

        $searchResultsBuilderMock = $this->getMockBuilder(
                'Magento\Catalog\Service\V1\Data\Product\Attribute\SearchResultsBuilder'
            )->disableOriginalConstructor()
            ->getMock();

        $searchResultsBuilderMock->expects($this->once())
            ->method('setItems')
            ->with($this->equalTo(array($dataObjectMock)));
        $searchResultsBuilderMock->expects($this->once())
            ->method('create');

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $filterBuilder = $helper->getObject('\Magento\Framework\Service\V1\Data\FilterBuilder');
        $filter = $filterBuilder->setField('frontend_input')->setValue('text')->setConditionType('eq')->create();

        $filterGroupBuilder = $helper->getObject('Magento\Framework\Service\V1\Data\Search\FilterGroupBuilder');
        /** @var SearchCriteriaBuilder $searchBuilder */
        $searchBuilder = $helper->getObject(
            'Magento\Framework\Service\V1\Data\SearchCriteriaBuilder',
            ['filterGroupBuilder' => $filterGroupBuilder]
        );
        $searchBuilder->addFilter([$filter]);
        $searchBuilder->addSortOrder('id', \Magento\Framework\Service\V1\Data\SearchCriteria::SORT_DESC);
        $searchBuilder->setCurrentPage(1);
        $searchBuilder->setPageSize(10);

        $prductAttrService = $helper->getObject(
            'Magento\Catalog\Service\V1\ProductAttributeReadService',
            [
                'searchResultsBuilder' => $searchResultsBuilderMock,
                'attributeCollection' => $attrCollectionMock,
                'attributeBuilder' => $attributeBuilderMock
            ]
        );
        $prductAttrService->search($searchBuilder->create());
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param array $valueMap
     */
    private function _mockReturnValue($mock, $valueMap)
    {
        foreach ($valueMap as $method => $value) {
            $mock->expects($this->any())->method($method)->will($this->returnValue($value));
        }
    }
}
