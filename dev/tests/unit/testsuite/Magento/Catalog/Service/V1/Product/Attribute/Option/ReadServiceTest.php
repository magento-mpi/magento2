<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

use Magento\Catalog\Service\V1\Product\MetadataServiceInterface as ProductMetadataServiceInterface;
use Magento\TestFramework\Helper\ObjectManager;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for retrieving attribute options
     */
    public function testOptions()
    {
        $objectManager = new ObjectManager($this);

        $attributeCode = 'attr_code';
        $metadataServiceMock = $this->getMock(
            'Magento\Catalog\Service\V1\MetadataService',
            array('getAttributeMetadata'),
            array(),
            '',
            false
        );

        $mock = $this->getMock(
            'Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata',
            array('getOptions'),
            array(),
            '',
            false
        );

        $options = array();
        $mock->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options));

        $metadataServiceMock->expects($this->once())
            ->method('getAttributeMetadata')
            ->with(
                ProductMetadataServiceInterface::ENTITY_TYPE,
                $attributeCode
            )
            ->will($this->returnValue($mock));

        /** @var \Magento\Catalog\Service\V1\Product\Attribute\Option\ReadServiceInterface $service */
        $service = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Product\Attribute\Option\ReadService',
            array(
                'metadataService' => $metadataServiceMock
            )
        );
        $this->assertEquals($options, $service->options($attributeCode));
    }
} 