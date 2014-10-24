<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\DataObjectSerialization;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModuleMSC\Api\Data\ItemDataBuilder;
use Magento\Webapi\Model\DataObjectProcessor;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Webapi\Controller\Rest\Response\DataObjectConverter;

/**
 * Class to test if custom attributes are serialized correctly for the new Module Service Contract approach
 */
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
     * @var \Magento\TestModuleMSC\Api\Data\CustomAttributeNestedDataObjectDataBuilder
     */
    protected $customAttributeNestedDataObjectDataBuilder;

    /**
     * @var \Magento\TestModuleMSC\Api\Data\CustomAttributeDataObjectDataBuilder
     */
    protected $customAttributeDataObjectDataBuilder;

    /**
     * @var DataObjectProcessor $dataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var DataObjectConverter $dataObjectConverter
     */
    protected $dataObjectConverter;

    /**
     * Set up custom attribute related data objects
     */
    protected function setUp()
    {
        $this->_version = 'V1';
        $this->_soapService = 'testModuleMSCAllSoapAndRestV1';
        $this->_restResourcePath = "/{$this->_version}/testmoduleMSC/";

        $this->valueBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\Data\AttributeValueBuilder'
        );

        $this->itemDataBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModuleMSC\Api\Data\ItemDataBuilder'
        );

        $this->customAttributeNestedDataObjectDataBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModuleMSC\Api\Data\CustomAttributeNestedDataObjectDataBuilder'
        );

        $this->customAttributeDataObjectDataBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModuleMSC\Api\Data\CustomAttributeDataObjectDataBuilder'
        );

        $this->dataObjectProcessor = Bootstrap::getObjectManager()->create(
            'Magento\Webapi\Model\DataObjectProcessor'
        );

        $this->dataObjectConverter = Bootstrap::getObjectManager()->create(
            'Magento\Webapi\Controller\Rest\Response\DataObjectConverter'
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

        //\Magento\TestModuleMSC\Api\AllSoapAndRest::itemAnyType just return the input data back as response
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
        $requestData = $this->dataObjectProcessor->buildOutputDataArray($item, get_class($item));
        $result = $this->_webApiCall($serviceInfo, ['entityItem' => $requestData]);

        $expectedResponse = $this->dataObjectConverter->processServiceOutput(
            $item,
            '\Magento\TestModuleMSC\Api\AllSoapAndRestInterface',
            'itemAnyType'
        );
        //\Magento\TestModuleMSC\Api\AllSoapAndRest::itemAnyType just return the input data back as response
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

        $expectedResponse = $this->dataObjectConverter->processServiceOutput(
            $item,
            '\Magento\TestModuleMSC\Api\AllSoapAndRestInterface',
            'getPreconfiguredItem'
        );
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
        $requestData = $this->dataObjectProcessor->buildOutputDataArray(
            $item,
            '\Magento\TestModuleMSC\Api\Data\ItemInterface'
        );
        $result = $this->_webApiCall($serviceInfo, ['entityItem' => $requestData]);

        $expectedResponse = $this->dataObjectConverter->processServiceOutput(
            $item,
            '\Magento\TestModuleMSC\Api\AllSoapAndRestInterface',
            'itemAnyType'
        );
        //\Magento\TestModuleMSC\Api\AllSoapAndRest::itemAnyType just return the input data back as response
        $this->assertEquals($expectedResponse, $result);
    }
}
