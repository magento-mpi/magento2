<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Catalog\Model\Category;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_WRITE_NAME = 'catalogCategoryReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/categories';

    private $modelId = 333;

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/category.php
     */
    public function testInfo()
    {
        $expected = [
            'custom_attributes' => [
                [
                    'attribute_code' => 'available_sort_by',
                    'value' => '',
                ],
                [
                    'attribute_code' => 'is_active',
                    'value' => '1',
                ],
                [
                    'attribute_code' => 'include_in_menu',
                    'value' => '1',
                ],
                [
                    'attribute_code' => 'name',
                    'value' => 'Category 1',
                ],
                [
                    'attribute_code' => 'default_sort_by',
                    'value' => 'name',
                ],
                [
                    'attribute_code' => 'url_key',
                    'value' => 'category-1',
                ],
                [
                    'attribute_code' => 'url_path',
                    'value' => 'category-1.html',
                ],
            ],
        ];

        $this->assertEquals($expected, $this->getInfoCategory($this->modelId));
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
                'httpMethod' => Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_WRITE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_WRITE_NAME . 'info'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['categoryId' => $id]);
    }
}
