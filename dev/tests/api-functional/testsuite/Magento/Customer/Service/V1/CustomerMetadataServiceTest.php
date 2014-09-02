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
     *
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
                    'attribute_code' => 'firstname',
                    'frontend_input' => 'text',
                    'input_filter' => null,
                    'store_label' => 'First Name',
                    'validation_rules' => [
                        ['name' => 'max_text_length', 'value' => 255],
                        ['name' => 'min_text_length', 'value' => 1]
                    ],
                    'visible' => '1',
                    'required' => '1',
                    'multiline_count' => '0',
                    'data_model' => null,
                    'options' => [],
                    'frontend_class' => ' required-entry',
                    'frontend_label' => 'First Name',
                    'note' => null,
                    'system' => '1',
                    'user_defined' => 0,
                    'sort_order' => '40',
                    'backend_type' => 'varchar'
                ]
            ],
            Customer::GENDER => [
                Customer::GENDER,
                [
                    'attribute_code' => 'gender',
                    'frontend_input' => 'select',
                    'input_filter' => null,
                    'store_label' => 'Gender',
                    'validation_rules' => [],
                    'visible' => 0,
                    'required' => 0,
                    'multiline_count' => '0',
                    'data_model' => null,
                    'options' => [
                        ['label' => null, 'value' => null],
                        ['label' => 'Male', 'value' => '1'],
                        ['label' => 'Female', 'value' => '2']
                    ],
                    'frontend_class' => '',
                    'frontend_label' => 'Gender',
                    'note' => null,
                    'system' => 0,
                    'user_defined' => 0,
                    'sort_order' => '110',
                    'backend_type' => 'int'
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

        $firstname = $this->getAttributeMetadataDataProvider()[Customer::FIRSTNAME][1];
        $this->assertContains($firstname, $attributeMetadata);
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
}
