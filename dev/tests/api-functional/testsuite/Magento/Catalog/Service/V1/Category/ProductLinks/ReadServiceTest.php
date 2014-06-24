<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\ProductLinks;

use Magento\Catalog\Model\Category;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_WRITE_NAME = 'catalogCategoryProductLinksReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH_SUFFIX = '/V1/categories';
    const RESOURCE_PATH_PREFIX = 'products';

    private $modelId = 333;

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/category_product.php
     */
    public function testAssignedProducts()
    {
        $expected = [
            [
                'custom_attributes' =>
                    [
                        [
                            'attribute_code' => 'group_price',
                            'value' => [],
                        ],
                        [
                            'attribute_code' => 'tier_price',
                            'value' => [],
                        ],
                        [
                            'attribute_code' => 'required_options',
                            'value' => '0',
                        ],
                        [
                            'attribute_code' => 'has_options',
                            'value' => '0',
                        ],
                        [
                            'attribute_code' => 'category_ids',
                            'value' =>
                                [
                                    0 => '333',
                                ],
                        ],
                    ],
                'sku' => 'simple333',
                'status' => 1,
                'position' => '1',
            ],
        ];
        $result = $this->getAssignedProducts($this->modelId);

        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey('updated_at', $result[0]);
        unset($result[0]['updated_at']);

        $this->assertEquals($expected, $result);
    }

    public function testInfoNoSuchEntityException()
    {
        try {
            $this->getAssignedProducts(-1);
        } catch (\Exception $e) {
            $this->assertContains('No such entity with %fieldName = %fieldValue', $e->getMessage());
        }
    }

    /**
     * @param int $id category id
     * @return string
     */
    protected function getAssignedProducts($id)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH_SUFFIX . '/' . $id . '/' . self::RESOURCE_PATH_PREFIX,
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_WRITE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_WRITE_NAME . 'assignedProducts'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['categoryId' => $id]);
    }
}
