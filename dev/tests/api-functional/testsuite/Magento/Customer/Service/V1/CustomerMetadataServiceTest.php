<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Customer\Service\V1\Data\Address;
use Magento\Customer\Service\V1\Data\Customer;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Customer\Service\V1\Data\Eav\AttributeMetadata;

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
        $this->_markTestAsRestOnly("Should be enabled for SOAP after MAGETWO-27137");
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
            "entityType"    => $entityType,
            "attributeCode" => $attributeCode
        ];

        $attributeMetadata = $this->_webapiCall($serviceInfo, $requestData);
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
                    AttributeMetadata::ATTRIBUTE_CODE   => 'firstname',
                    AttributeMetadata::FRONTEND_INPUT   => 'text',
                    AttributeMetadata::INPUT_FILTER     => '',
                    AttributeMetadata::STORE_LABEL      => 'First Name',
                    AttributeMetadata::VALIDATION_RULES => [
                        0 => ['name' => 'min_text_length', 'value' => 1],
                        1 => ['name' => 'max_text_length', 'value' => 255],
                    ],
                    AttributeMetadata::VISIBLE          => true,
                    AttributeMetadata::REQUIRED         => true,
                    AttributeMetadata::MULTILINE_COUNT  => 0,
                    AttributeMetadata::DATA_MODEL       => '',
                    AttributeMetadata::OPTIONS          => [],
                    AttributeMetadata::FRONTEND_CLASS   => ' required-entry',
                    AttributeMetadata::FRONTEND_LABEL   => 'First Name',
                    AttributeMetadata::NOTE             => '',
                    AttributeMetadata::SYSTEM           => true,
                    AttributeMetadata::USER_DEFINED     => false,
                    AttributeMetadata::BACKEND_TYPE     => 'varchar',
                    AttributeMetadata::SORT_ORDER       => 40
                ]
            ],
            Customer::GENDER => [
                CustomerMetadataServiceInterface::ENTITY_TYPE_CUSTOMER,
                Customer::GENDER,
                [
                    AttributeMetadata::ATTRIBUTE_CODE   => 'gender',
                    AttributeMetadata::FRONTEND_INPUT   => 'select',
                    AttributeMetadata::INPUT_FILTER     => '',
                    AttributeMetadata::STORE_LABEL      => 'Gender',
                    AttributeMetadata::VALIDATION_RULES => [],
                    AttributeMetadata::VISIBLE          => false,
                    AttributeMetadata::REQUIRED         => false,
                    AttributeMetadata::MULTILINE_COUNT  => 0,
                    AttributeMetadata::DATA_MODEL       => '',
                    AttributeMetadata::OPTIONS          => [
                        ['label' => null, 'value' => null],
                        ['label' => 'Male', 'value' => '1'],
                        ['label' => 'Female', 'value' => '2']
                    ],
                    AttributeMetadata::FRONTEND_CLASS   => '',
                    AttributeMetadata::FRONTEND_LABEL   => 'Gender',
                    AttributeMetadata::NOTE             => null,
                    AttributeMetadata::SYSTEM           => false,
                    AttributeMetadata::USER_DEFINED     => false,
                    AttributeMetadata::BACKEND_TYPE     => 'int',
                    AttributeMetadata::SORT_ORDER       => 110
                ]
            ],
            Address::KEY_POSTCODE => [
                CustomerMetadataServiceInterface::ENTITY_TYPE_ADDRESS,
                Address::KEY_POSTCODE,
                [
                    AttributeMetadata::ATTRIBUTE_CODE   => 'postcode',
                    AttributeMetadata::FRONTEND_INPUT   => 'text',
                    AttributeMetadata::INPUT_FILTER     => '',
                    AttributeMetadata::STORE_LABEL      => 'Zip/Postal Code',
                    AttributeMetadata::VALIDATION_RULES => [],
                    AttributeMetadata::VISIBLE          => true,
                    AttributeMetadata::REQUIRED         => true,
                    AttributeMetadata::MULTILINE_COUNT  => 0,
                    AttributeMetadata::DATA_MODEL       => 'Magento\Customer\Model\Attribute\Data\Postcode',
                    AttributeMetadata::OPTIONS          => [],
                    AttributeMetadata::FRONTEND_CLASS   => ' required-entry',
                    AttributeMetadata::FRONTEND_LABEL   => 'Zip/Postal Code',
                    AttributeMetadata::NOTE             => '',
                    AttributeMetadata::SYSTEM           => true,
                    AttributeMetadata::USER_DEFINED     => false,
                    AttributeMetadata::BACKEND_TYPE     => 'varchar',
                    AttributeMetadata::SORT_ORDER       => 110
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
        $this->_markTestAsRestOnly("Should be enabled for SOAP after MAGETWO-27137");

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
        $attributeMetadata = $this->_webApiCall($serviceInfo, $requestData);

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
        $this->_markTestAsRestOnly("Should be enabled for SOAP after MAGETWO-27137");

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/customer/all",
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
                return $array;
            },
            $this->_webApiCall($serviceInfo)
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
        $attributeMetadata = $this->_webApiCall($serviceInfo, $requestData);

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
                'resourcePath' => self::RESOURCE_PATH . "/address/all",
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
                return $array;
            },
            $this->_webApiCall($serviceInfo)
        );

        $this->assertCount(19, $attributeMetadata);

        $postcode = $this->getAttributeMetadataDataProvider()[Address::KEY_POSTCODE][2];
        $this->assertContains($postcode, $attributeMetadata);
    }

    /**
     * Data provider for testGetCustomAddressAttributeMetadata.
     *
     * @return array
     */
    public function getCustomAddressAttributeMetadataDataProvider()
    {
        return [
            [
                [
                    AttributeMetadata::OPTIONS          => [],
                    AttributeMetadata::VALIDATION_RULES => [],
                    AttributeMetadata::ATTRIBUTE_CODE   => 'address_user_attribute',
                    AttributeMetadata::FRONTEND_INPUT   => 'text',
                    AttributeMetadata::INPUT_FILTER     => '',
                    AttributeMetadata::STORE_LABEL      => 'Address user attribute',
                    AttributeMetadata::VISIBLE          => true,
                    AttributeMetadata::REQUIRED         => false,
                    AttributeMetadata::MULTILINE_COUNT  => 1,
                    AttributeMetadata::DATA_MODEL       => '',
                    AttributeMetadata::FRONTEND_CLASS   => '',
                    AttributeMetadata::FRONTEND_LABEL   => 'Address user attribute',
                    AttributeMetadata::NOTE             => '',
                    AttributeMetadata::SYSTEM           => false,
                    AttributeMetadata::USER_DEFINED     => true,
                    AttributeMetadata::BACKEND_TYPE     => 'static',
                    AttributeMetadata::SORT_ORDER       => 2
                ]
            ]
        ];
    }

    /**
     * Test retrieval of address attribute metadata given a specific attribute code.
     *
     * @param array $expectedMetadata The expected metadata for the custom address attribute.
     *
     * @dataProvider getCustomAddressAttributeMetadataDataProvider
     * @magentoApiDataFixture Magento/Customer/_files/attribute_user_defined_address.php
     */
    public  function testGetCustomAddressAttributeMetadata($expectedMetadata)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/address/custom",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerMetadataServiceV1GetCustomAddressAttributeMetadata'
            ]
        ];

        $requestData = [];
        $attributeMetadataList = $this->_webApiCall($serviceInfo, $requestData);
        $addressUserAttribute = [];
        foreach ($attributeMetadataList as $attributeMetadata) {
            if (isset($attributeMetadata['attribute_code'])
                && $attributeMetadata['attribute_code'] == $expectedMetadata['attribute_code']) {

                $addressUserAttribute = $attributeMetadata;
                break;
            }
        }
        $this->assertEquals($expectedMetadata, $addressUserAttribute);
    }

    /**
     * Data provider for testGetCustomCustomerAttributeMetadata.
     *
     * @return array
     */
    public function getCustomCustomerAttributeMetadataDataProvider()
    {
        return [
            [
                [
                    AttributeMetadata::ATTRIBUTE_CODE   => 'user_attribute',
                    AttributeMetadata::FRONTEND_INPUT   => 'text',
                    AttributeMetadata::INPUT_FILTER     => '',
                    AttributeMetadata::STORE_LABEL      => 'frontend_label',
                    AttributeMetadata::VALIDATION_RULES => [],
                    AttributeMetadata::VISIBLE          => true,
                    AttributeMetadata::REQUIRED         => false,
                    AttributeMetadata::MULTILINE_COUNT  => 1,
                    AttributeMetadata::DATA_MODEL       => '',
                    AttributeMetadata::OPTIONS          => [],
                    AttributeMetadata::FRONTEND_CLASS   => '',
                    AttributeMetadata::FRONTEND_LABEL   => 'frontend_label',
                    AttributeMetadata::NOTE             => '',
                    AttributeMetadata::SYSTEM           => false,
                    AttributeMetadata::USER_DEFINED     => true,
                    AttributeMetadata::BACKEND_TYPE     => 'static',
                    AttributeMetadata::SORT_ORDER       => 1221
                ]
            ]
        ];
    }

    /**
     * Test retrieval of address attribute metadata given a specific attribute code.
     *
     * @param array $expectedMetadata The expected metadata for the custom customer attribute.
     *
     * @dataProvider getCustomCustomerAttributeMetadataDataProvider
     * @magentoApiDataFixture Magento/Customer/_files/attribute_user_defined_customer.php
     */
    public  function testGetCustomCustomerAttributeMetadata($expectedMetadata)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/customer/custom",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerMetadataServiceV1GetCustomCustomerAttributeMetadata'
            ]
        ];

        $requestData = [];
        $attributeMetadataList = $this->_webApiCall($serviceInfo, $requestData);
        $customerUserAttribute = [];
        foreach ($attributeMetadataList as $attributeMetadata) {
            if (isset($attributeMetadata['attribute_code'])
                && $attributeMetadata['attribute_code'] == $expectedMetadata['attribute_code']) {

                $customerUserAttribute = $attributeMetadata;
                break;
            }
        }
        $this->assertEquals($expectedMetadata, $customerUserAttribute);
    }

    /**
     * Data provider for testGetAttributes.
     *
     * @return array
     */
    public function getAttributesDataProvider()
    {
        $attributeMetadata = $this->getAttributeMetadataDataProvider();
        return [
            [
                CustomerMetadataServiceInterface::ENTITY_TYPE_CUSTOMER,
                'adminhtml_customer',
                $attributeMetadata[Customer::FIRSTNAME][2]
            ],
            [
                CustomerMetadataServiceInterface::ENTITY_TYPE_CUSTOMER,
                'adminhtml_customer',
                $attributeMetadata[Customer::GENDER][2]
            ],
            [
                CustomerMetadataServiceInterface::ENTITY_TYPE_ADDRESS,
                'adminhtml_customer_address',
                $attributeMetadata[Address::KEY_POSTCODE][2]
            ]
        ];
    }

    /**
     * Test retrieval of attributes
     *
     * @param string $entityType Entity type (customer / customer_address)
     * @param string $formCode Form code
     * @param array $expectedMetadata The expected attribute metadata
     *
     * @dataProvider getAttributesDataProvider
     */
    public function testGetAttributes($entityType, $formCode, $expectedMetadata)
    {
        $this->_markTestAsRestOnly("Should be enabled for SOAP after MAGETWO-27137");

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$entityType/entity/$formCode/form",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerMetadataServiceV1GetAttributes'
            ]
        ];

        $requestData = [];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $requestData['entityType'] = $entityType;
            $requestData['formCode']   = $formCode;
        }

        $attributeMetadataList = $this->_webApiCall($serviceInfo, $requestData);
        foreach ($attributeMetadataList as $attributeMetadata) {
            if(isset($attributeMetadata['attribute_code'])
                && $attributeMetadata['attribute_code'] == $expectedMetadata['attribute_code']) {

                $this->assertEquals($expectedMetadata, $attributeMetadata);
                break;
            }
        }
    }

    /**
     * Data provider for testGetAllAttributeSetMetadata.
     *
     * @return array
     */
    public function getAllAttributeSetMetadataDataProvider()
    {
        $attributeMetadata = $this->getAttributeMetadataDataProvider();
        return [
            [
                CustomerMetadataServiceInterface::ENTITY_TYPE_CUSTOMER,
                0, //Default Attribute Set
                1, //Default Store View. To test Admin - set to 0
                $attributeMetadata[Customer::FIRSTNAME][2]
            ],
            [
                CustomerMetadataServiceInterface::ENTITY_TYPE_CUSTOMER,
                0, //Default Attribute Set
                1, //Default Store View. To test Admin - set to 0
                $attributeMetadata[Customer::GENDER][2]
            ],
            [
                CustomerMetadataServiceInterface::ENTITY_TYPE_ADDRESS,
                0, //Default Attribute Set
                1, //Default Store View. To test Admin - set to 0
                $attributeMetadata[Address::KEY_POSTCODE][2]
            ]
        ];
    }

    /**
     * Test retrieval of attributes from specific attribute set
     *
     * @param string $entityType Entity type (customer / customer_address)
     * @param int $attributeSetId Id of attribute set to retrieve attributes from
     * @param int $storeId Store ID
     * @param array $expectedMetadata The expected attribute metadata
     *
     * @dataProvider getAllAttributeSetMetadataDataProvider
     */
    public function testGetAllAttributeSetMetadata($entityType, $attributeSetId, $storeId, $expectedMetadata)
    {
        $this->_markTestAsRestOnly("Should be enabled for SOAP after MAGETWO-27137");

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$entityType/entity/$attributeSetId/attributeSet/$storeId/store",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerMetadataServiceV1GetAllAttributeSetMetadata'
            ]
        ];

        $requestData = [];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $requestData['entityType']     = $entityType;
            $requestData['attributeSetId'] = $attributeSetId;
            $requestData['storeId']        = $storeId;
        }

        $attributeMetadataList = $this->_webApiCall($serviceInfo, $requestData);

        foreach ($attributeMetadataList as $attributeMetadata) {
            if(isset($attributeMetadata['attribute_code'])
                && $attributeMetadata['attribute_code'] == $expectedMetadata['attribute_code']) {

                $this->assertEquals($expectedMetadata, $attributeMetadata);
                break;
            }
        }
    }
}
