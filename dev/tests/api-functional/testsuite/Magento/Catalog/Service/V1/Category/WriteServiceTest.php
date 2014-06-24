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
use Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_WRITE_NAME = 'catalogCategoryWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/categories';

    private $modelId = 333;

    /**
     * @return array
     */
    public function categoryCreationProvider()
    {
        return [[$this->getSimpleCategoryData()]];
    }

    protected function getSimpleCategoryData($categoryData = array())
    {
        return [
            'path' => '2',
            'parent_id' => '2',
            'custom_attributes' => [
                [
                    'attribute_code' => 'name',
                    'value' => isset($categoryData[AttributeMetadata::NAME])
                        ? $categoryData[AttributeMetadata::NAME] : uniqid('Category-', true)
                ],
                ['attribute_code' => 'is_active', 'value' => '0'],
                ['attribute_code' => 'url_key', 'value' => ''],
                ['attribute_code' => 'description', 'value' => ''],
                ['attribute_code' => 'meta_title', 'value' => ''],
                ['attribute_code' => 'meta_keywords', 'value' => ''],
                ['attribute_code' => 'meta_description', 'value' => ''],
                ['attribute_code' => 'include_in_menu', 'value' => '1'],
                ['attribute_code' => 'display_mode', 'value' => 'PRODUCTS'],
                ['attribute_code' => 'landing_page', 'value' => ''],
                ['attribute_code' => 'is_anchor', 'value' => '0'],
                ['attribute_code' => 'custom_use_parent_settings', 'value' => '0'],
                ['attribute_code' => 'custom_apply_to_products', 'value' => '0'],
                ['attribute_code' => 'custom_design', 'value' => ''],
                ['attribute_code' => 'custom_design_from', 'value' => ''],
                ['attribute_code' => 'custom_design_to', 'value' => ''],
                ['attribute_code' => 'page_layout', 'value' => ''],
                ['attribute_code' => 'custom_layout_update', 'value' => ''],
            ]
        ];
    }

    /**
     * Test for create category process
     *
     * @dataProvider categoryCreationProvider
     */
    public function testCreate($category)
    {
        $response = $this->createCategory($category);
        $this->assertTrue($response > 0);
    }

    /**
     * Create category process
     *
     * @param  $category
     * @return int
     */
    protected function createCategory($category)
    {
        $serviceInfo = [
            'rest' => ['resourcePath' => self::RESOURCE_PATH, 'httpMethod' => RestConfig::HTTP_METHOD_POST],
            'soap' => [
                'service' => self::SERVICE_WRITE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_WRITE_NAME . 'create'
            ],
        ];
        $requestData = ['category' => $category];
        return $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/category.php
     */
    public function testDelete()
    {
        $this->assertTrue($this->deleteCategory($this->modelId));
    }

    public function testDeleteNoSuchEntityException()
    {
        try {
            $this->deleteCategory(-1);
        } catch (\Exception $e) {
            $this->assertContains('No such entity with %fieldName = %fieldValue', $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    protected function deleteCategory($id)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $id,
                'httpMethod' => Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_WRITE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_WRITE_NAME . 'delete'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['categoryId' => $id]);
    }
}
