<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

class ProductBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Catalog\Service\V1\Data\ProductBuilder|\PHPUnit_Framework_TestCase */
    protected $_productBuilder;

    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $_objectManager;

    /** @var \Magento\Catalog\Service\V1\ProductMetadataService */
    private $_productMetadataService;

    /** @var \Magento\Framework\Service\Data\Eav\AttributeValueBuilder */
    private $_valueBuilder;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        
        $this->_productMetadataService = $this->getMockBuilder(
            'Magento\Catalog\Service\V1\ProductMetadataService'
        )->setMethods(
            array('getCustomAttributesMetadata')
        )->disableOriginalConstructor()->getMock();
        $this->_productMetadataService
            ->expects($this->any())
            ->method('getCustomAttributesMetadata')
            ->will($this->returnValue(
                array(
                    new \Magento\Framework\Object(array('attribute_code' => 'attribute_code_1')),
                    new \Magento\Framework\Object(array('attribute_code' => 'attribute_code_2'))
                )
            )
        );
        $this->_valueBuilder = $this->_objectManager->getObject(
            'Magento\Framework\Service\Data\Eav\AttributeValueBuilder'
        );
        $this->_productBuilder = $this->_objectManager->getObject(
            'Magento\Catalog\Service\V1\Data\ProductBuilder',
            [
                'valueBuilder' => $this->_valueBuilder,
                'metadataService' => $this->_productMetadataService
            ]
        );
    }

    /**
     * @dataProvider setValueDataProvider
     */
    public function testSetValue($method, $value, $getMethod)
    {
        $productData = $this->_productBuilder->$method($value)->create();
        $this->assertEquals($value, $productData->$getMethod());
    }

    public function setValueDataProvider()
    {
        return array(
            ['setId', 100, 'getId'],
            ['setSku', 'product_sku', 'getSku'],
            ['setStoreId', 0, 'getStoreId'],
            ['setPrice', 100.00, 'getPrice'],
            ['setVisibility', 1, 'getVisibility'],
            ['setTypeId', 2, 'getTypeId'],
            ['setCreatedAt', '2014-05-23', 'getCreatedAt'],
            ['setUpdatedAt', '2014-05-25', 'getUpdatedAt'],
            ['setStatus', 2, 'getStatus'],
            ['setWeight', 72.5, 'getWeight']
        );
    }

    public function testGetCustomAttributes()
    {
        $expectedAttributesCodes = ['attribute_code_1', 'attribute_code_2'];
        $this->assertEquals($expectedAttributesCodes, $this->_productBuilder->getCustomAttributesCodes());
    }
}
