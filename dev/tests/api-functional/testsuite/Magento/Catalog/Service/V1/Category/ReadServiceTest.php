<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'catalogCategoryReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/categories';

    private $modelId = 333;

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/category_backend.php
     */
    public function testInfo()
    {
        $expected = [
            'parent_id' => '3',
            'path' => '1/2/3',
            'position' => '1',
            'level' => '2',
            'custom_attributes' => [
                [
                    'attribute_code' => 'children_count',
                    'value' => '0',
                ],
                [
                    'attribute_code' => 'is_active',
                    'value' => '1',
                ],
                [
                    'attribute_code' => 'default_sort_by',
                    'value' => 'name',
                ],
                [
                    'attribute_code' => 'url_path',
                    'value' => 'category-1',
                ],
            ],
            'available_sort_by' => ['position', 'name'],
            'include_in_menu' => '1',
            'name' => 'Category 1',
            'url_key' => 'category-1',
            'category_id' => '333',
            'active' => '1',
            'children' => ['333']
        ];

        $result = $this->getInfoCategory($this->modelId);

        $this->assertArrayHasKey('created_at', $result);
        $this->assertArrayHasKey('updated_at', $result);

        unset($result['created_at'], $result['updated_at']);
        ksort($expected);
        ksort($result);
        $this->assertEquals($expected, $result);
    }

    public function testInfoNoSuchEntityException()
    {
        try {
            $this->getInfoCategory(-1);
        } catch (\Exception $e) {
            $this->assertContains('No such entity with %fieldName = %fieldValue', $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return string
     */
    protected function getInfoCategory($id)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $id,
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'info'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['categoryId' => $id]);
    }
}
