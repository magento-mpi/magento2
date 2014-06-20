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
use Magento\TestFramework\Helper\Bootstrap;
use\Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata;

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
        return [[$this->getSimpleCategoryData(['name' => 'Test Category Name'])]];
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
        $categoryId = $this->createCategory($category);
        $this->assertGreaterThan(0, $categoryId);

        $category = Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Category');
        $category->setId($categoryId);

        self::setFixture('testCreate.remove.category', $category);
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
        $serviceInfo = array_merge_recursive($this->serviceInfo,
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

    /**
     * @dataProvider updateDataProvider
     * @magentoApiDataFixture Magento/Catalog/_files/category.php
     */
    public function testUpdate($categoryData)
    {
        $this->assertGreaterThan(0, $this->updateCategory(self::$modelId, $categoryData));
        //TODO Validate category data
        /** @var \Magento\Catalog\Service\V1\Category\ReadServiceInterface $readService */
        /*$readService = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\Category\ReadServiceInterface');
        $actualData = $readService->info(self::$modelId);
        $this->assertEmpty(array_diff($categoryData, $actualData))*/
    }

    public function updateDataProvider()
    {
        return array(
            [[AttributeMetadata::NAME => 'Test Update Category']],
            [[AttributeMetadata::ACTIVE => false]],
            [[AttributeMetadata::AVAILABLE_SORT_BY => ['Position', 'Price', 'Name']]],
            [[AttributeMetadata::CUSTOM_DESIGN => 'Test Update Category']],
            [[AttributeMetadata::CUSTOM_APPLY_TO_PRODUCTS => 'Test Update Category']],
            [[AttributeMetadata::CUSTOM_DESIGN_FROM => '']],
            [[AttributeMetadata::CUSTOM_DESIGN_TO => '']],
            [[AttributeMetadata::CUSTOM_LAYOUT_UPDATE => '']],
            [[AttributeMetadata::DEFAULT_SORT_BY => []]],
            [[AttributeMetadata::DESCRIPTION => '']],
            [[AttributeMetadata::DISPLAY_MODE => '']],
            [[AttributeMetadata::ANCHOR => '']],
            [[AttributeMetadata::LANDING_PAGE => '']],
            [[AttributeMetadata::META_DESCRIPTION => '']],
            [[AttributeMetadata::META_KEYWORDS => '']],
            [[AttributeMetadata::META_TITLE => '']],
            [[AttributeMetadata::PAGE_LAYOUT => '']],
            [[AttributeMetadata::URL_KEY => '']],
            [[AttributeMetadata::INCLUDE_IN_MENU => '']],
            [[AttributeMetadata::FILTER_PRICE_RANGE => '']],
            [[AttributeMetadata::CUSTOM_USE_PARENT_SETTINGS => '']],
        );
    }

    /**
     * @param $categoryData
     * @dataProvider updateValidateInputDataProvider
     * @expectedException \Exception
     */
    public function testUpdateValidateInput($categoryData)
    {
        $this->updateCategory(self::$modelId, $categoryData);
    }

    public function updateValidateInputDataProvider()
    {
        return array(
            [[AttributeMetadata::NAME => '']],
            [[AttributeMetadata::NAME => NULL]],
            [[AttributeMetadata::ACTIVE => NULL]],
            [[AttributeMetadata::INCLUDE_IN_MENU => NULL]],
            [[AttributeMetadata::AVAILABLE_SORT_BY => NULL]],
            [[AttributeMetadata::DEFAULT_SORT_BY => 'key1']],
        );
    }

    protected function updateCategory($id, $data)
    {
        $serviceInfo = array_merge_recursive($this->serviceInfo,
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
