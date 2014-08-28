<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to test if custom attributes are serialized correctly
 */
namespace Magento\Webapi\Routing;

use Magento\Framework\Service\DataObjectConverter;
use Magento\Framework\Service\EavDataObjectConverter;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModule1\Service\V1\Entity\ItemBuilder;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class CustomAttributeSerializationTest extends \Magento\Webapi\Routing\BaseService
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
    protected $_soapService = 'testModule1AllSoapAndRest';

    /**
     * @var \Magento\Framework\Service\Data\Eav\AttributeValueBuilder
     */
    protected $valueBuilder;

    /**
     * @var ItemBuilder
     */
    protected $itemBuilder;

    /**
     * @var \Magento\TestModule1\Service\V1\Entity\CustomAttributeNestedDataObjectBuilder
     */
    protected $customAttributeNestedDataObjectBuilder;

    /**
     * @var \Magento\TestModule1\Service\V1\Entity\CustomAttributeDataObjectBuilder
     */
    protected $customAttributeDataObjectBuilder;

    /**
     * @var DataObjectConverter $dataObjectConverter
     */
    protected $dataObjectConverter;

    /**
     * Set up custom attribute related data objects
     */
    protected function setUp()
    {
        $this->_markTestAsRestOnly(
            'Should be removed once Data Objects custom attribute processing for SOAP is implemented in MAGETWO-27314.'
        );

        $this->_version = 'V1';
        $this->_soapService = 'testModule1AllSoapAndRestV1';
        $this->_restResourcePath = "/{$this->_version}/testmodule1/";

        $this->valueBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\Data\Eav\AttributeValueBuilder'
        );

        $this->itemBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModule1\Service\V1\Entity\ItemBuilder'
        );

        $this->customAttributeNestedDataObjectBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModule1\Service\V1\Entity\CustomAttributeNestedDataObjectBuilder'
        );

        $this->customAttributeDataObjectBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModule1\Service\V1\Entity\CustomAttributeDataObjectBuilder'
        );

        $this->dataObjectConverter = Bootstrap::getObjectManager()->create(
          'Magento\Framework\Service\DataObjectConverter'
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
        $result = $this->_webApiCall($serviceInfo, ['item' => $requestData]);

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

        //\Magento\TestModule1\Service\V1\AllSoapAndRest::itemAnyType just return the input data back as response
        $this->assertEquals($expectedResponse, $result);
    }

    public function testDataObjectCustomAttributes()
    {
        $customAttributeDataObject = $this->customAttributeDataObjectBuilder
            ->setName('nameValue')
            ->setCustomAttribute('custom_attribute_int', 1)
            ->create();

        $item = $this->itemBuilder
            ->setItemId(1)
            ->setName('testProductAnyType')
            ->setCustomAttribute('custom_attribute_data_object', $customAttributeDataObject)
            ->setCustomAttribute('custom_attribute_string', 'someStringValue')
            ->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . 'itemAnyType',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'ItemAnyType']
        ];
        $requestData = $item->__toArray();
        $result = $this->_webApiCall($serviceInfo, ['item' => $requestData]);

        $expectedResponse = $this->dataObjectConverter->processServiceOutput($item);
        //\Magento\TestModule1\Service\V1\AllSoapAndRest::itemAnyType just return the input data back as response
        $this->assertEquals($expectedResponse, $result);
    }

    public function testNestedDataObjectCustomAttributes()
    {
        $customAttributeNestedDataObject = $this->customAttributeNestedDataObjectBuilder
            ->setName('nestedNameValue')
            ->create();

        $customAttributeDataObject = $this->customAttributeDataObjectBuilder
            ->setName('nameValue')
            ->setCustomAttribute('custom_attribute_nested', $customAttributeNestedDataObject)
            ->setCustomAttribute('custom_attribute_int', 1)
            ->create();

        $item = $this->itemBuilder
            ->setItemId(1)
            ->setName('testProductAnyType')
            ->setCustomAttribute('custom_attribute_data_object', $customAttributeDataObject)
            ->setCustomAttribute('custom_attribute_string', 'someStringValue')
            ->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . 'itemAnyType',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'ItemAnyType']
        ];
        $requestData = $item->__toArray();
        $result = $this->_webApiCall($serviceInfo, ['item' => $requestData]);

        $expectedResponse = $this->dataObjectConverter->processServiceOutput($item);
        //\Magento\TestModule1\Service\V1\AllSoapAndRest::itemAnyType just return the input data back as response
        $this->assertEquals($expectedResponse, $result);
    }

} 