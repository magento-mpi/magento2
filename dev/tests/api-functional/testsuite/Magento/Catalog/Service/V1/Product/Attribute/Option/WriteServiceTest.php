<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

use Magento\Catalog\Service\V1\Product\Attribute\ReadServiceTest as AttrReadServiceTest;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeOptionWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attributes';

    private static $serviceInfo = [
        'soap' => ['service' => self::SERVICE_NAME, 'serviceVersion' => self::SERVICE_VERSION]
    ];

    /**
     * @magentoApiDataFixture Magento/Catalog/Model/Product/Attribute/_files/select_attribute.php
     */
    public function testAddOption()
    {
        $objectManager = Bootstrap::getObjectManager();
        $testAttributeCode = 'select_attribute';
        $serviceInfo = array_merge_recursive(
            self::$serviceInfo,
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH . '/' . $testAttributeCode . '/options',
                    'httpMethod' => RestConfig::HTTP_METHOD_POST
                ],
                'soap' => ['operation' => self::SERVICE_NAME . 'addOption']
            ]
        );

        /** @var \Magento\Catalog\Service\V1\Data\Eav\OptionBuilder $optionBuilder */
        $optionBuilder = $objectManager->get('\Magento\Catalog\Service\V1\Data\Eav\OptionBuilder');
        /** @var \Magento\Catalog\Service\V1\Data\Eav\Option\LabelBuilder $labelBuilder */
        $labelBuilder = $objectManager->get('\Magento\Catalog\Service\V1\Data\Eav\Option\LabelBuilder');

        $optionBuilder->setLabel('new color');
        $optionBuilder->setDefault(true);
        $optionBuilder->setStoreLabels(
            [
                $labelBuilder->populateWithArray(['label' => 'DE label', 'store_id' => 1])->create(),
            ]
        );

        $response = $this->_webApiCall(
            $serviceInfo,
            [
                'id' => $testAttributeCode,
                'option' => $optionBuilder->create()->__toArray(),
            ]
        );

        $this->assertTrue($response);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/Model/Product/Attribute/_files/select_attribute.php
     */
    public function testRemoveOption()
    {
        $attributeCode = 'select_attribute';
        //get option Id
        $attributeData = $this->getAttributeInfo($attributeCode);
        $this->assertArrayHasKey(1, $attributeData['options']);
        $this->assertNotEmpty($attributeData['options'][1]['value']);
        $optionId = $attributeData['options'][1]['value'];

        $serviceInfo = array_merge_recursive(
            self::$serviceInfo,
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH . '/' . $attributeCode . '/options/' . $optionId,
                    'httpMethod' => RestConfig::HTTP_METHOD_DELETE
                ],
                'soap' => ['operation' => self::SERVICE_NAME . 'removeOption']
            ]
        );
        $this->assertTrue($this->_webApiCall($serviceInfo, ['id' => $attributeCode, 'optionId' => $optionId]));
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
                'resourcePath' => AttrReadServiceTest::RESOURCE_PATH . '/' . $id,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => AttrReadServiceTest::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => AttrReadServiceTest::SERVICE_NAME . 'Info'
            ],
        ];
        return $this->_webApiCall($serviceInfo, array('id' => $id));
    }
}