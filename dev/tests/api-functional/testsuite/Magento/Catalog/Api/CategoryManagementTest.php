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
use Magento\Catalog\Service\V1\Data\Category as CategoryDataObject;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

class CategoryManagementTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/categories';

    const SERVICE_NAME = 'catalogCategoryWriteServiceV1';

    public function testTree()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ]
        ];
        $result = $this->_webApiCall($serviceInfo);

        $this->assertEquals(0, $result['parent_id']);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/category_tree.php
     * @dataProvider updateMoveDataProvider
     */
    public function testUpdateMove($categoryId, $parentId, $afterId, $expectedPosition)
    {
        $expectedPath = '1/2/400/' . $categoryId;
        $categoryData = ['categoryId' => $categoryId, 'parentId' => $parentId, 'afterId' => $afterId];
        $serviceInfo =
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH . '/' . $categoryId . '/move',
                    'httpMethod' => Config::HTTP_METHOD_PUT
                ],
                'soap' => [
                    // @todo fix this configuration after SOAP test framework is functional
                ]
            ];
        $this->assertTrue($this->_webApiCall($serviceInfo, $categoryData));
        /** @var \Magento\Catalog\Model\Category $model */
        $readService = Bootstrap::getObjectManager()->get('\Magento\Catalog\Api\CategoryRepositoryInterface');
        $model = $readService->get($categoryId);
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
}
