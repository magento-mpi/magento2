<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product;

use Magento\TestFramework\Helper\ObjectManager;

class MetadataServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Service\V1\Product\MetadataService
     */
    private $service;

    /**
     * @var \Magento\Catalog\Service\V1\MetadataService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataService;

    /**
     * @var \Magento\Framework\Data\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filter;

    /**
     * @var \Magento\Framework\Data\SearchCriteria|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteria;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchResults|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResult;

    /**
     * @var \Magento\Framework\Service\Config\MetadataConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataConfig;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->metadataConfig = $this->getMockBuilder('Magento\Framework\Service\Config\MetadataConfig')
            ->setMethods(['getCustomAttributesMetadata'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadataService = $this->getMockBuilder('Magento\Catalog\Service\V1\MetadataService')
            ->setMethods(['getAllAttributeMetadata'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchCriteriaBuilder = $this->getMockBuilder('Magento\Framework\Data\SearchCriteriaBuilder')
            ->setMethods(['addFilter', 'create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterBuilder = $this->getMockBuilder('Magento\Framework\Service\V1\Data\FilterBuilder')
            ->setMethods(['setField', 'setValue', 'create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filter = $this->getMockBuilder('Magento\Framework\Service\V1\Data\Filter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchCriteria = $this->getMockBuilder('Magento\Framework\Data\SearchCriteria')
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchResult = $this->getMockBuilder('Magento\Framework\Service\V1\Data\SearchResults')
            ->setMethods(['getItems'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = $helper->getObject(
            'Magento\Catalog\Service\V1\Product\MetadataService',
            [
                'metadataService' => $this->metadataService,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilder,
                'filterBuilder' => $this->filterBuilder,
                'metadataConfig' => $this->metadataConfig
            ]
        );
    }

    public function testGetCustomAttributesMetadata()
    {
        $this->getProductAttributesMetadata(MetadataServiceInterface::DEFAULT_ATTRIBUTE_SET_ID);

        $this->metadataConfig->expects($this->once())->method('getCustomAttributesMetadata')
            ->with($this->equalTo(MetadataServiceInterface::DEFAULT_ATTRIBUTE_SET_ID))
            ->will($this->returnValue([]));

        $result = $this->service->getCustomAttributesMetadata(MetadataServiceInterface::DEFAULT_ATTRIBUTE_SET_ID);

        $this->assertEquals([$this->metadataService], $result);
    }

    public function testGetProductAttributesMetadata()
    {
        $this->getProductAttributesMetadata(MetadataServiceInterface::DEFAULT_ATTRIBUTE_SET_ID);

        $result = $this->service->getProductAttributesMetadata(MetadataServiceInterface::DEFAULT_ATTRIBUTE_SET_ID);

        $this->assertEquals([$this->metadataService], $result);
    }

    private function getProductAttributesMetadata($attributeSetId)
    {
        $this->searchCriteriaBuilder->expects($this->once())->method('addFilter')
            ->with($this->equalTo([$this->filter]));
        $this->searchCriteriaBuilder->expects($this->once())->method('create')
            ->will($this->returnValue($this->searchCriteria));

        $this->filterBuilder->expects($this->once())->method('setField')
            ->with($this->equalTo('attribute_set_id'))
            ->will($this->returnValue($this->filterBuilder));
        $this->filterBuilder->expects($this->once())->method('setValue')
            ->with($this->equalTo($attributeSetId))
            ->will($this->returnValue($this->filterBuilder));
        $this->filterBuilder->expects($this->once())->method('create')
            ->will($this->returnValue($this->filter));

        $this->metadataService->expects($this->once())->method('getAllAttributeMetadata')
            ->with($this->equalTo(MetadataServiceInterface::ENTITY_TYPE), $this->equalTo($this->searchCriteria))
            ->will($this->returnValue($this->searchResult));
        $this->searchResult->expects($this->once())->method('getItems')
            ->will($this->returnValue([$this->metadataService]));
    }
}
