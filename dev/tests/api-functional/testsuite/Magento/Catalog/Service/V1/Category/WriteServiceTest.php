<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Catalog\Service\V1\Data\Category as CategoryDataObject;
use Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Webapi\Model\Rest\Config;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogCategoryWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/categories';

    private $serviceInfo = ['soap' => ['service' => self::SERVICE_NAME, 'serviceVersion' => self::SERVICE_VERSION]];
    private $modelId = 333;

    /**
     * @return array
     */
    public function categoryCreationProvider()
    {
        return [
            [
                $this->getSimpleCategoryData(
                    [
                        AttributeMetadata::NAME => 'Test Category Name'
                    ]
                )
            ]
        ];
    }

    /**
     * Test for create category process
     *
     * @magentoApiDataFixture Magento/Catalog/Model/Category/_files/service_category_create.php
     * @dataProvider categoryCreationProvider
     */
    public function testCreate($category)
    {
        $categoryId = $this->createCategory($category);
        $this->assertGreaterThan(0, $categoryId);
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
     * @dataProvider deleteSystemOrRootDataProvider
     * @expectedException \Exception
     */
    public function testDeleteSystemOrRoot()
    {
        $this->deleteCategory($this->modelId);
    }

    public function deleteSystemOrRootDataProvider()
    {
        return array(
            [\Magento\Catalog\Model\Category::TREE_ROOT_ID],
            [2] //Default root category
        );
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/category.php
     */
    public function testUpdate()
    {
        $categoryId = 333;
        $categoryData = [
            'custom_attributes' => [
                [
                    'attribute_code' => AttributeMetadata::NAME,
                    'value' => "Update Category Test"
                ],
                [
                    'attribute_code' => AttributeMetadata::DESCRIPTION,
                    'value' => "Update Category Description Test"
                ]
            ]
        ];
        $this->assertTrue($this->updateCategory($categoryId, $categoryData));
        /** @var \Magento\Catalog\Model\Category $model */
        $model = Bootstrap::getObjectManager()->get('\Magento\Catalog\Model\Category');
        $model->load($categoryId);
        foreach ($categoryData['custom_attributes'] as $attribute) {
            $this->assertEquals($attribute['value'], $model->getData($attribute['attribute_code']));
        }
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/category_tree.php
     * @dataProvider updateMoveDataProvider
     */
    public function testUpdateMove($categoryId, $parentId, $afterId, $expectedPosition)
    {
        $expectedPath = '1/2/400/' . $categoryId;
        $categoryData = ['categoryId' => $categoryId, 'parentId' => $parentId, 'afterId' => $afterId];
        $serviceInfo = array_merge_recursive(
            $this->serviceInfo,
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH . '/' . $categoryId . '/move',
                    'httpMethod' => Config::HTTP_METHOD_PUT
                ],
                'soap' => ['operation' => self::SERVICE_NAME . 'move']
            ]
        );
        $this->assertTrue($this->_webApiCall($serviceInfo, $categoryData));
        /** @var \Magento\Catalog\Model\Category $model */
        $readService = Bootstrap::getObjectManager()->get('\Magento\Catalog\Service\V1\Category\ReadService');
        $model = $readService->info($categoryId);
        $this->assertEquals($expectedPath, $model->getPath());
        $this->assertEquals($expectedPosition, $model->getPosition());
        $this->assertEquals($parentId, $model->getParentId());
    }

    public function updateMoveDataProvider()
    {
        return array(
            [402, 400, null, 2],
            [402, 400, 401, 2],
            [402, 400, 999, 2],
            [402, 400, 0, 1]
        );
    }

    protected function getSimpleCategoryData($categoryData = array())
    {
        return [
            'path' => '2',
            'parent_id' => '2',
            'name' => isset($categoryData[AttributeMetadata::NAME])
                    ? $categoryData[AttributeMetadata::NAME] : uniqid('Category-', true),
            'active' => '0',
            'custom_attributes' => [
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
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'create'
            ],
        ];
        $requestData = ['category' => $category];
        return $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    protected function deleteCategory($id)
    {
        $serviceInfo = array_merge_recursive(
            $this->serviceInfo,
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH . '/' . $id,
                    'httpMethod' => Config::HTTP_METHOD_DELETE
                ],
                'soap' => ['operation' => self::SERVICE_NAME . 'delete']
            ]
        );
        return $this->_webApiCall($serviceInfo, ['categoryId' => $id]);
    }

    protected function updateCategory($id, $data)
    {
        $serviceInfo = array_merge_recursive(
            $this->serviceInfo,
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH . '/' . $id,
                    'httpMethod' => Config::HTTP_METHOD_PUT
                ],
                'soap' => ['operation' => self::SERVICE_NAME . 'update']
            ]
        );
        return $this->_webApiCall($serviceInfo, ['categoryId' => $id, 'category' => $data]);
    }
}
