<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;

class AttributeOptionManagementInterfaceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'eavApiAttributeOptionManagementInterfaceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attributes';

    public function testGetItems()
    {
        $testAttributeCode = 'quantity_and_stock_status';
        $expectedOptions = [
            [
                AttributeOptionInterface::VALUE => '1',
                AttributeOptionInterface::LABEL => 'In Stock',
            ],
            [
                AttributeOptionInterface::VALUE => '0',
                AttributeOptionInterface::LABEL => 'Out of Stock',
            ]
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $testAttributeCode . '/options',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Options'
            ],
        ];

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $response = $this->_webApiCall($serviceInfo, array('id' => $testAttributeCode));
        } else {
            $response = $this->_webApiCall($serviceInfo);
        }

        $this->assertTrue(is_array($response));
        $this->assertEquals($expectedOptions, $response);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/Model/Product/Attribute/_files/select_attribute.php
     */
    public function testAdd()
    {
        $testAttributeCode = 'select_attribute';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $testAttributeCode . '/options',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'addOption'
            ]
        ];

        $optionData = array (
            AttributeOptionInterface::LABEL => 'new color',
            AttributeOptionInterface::VALUE => 'grey',
            AttributeOptionInterface::SORT_ORDER => 100,
            AttributeOptionInterface::IS_DEFAULT => true,
            AttributeOptionInterface::STORE_LABELS => array (
                array (
                    AttributeOptionLabelInterface::LABEL => 'DE label',
                    AttributeOptionLabelInterface::STORE_ID => 1,
                ),
            ),
        );

        $response = $this->_webApiCall(
            $serviceInfo,
            [
                'attributeCode' => $testAttributeCode,
                'option' => $optionData,
            ]
        );

        $this->assertTrue($response);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/Model/Product/Attribute/_files/select_attribute.php
     */
    public function testDelete()
    {
        $attributeCode = 'select_attribute';
        //get option Id
        $attributeData = $this->getAttributeInfo($attributeCode);
        $this->assertArrayHasKey(1, $attributeData['options']);
        $this->assertNotEmpty($attributeData['options'][1]['value']);
        $optionId = $attributeData['options'][1]['value'];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $attributeCode . '/options/' . $optionId,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'removeOption',
            ]
        ];
        $this->assertTrue($this->_webApiCall(
            $serviceInfo,
            [
                'attributeCode' => $attributeCode,
                'optionId' => $optionId,
            ]
        ));
        $attributeData = $this->getAttributeInfo($attributeCode);
        $this->assertTrue(is_array($attributeData['options']));
        $this->assertArrayNotHasKey(1, $attributeData['options']);
    }

    /**
     * Retrieve attribute info
     *
     * @param  string $id
     * @return mixed
     */
    private function getAttributeInfo($id)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $id,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Info'
            ],
        ];
        return $this->_webApiCall($serviceInfo, array('id' => $id));
    }

}
