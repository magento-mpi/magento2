<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Customer\Service\V1\Data\Customer;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Customer\Service\V1\Data\Eav\AttributeMetadata;

class CustomerMetadataServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = "customerCustomerMetadataServiceV1";
    const SERVICE_VERSION = "V1";
    const RESOURCE_PATH = "/V1/customerAttributeMetadata";

    /**
     * Test retrieval of attribute metadata for the customer entity type.
     *
     * @param string $attributeCode The attribute code of the requested metadata.
     * @param array $expectedMetadata Expected entity metadata for the attribute code.
     * @dataProvider getAttributeMetadataDataProvider
     */
    public function testGetAttributeMetadata($attributeCode, $expectedMetadata)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/$attributeCode",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerMetadataServiceV1GetAttributeMetadata'
            ]
        ];

        $requestData = [
            "attributeCode" => $attributeCode
        ];

        $attributeMetadata = $this->_webapiCall($serviceInfo, $requestData);

        $validationResult = $this->checkValidationRules($expectedMetadata, $attributeMetadata);
        list($expectedMetadata, $attributeMetadata) = $validationResult;
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
                Customer::FIRSTNAME,
                [
                    AttributeMetadata::ATTRIBUTE_CODE   => 'firstname',
                    AttributeMetadata::FRONTEND_INPUT   => 'text',
                    AttributeMetadata::INPUT_FILTER     => '',
                    AttributeMetadata::STORE_LABEL      => 'First Name',
                    AttributeMetadata::VALIDATION_RULES => [
                        ['name' => 'min_text_length', 'value' => 1],
                        ['name' => 'max_text_length', 'value' => 255],
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
            ]
        ];
    }

    /**
     * Test retrieval of all customer attribute metadata.
     */
    public function testGetAllAttributeMetadata()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerMetadataServiceV1GetAllAttributesMetadata'
            ]
        ];

        $attributeMetadata = $this->_webApiCall($serviceInfo);

        $this->assertCount(23, $attributeMetadata);

        $firstName = $this->getAttributeMetadataDataProvider()[Customer::FIRSTNAME][1];
        $validationResult = $this->checkMultipleAttributesValidationRules($firstName, $attributeMetadata);
        list($firstName, $attributeMetadata) = $validationResult;
        $this->assertContains($firstName, $attributeMetadata);
    }

    /**
     * Test retrieval of custom customer attribute metadata.
     */
    public function testGetCustomAttributeMetadata()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/custom',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerMetadataServiceV1GetCustomAttributesMetadata'
            ]
        ];

        $attributeMetadata = $this->_webApiCall($serviceInfo);

        //Default custom attribute code 'disable_auto_group_change'
        $this->assertCount(1, $attributeMetadata);
        $this->assertEquals('disable_auto_group_change', $attributeMetadata[0]['attribute_code']);
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
     * @dataProvider getCustomAddressAttributeMetadataDataProvider
     * @magentoApiDataFixture Magento/Customer/_files/attribute_user_defined_address.php
     */
    public function testGetCustomAddressAttributeMetadata($expectedMetadata)
    {
        $this->markTestSkipped("This test case will be fixed in MAGETWO-27258");
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
        $validationResult = $this->checkValidationRules($expectedMetadata, $addressUserAttribute);
        list($expectedMetadata, $addressUserAttribute) = $validationResult;
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
     * @dataProvider getCustomCustomerAttributeMetadataDataProvider
     * @magentoApiDataFixture Magento/Customer/_files/attribute_user_defined_customer.php
     */
    public function testGetCustomCustomerAttributeMetadata($expectedMetadata)
    {
        $this->markTestSkipped("This test case will be fixed in MAGETWO-27258");
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
        $validationResult = $this->checkValidationRules($expectedMetadata, $customerUserAttribute);
        list($expectedMetadata, $customerUserAttribute) = $validationResult;
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
                $attributeMetadata[Customer::FIRSTNAME][1]
            ],
            [
                CustomerMetadataServiceInterface::ENTITY_TYPE_CUSTOMER,
                'adminhtml_customer',
                $attributeMetadata[Customer::GENDER][1]
            ]
        ];
    }

    /**
     * Test retrieval of attributes
     *
     * @param string $entityType Entity type (customer / customer_address)
     * @param string $formCode Form code
     * @param array $expectedMetadata The expected attribute metadata
     * @dataProvider getAttributesDataProvider
     */
    public function testGetAttributes($entityType, $formCode, $expectedMetadata)
    {
        $this->markTestSkipped("This test case will be fixed in MAGETWO-27258");
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

                $validationResult = $this->checkValidationRules($expectedMetadata, $attributeMetadata);
                list($expectedMetadata, $attributeMetadata) = $validationResult;
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
                $attributeMetadata[Customer::FIRSTNAME][1]
            ],
            [
                CustomerMetadataServiceInterface::ENTITY_TYPE_CUSTOMER,
                0, //Default Attribute Set
                1, //Default Store View. To test Admin - set to 0
                $attributeMetadata[Customer::GENDER][1]
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
     * @dataProvider getAllAttributeSetMetadataDataProvider
     */
    public function testGetAllAttributeSetMetadata($entityType, $attributeSetId, $storeId, $expectedMetadata)
    {
        $this->markTestSkipped("This test case will be fixed in MAGETWO-27258");
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

                $validationResult = $this->checkValidationRules($expectedMetadata, $attributeMetadata);
                list($expectedMetadata, $attributeMetadata) = $validationResult;
                $this->assertEquals($expectedMetadata, $attributeMetadata);
                break;
            }
        }
    }

    /**
     * Checks that expected and actual attribute metadata validation rules are equal
     * and removes the validation rules entry from expected and actual attribute metadata
     *
     * @param array $expectedResult
     * @param array $actualResult
     * @return array
     */
    public function checkValidationRules($expectedResult, $actualResult)
    {
        $expectedRules = [];
        $actualRules   = [];

        if (isset($expectedResult[AttributeMetadata::VALIDATION_RULES])) {
            $expectedRules = $expectedResult[AttributeMetadata::VALIDATION_RULES];
            unset($expectedResult[AttributeMetadata::VALIDATION_RULES]);
        }
        if (isset($actualResult[AttributeMetadata::VALIDATION_RULES])) {
            $actualRules = $actualResult[AttributeMetadata::VALIDATION_RULES];
            unset($actualResult[AttributeMetadata::VALIDATION_RULES]);
        }

        if (is_array($expectedRules) && is_array($actualRules)) {
            foreach($expectedRules as $expectedRule) {
                if (isset($expectedRule['name']) && isset($expectedRule['value'])) {
                    $found = false;
                    foreach($actualRules as $actualRule) {
                        if (isset($actualRule['name']) && isset($actualRule['value'])) {
                            if ($expectedRule['name'] == $actualRule['name']
                                && $expectedRule['value'] == $actualRule['value']
                            ){
                                $found = true;
                                break;
                            }
                        }
                    }
                    $this->assertTrue($found);
                }
            }
        }
        return [$expectedResult, $actualResult];
    }

    /**
     * Check specific attribute validation rules in set of multiple attributes
     *
     * @param array $expectedResult Set of expected attribute metadata
     * @param array $actualResultSet Set of actual attribute metadata
     * @return array
     */
    public function checkMultipleAttributesValidationRules($expectedResult, $actualResultSet)
    {
        if (is_array($expectedResult) && is_array($actualResultSet)) {
            if (isset($expectedResult[AttributeMetadata::ATTRIBUTE_CODE])) {
                foreach($actualResultSet as $actualAttributeKey => $actualAttribute) {
                    if (isset($actualAttribute[AttributeMetadata::ATTRIBUTE_CODE])
                        && $expectedResult[AttributeMetadata::ATTRIBUTE_CODE]
                            == $actualAttribute[AttributeMetadata::ATTRIBUTE_CODE]
                    ) {
                        $this->checkValidationRules($expectedResult, $actualAttribute);
                        unset($actualResultSet[$actualAttributeKey][AttributeMetadata::VALIDATION_RULES]);
                    }
                }
                unset($expectedResult[AttributeMetadata::VALIDATION_RULES]);
            }
        }
        return [$expectedResult, $actualResultSet];
    }
}
