<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;
use Magento\TestFramework\Helper\Bootstrap;

class CategoryRepositoryTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/categories';

    private $modelId = 333;

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/category_backend.php
     */
    public function testIGet()
    {
        $expected = [
            'parent_id' => 3,
            'path' => '1/2/3',
            'position' => 1,
            'level' => 2,
            'available_sort_by' => ['position', 'name'],
            'include_in_menu' => true,
            'name' => 'Category 1',
            'url_key' => 'category-1',
            'category_id' => 333,
            'is_active' => true,
            'children' => null
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
                // @todo fix this configuration after SOAP test framework is functional
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['categoryId' => $id]);
    }
    /**
     * @return array
     */
    public function categoryCreationProvider()
    {
        return [
            [
                $this->getSimpleCategoryData(
                    [
                        'name' => 'Test Category Name'
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
                    'attribute_code' => 'name',
                    'value' => "Update Category Test"
                ],
                [
                    'attribute_code' => 'description',
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

    protected function getSimpleCategoryData($categoryData = array())
    {
        return [
            'path' => '2',
            'parent_id' => '2',
            'name' => isset($categoryData['name'])
                ? $categoryData['name'] : uniqid('Category-', true),
            'is_active' => '0',
            'include_in_menu' => 1,
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
                ['attribute_code' => 'page_layout', 'value' => '']
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
            'rest' => ['resourcePath' => self::RESOURCE_PATH, 'httpMethod' => Config::HTTP_METHOD_POST],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
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
        $serviceInfo =
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH . '/' . $id,
                    'httpMethod' => Config::HTTP_METHOD_DELETE
                ],
                'soap' => [
                    // @todo fix this configuration after SOAP test framework is functional
                ]
            ];
        return $this->_webApiCall($serviceInfo, ['categoryId' => $id]);
    }

    protected function updateCategory($id, $data)
    {
        $serviceInfo =
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH . '/' . $id,
                    'httpMethod' => Config::HTTP_METHOD_PUT
                ],
                'soap' => [
                    // @todo fix this configuration after SOAP test framework is functional
                ]
            ];
        return $this->_webApiCall($serviceInfo, ['categoryId' => $id, 'category' => $data]);
    }

}
