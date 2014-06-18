<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Catalog\Model\Category;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;
use SoapFault;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_WRITE_NAME = 'catalogCategoryWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/categories';

    private static $categoryData = ['name' => 'Test Category'];
    private static $modelId;

    public static function deleteDataFixture()
    {
        /** @var \Magento\Catalog\Model\CategoryFactory $categoryFactory */
        $categoryFactory = Bootstrap::getObjectManager()->create('Magento\Catalog\Model\CategoryFactory');
        /** @var Category $category */
        $category = $categoryFactory->create();
        $category->setData(self::$categoryData);
        $category->save();
        self::$modelId = $category->getId();
    }

    /**
     * @magentoApiDataFixture deleteDataFixture
     */
    public function testDelete()
    {
        $this->assertTrue($this->deleteCategory(self::$modelId));
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
     * @return array|bool|float|int|string
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
