<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to test if custom attributes are serialized correctly for the new Module Service Contract approach
 */
namespace Magento\Webapi\DataObjectSerialization;

use Magento\Framework\Service\SimpleDataObjectConverter;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModuleMSC\Service\V1\Entity\ItemDataBuilder;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class CustomAttributeSerializationMSCTest extends \Magento\Webapi\Routing\BaseService
{
    /**
     * @var string
     */
    protected $_version;
    /**
     * @var string
     */
    protected $_restResourcePath;
    /**
     * @var string
     */
    protected $_soapService = 'testModuleMSCAllSoapAndRest';

    /**
     * @var \Magento\Framework\Service\Data\AttributeValueBuilder
     */
    protected $valueBuilder;

    /**
     * @var ItemDataBuilder
     */
    protected $itemDataBuilder;

    /**
     * @var \Magento\TestModuleMSC\Service\V1\Entity\CustomAttributeNestedDataObjectDataBuilder
     */
    protected $customAttributeNestedDataObjectDataBuilder;

    /**
     * @var \Magento\TestModuleMSC\Service\V1\Entity\CustomAttributeDataObjectDataBuilder
     */
    protected $customAttributeDataObjectDataBuilder;

    /**
     * @var SimpleDataObjectConverter $dataObjectConverter
     */
    protected $dataObjectConverter;

    /**
     * Set up custom attribute related data objects
     */
    protected function setUp()
    {
        $this->_version = 'V1';
        $this->_soapService = 'testmoduleMSCAllSoapAndRestV1';
        $this->_restResourcePath = "/{$this->_version}/testmoduleMSC/";

        $this->valueBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\Data\AttributeValueBuilder'
        );

        $this->itemDataBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModuleMSC\Service\V1\Entity\ItemDataBuilder'
        );

        $this->customAttributeNestedDataObjectDataBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModuleMSC\Service\V1\Entity\CustomAttributeNestedDataObjectDataBuilder'
        );

        $this->customAttributeDataObjectDataBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModuleMSC\Service\V1\Entity\CustomAttributeDataObjectDataBuilder'
        );

        $this->dataObjectConverter = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\SimpleDataObjectConverter'
        );
    }

    public function testSimpleAndNonExistentCustomAttributes()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . 'itemAnyType',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'ItemAnyType']
        ];
        $requestData = [
            'item_id' => 1,
            'name' => 'testProductAnyType',
            'custom_attributes' =>
                [
                    'non_existent' =>
                        [
                            'attribute_code' => 'non_existent',
                            'value' => 'test'
                        ],
                    'custom_attribute_string' =>
                        [
                            'attribute_code' => 'custom_attribute_string',
                            'value' => 'someStringValue',
                        ],
                ],
        ];
        $result = $this->_webApiCall($serviceInfo, ['entityItem' => $requestData]);

        //The non_existent custom attribute should be dropped since its not a defined custom attribute
        $expectedResponse = [
            'item_id' => 1,
            'name' => 'testProductAnyType',
            'custom_attributes' =>
                [
                    [
                        'attribute_code' => 'custom_attribute_string',
                        'value' => 'someStringValue',
                    ],
                ],
        ];

        //\Magento\TestModuleMSC\Service\V1\AllSoapAndRest::itemAnyType just return the input data back as response
        $this->assertEquals($expectedResponse, $result);
    }

    public function testDataObjectCustomAttributes()
    {
        $customAttributeDataObject = $this->customAttributeDataObjectDataBuilder
            ->setName('nameValue')
            ->setCustomAttribute($this->valueBuilder->setAttributeCode('custom_attribute_int')->setValue(1)->create())
            ->create();

        $customAttributeDataObjectAttributeValue = $this->valueBuilder
            ->setAttributeCode('custom_attribute_data_object')
            ->setValue($customAttributeDataObject)
            ->create();

        $customAttributeStringAttributeValue = $this->valueBuilder
            ->setAttributeCode('custom_attribute_string')
            ->setValue('someStringValue')
            ->create();

        $item = $this->itemDataBuilder
            ->setItemId(1)
            ->setName('testProductAnyType')
            ->setCustomAttribute($customAttributeDataObjectAttributeValue)
            ->setCustomAttribute($customAttributeStringAttributeValue)
            ->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . 'itemAnyType',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'ItemAnyType']
        ];
        $requestData = $item->__toArray();
        $result = $this->_webApiCall($serviceInfo, ['entityItem' => $requestData]);

        $expectedResponse = $this->dataObjectConverter->processServiceOutput($item);
        //\Magento\TestModuleMSC\Service\V1\AllSoapAndRest::itemAnyType just return the input data back as response
        $this->assertEquals($expectedResponse, $result);
    }

    public function testDataObjectCustomAttributesPreconfiguredItem()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . 'itemPreconfigured',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'GetPreconfiguredItem']
        ];

        $result = $this->_webApiCall($serviceInfo, []);

        $customAttributeIntAttributeValue = $this->valueBuilder
            ->setAttributeCode('custom_attribute_int')
            ->setValue(1)
            ->create();

        $customAttributeDataObject = $this->customAttributeDataObjectDataBuilder
            ->setName('nameValue')
            ->setCustomAttribute($customAttributeIntAttributeValue)
            ->create();

        $customAttributeDataObjectAttributeValue = $this->valueBuilder
            ->setAttributeCode('custom_attribute_data_object')
            ->setValue($customAttributeDataObject)
            ->create();

        $customAttributeStringAttributeValue = $this->valueBuilder
            ->setAttributeCode('custom_attribute_string')
            ->setValue('someStringValue')
            ->create();

        $item = $this->itemDataBuilder
            ->setItemId(1)
            ->setName('testProductAnyType')
            ->setCustomAttribute($customAttributeDataObjectAttributeValue)
            ->setCustomAttribute($customAttributeStringAttributeValue)
            ->create();
        $expectedResponse = $this->dataObjectConverter->processServiceOutput($item);
        $this->assertEquals($expectedResponse, $result);
    }

    public function testNestedDataObjectCustomAttributes()
    {
        $customAttributeNestedDataObject = $this->customAttributeNestedDataObjectDataBuilder
            ->setName('nestedNameValue')
            ->create();

        $customAttributeNestedDataObjectAttributeValue = $this->valueBuilder
            ->setAttributeCode('custom_attribute_nested')
            ->setValue($customAttributeNestedDataObject)
            ->create();

        $customAttributeIntAttributeValue = $this->valueBuilder
            ->setAttributeCode('custom_attribute_int')
            ->setValue(1)
            ->create();

        $customAttributeDataObject = $this->customAttributeDataObjectDataBuilder
            ->setName('nameValue')
            ->setCustomAttribute($customAttributeNestedDataObjectAttributeValue)
            ->setCustomAttribute($customAttributeIntAttributeValue)
            ->create();

        $customAttributeDataObjectAttributeValue = $this->valueBuilder
            ->setAttributeCode('custom_attribute_data_object')
            ->setValue($customAttributeDataObject)
            ->create();

        $customAttributeStringAttributeValue = $this->valueBuilder
            ->setAttributeCode('custom_attribute_string')
            ->setValue('someStringValue')
            ->create();

        $item = $this->itemDataBuilder
            ->setItemId(1)
            ->setName('testProductAnyType')
            ->setCustomAttribute($customAttributeDataObjectAttributeValue)
            ->setCustomAttribute($customAttributeStringAttributeValue)
            ->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . 'itemAnyType',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'ItemAnyType']
        ];
        $requestData = $item->__toArray();
        $result = $this->_webApiCall($serviceInfo, ['entityItem' => $requestData]);

        $expectedResponse = $this->dataObjectConverter->processServiceOutput($item);
        //\Magento\TestModuleMSC\Service\V1\AllSoapAndRest::itemAnyType just return the input data back as response
        $this->assertEquals($expectedResponse, $result);
    }
}
