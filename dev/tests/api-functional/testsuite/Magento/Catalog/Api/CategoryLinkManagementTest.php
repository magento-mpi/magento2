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
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Webapi\Model\Rest\Config;

class CategoryLinkManagementTest extends WebapiAbstract
{
    const SERVICE_WRITE_NAME = 'catalogCategoryLinksManagementV1';
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
                'sku' => 'simple333',
                'position' => '1',
                'category_id' => '333',
            ],
        ];
        $result = $this->getAssignedProducts($this->modelId);

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
