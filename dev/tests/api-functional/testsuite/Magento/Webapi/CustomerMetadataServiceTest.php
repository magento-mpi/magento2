<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi;

use Magento\Customer\Service\V1\Dto\Address;
use Magento\Customer\Service\V1\Dto\Customer;
use Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata;
use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;
use Magento\TestFramework\TestCase\WebapiAbstract;

class CustomerMetadataServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = "customerCustomerMetadataServiceV1";
    const SERVICE_VERSION = "V1";
    const RESOURCE_PATH = "/V1/customerMetadata";

    /**
     * Test retrieval of attribute metadata for the specified entity type.
     *
     * @param string $entityType Either 'customer' or 'customer_address'.
     * @param string $attributeCode The attribute code of the requested metadata.
     * @param array $expectedMetadata Expected entity metadata for the attribute code.
     *
     * @dataProvider getAttributeMetadataDataProvider
     */
    public function testGetAttributeMetadata($entityType, $attributeCode, $expectedMetadata)
    {
        $this->_markTestAsRestOnly();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$entityType/entity/$attributeCode/attribute",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerMetadataServiceV1GetAttributeMetadata'
            ]
        ];

        $requestData = [
            "entityType" => $entityType,
            "attributeCode" => $attributeCode
        ];

        $attributeMetadata = $this->decamelize($this->_webapiCall($serviceInfo, $requestData));
        $this->assertEquals($expectedMetadata, $attributeMetadata);
    }

    /**
     * Data provider for testGetAttributeMetadata.
     *
     * @return array
     */
    public function getAttributeMetadataDataProvider()
    {
        return [
            Customer::FIRSTNAME => [
                CustomerMetadataServiceInterface::ENTITY_TYPE_CUSTOMER,
                Customer::FIRSTNAME,
                [
                    'attribute_code' => 'firstname',
                    'frontend_input' => 'text',
                    'input_filter' => null,
                    'store_label' => 'First Name',
                    'validation_rules' => [
                        'max_text_length' => ['name' => 'max_text_length', 'value' => 255],
                        'min_text_length' => ['name' => 'min_text_length', 'value' => 1]
                    ],
                    'is_visible' => '1',
                    'is_required' => '1',
                    'multiline_count' => '0',
                    'data_model' => null,
                    'options' => [],
                    'frontend_class' => ' required-entry',
                    'frontend_label' => 'First Name',
                    'note' => null,
                    'is_system' => '1',
                    'is_user_defined' => '0',
                    'sort_order' => '40'
                ]
            ],
            Customer::GENDER => [
                CustomerMetadataServiceInterface::ENTITY_TYPE_CUSTOMER,
                Customer::GENDER,
                [
                    'attribute_code' => 'gender',
                    'frontend_input' => 'select',
                    'input_filter' => null,
                    'store_label' => 'Gender',
                    'validation_rules' => [],
                    'is_visible' => '0',
                    'is_required' => '0',
                    'multiline_count' => '0',
                    'data_model' => null,
                    'options' => [
                        '' => ['label' => null, 'value' => null],
                        'Male' => ['label' => 'Male', 'value' => '1'],
                        'Female' => ['label' => 'Female', 'value' => '2']
                    ],
                    'frontend_class' => '',
                    'frontend_label' => 'Gender',
                    'note' => null,
                    'is_system' => '0',
                    'is_user_defined' => '0',
                    'sort_order' => '110'
                ]
            ],
            Address::KEY_POSTCODE => [
                CustomerMetadataServiceInterface::ENTITY_TYPE_ADDRESS,
                Address::KEY_POSTCODE,
                [
                    'attribute_code' => 'postcode',
                    'frontend_input' => 'text',
                    'input_filter' => null,
                    'store_label' => 'Zip/Postal Code',
                    'validation_rules' => [],
                    'is_visible' => '1',
                    'is_required' => '1',
                    'multiline_count' => '0',
                    'data_model' => 'Magento\Customer\Model\Attribute\Data\Postcode',
                    'options' => [],
                    'frontend_class' => ' required-entry',
                    'frontend_label' => 'Zip/Postal Code',
                    'note' => null,
                    'is_system' => '1',
                    'is_user_defined' => '0',
                    'sort_order' => '110'
                ]
            ]
        ];
    }

    /**
     * Test retrieval of customer attribute metadata given a specific attribute code.
     *
     * @param string $attributeCode The customer metadata attribute code.
     * @param array $expectedMetadata The expected metadata for the specified attribute code.
     *
     * @dataProvider getCustomerAttributeMetadataDataProvider
     */
    public function testGetCustomerAttributeMetadata($attributeCode, $expectedMetadata)
    {
        $this->_markTestAsRestOnly();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/customer/$attributeCode/attribute",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerMetadataServiceV1GetCustomerAttributeMetadata'
            ]
        ];

        $requestData = ['attributeCode' => $attributeCode];
        $attributeMetadata = $this->decamelize($this->_webApiCall($serviceInfo, $requestData));

        $this->assertEquals($expectedMetadata, $attributeMetadata);
    }

    /**
     * Data provider for testGetCustomerAttributeMetadata.
     *
     * @return array
     */
    public function getCustomerAttributeMetadataDataProvider()
    {
        $attributeMetadata = $this->getAttributeMetadataDataProvider();
        return [
            Customer::FIRSTNAME => [Customer::FIRSTNAME, $attributeMetadata[Customer::FIRSTNAME][2]],
            Customer::GENDER => [Customer::GENDER, $attributeMetadata[Customer::GENDER][2]]
        ];
    }

    /**
     * Test retrieval of all customer attribute metadata.
     */
    public function testGetAllCustomerAttributeMetadata()
    {
        $this->_markTestAsRestOnly();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/customer",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerMetadataServiceV1GetAllCustomerAttributeMetadata'
            ]
        ];

        $attributeMetadata = array_map(
            function ($array) {
                return $this->decamelize($array);
            }, $this->_webApiCall($serviceInfo)
        );

        $this->assertCount(23, $attributeMetadata);

        $firstname = $this->getAttributeMetadataDataProvider()[Customer::FIRSTNAME][2];
        $this->assertContains($firstname, $attributeMetadata);
    }

    /**
     * Test retrieval of address attribute metadata given a specific attribute code.
     *
     * @param string $attributeCode The address metadata attribute code.
     * @param array $expectedMetadata The expected metadata for the specified attribute code.
     *
     * @dataProvider getAddressAttributeMetadataDataProvider
     */
    public function testGetAddressAttributeMetadata($attributeCode, $expectedMetadata)
    {
        $this->_markTestAsRestOnly();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/address/$attributeCode/attribute",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerMetadataServiceV1GetAddressAttributeMetadata'
            ]
        ];

        $requestData = ['attributeCode' => $attributeCode];
        $attributeMetadata = $this->decamelize($this->_webApiCall($serviceInfo, $requestData));

        $this->assertEquals($expectedMetadata, $attributeMetadata);
    }

    /**
     * Data provider for testGetCustomerAttributeMetadata.
     *
     * @return array
     */
    public function getAddressAttributeMetadataDataProvider()
    {
        $attributeMetadata = $this->getAttributeMetadataDataProvider();
        return [
            Address::KEY_POSTCODE => [Address::KEY_POSTCODE, $attributeMetadata[Address::KEY_POSTCODE][2]]
        ];
    }

    /**
     * Test retrieval of all customer attribute metadata.
     */
    public function testGetAllAddressAttributeMetadata()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/address",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerMetadataServiceV1GetAllAddressAttributeMetadata'
            ]
        ];

        $attributeMetadata = array_map(
            function ($array) {
                return $this->decamelize($array);
            }, $this->_webApiCall($serviceInfo)
        );

        $this->assertCount(19, $attributeMetadata);

        $postcode = $this->getAttributeMetadataDataProvider()[Address::KEY_POSTCODE][2];
        $this->assertContains($postcode, $attributeMetadata);
    }

    /**
     * Transform an array. Convert all camelCase keys to snake_case.
     *
     * @param array $attributeMetadata An attribute metadata key/value array.
     * @return array The array with all camelCase keys converted to snake_case.
     */
    private function decamelize($attributeMetadata)
    {
        $data = [];
        foreach ($attributeMetadata as $key => $value) {
            $data[strtolower(preg_replace("/(?<=\\w)(?=[A-Z])/", "_$1", $key))] = $value;
        }
        return $data;
    }
}
