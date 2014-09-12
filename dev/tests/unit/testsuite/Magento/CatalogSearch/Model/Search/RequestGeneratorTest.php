<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Search;

use Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory;

class RequestGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $objectManagerHelper;

    /** @var \Magento\CatalogSearch\Model\Search\RequestGenerator */
    protected $object;

    /** @var  CollectionFactory | \PHPUnit_Framework_MockObject_MockObject */
    protected $productAttributeCollectionFactory;

    public function setUp()
    {
        $this->productAttributeCollectionFactory =
            $this->getMockBuilder('Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory')
                ->setMethods(['create'])
                ->disableOriginalConstructor()
                ->getMock();

        $this->objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->object = $this->objectManagerHelper->getObject(
            'Magento\\CatalogSearch\\Model\\Search\\RequestGenerator',
            ['productAttributeCollectionFactory' => $this->productAttributeCollectionFactory]
        );
    }

    /**
     * Create attribute mock
     *
     * @param string $code
     * @param string $type
     * @param bool $visibleInAdvanced
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createAttributeMock($code, $type, $visibleInAdvanced = true)
    {
        $attribute = $this->getMockBuilder('Magento\Catalog\Model\Resource\Product\Attribute')
            ->disableOriginalConstructor()
            ->setMethods(['getAttributeCode', 'getBackendType', 'getIsVisibleInAdvancedSearch', 'getSearchWeight'])
            ->getMock();
        $attribute->expects($this->any())
            ->method('getAttributeCode')
            ->willReturn($code);
        $attribute->expects($this->any())
            ->method('getBackendType')
            ->willReturn($type);

        $attribute->expects($this->any())
            ->method('getSearchWeight')
            ->willReturn(1);

        $attribute->expects($this->any())
            ->method('getIsVisibleInAdvancedSearch')
            ->willReturn($visibleInAdvanced);
        return $attribute;
    }

    public function testGenerate()
    {
        $collection = $this->getMockBuilder('Magento\Catalog\Model\Resource\Product\Attribute\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $collection->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator(
                [
                    $this->createAttributeMock('sku', 'static'),
                    $this->createAttributeMock('price', 'static'),
                    $this->createAttributeMock('name', 'text'),
                    $this->createAttributeMock('name2', 'text', false),
                    $this->createAttributeMock('date', 'decimal'),
                    $this->createAttributeMock('attr_int', 'int'),
                ]
                ));
        $this->productAttributeCollectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($collection);

        $this->object->generate();
    }
}
