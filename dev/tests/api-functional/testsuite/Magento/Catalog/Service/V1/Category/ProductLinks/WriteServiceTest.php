<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\ProductLinks;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_WRITE_NAME = 'catalogCategoryProductLinksWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH_SUFFIX = '/V1/categories';
    const RESOURCE_PATH_PREFIX = 'products';

    private $categoryId = 333;

    /**
     * @dataProvider assignProductProvider
     * @magentoApiDataFixture Magento/Catalog/_files/products_in_category.php
     *
     * @param int $productId
     * @param string[] $productLink
     * @param int $productPosition
     */
    public function testAssignProduct($productLink, $productId, $productPosition = 0)
    {
        $result = $this->executeRequest(
            $this->categoryId,
            'assignProduct',
            $productLink
        );
        $this->assertTrue($result);
        $this->assertTrue($this->isProductInCategory($this->categoryId, $productId, $productPosition));
    }

    public function assignProductProvider()
    {
        return [
            [
                ['sku' => 'simple_with_cross', 'position' => 7], 334, 7
            ],
            [
                ['sku' => 'simple_with_cross'], 334, 0
            ],
        ];
    }

    /**
     * @dataProvider updateProductProvider
     * @magentoApiDataFixture Magento/Catalog/_files/products_in_category.php
     *
     * @param int $productId
     * @param string[] $productLink
     * @param int $productPosition
     */
    public function testUpdateProduct($productLink, $productId, $productPosition = 0)
    {
        $result = $this->executeRequest(
            $this->categoryId,
            'assignProduct',
            $productLink
        );
        $this->assertTrue($result);
        $this->assertFalse($this->isProductInCategory($this->categoryId, $productId, $productPosition));
    }

    public function updateProductProvider()
    {
        return [
            [
                ['sku' => 'simple_with_cross', 'position' => 7], 333, 4
            ],
            [
                ['sku' => 'simple_with_cross'], 333, 0
            ],
        ];
    }

    /**
     * @param int $categoryId
     * @param string $operationName
     * @param string[] $productLink
     * @return bool
     * @throws \Exception
     */
    private function executeRequest($categoryId, $operationName, $productLink)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH_SUFFIX . '/' . $categoryId . '/' . self::RESOURCE_PATH_PREFIX,
                'httpMethod' => Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_WRITE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_WRITE_NAME . $operationName
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['categoryId' => $categoryId, 'productLink' => $productLink]);
    }

    /**
     * @param int $categoryId
     * @param int $productId
     * @param int $productPosition
     * @return bool
     */
    private function isProductInCategory($categoryId, $productId, $productPosition)
    {
        /** @var \Magento\Catalog\Service\V1\Category\CategoryLoader $categoryLoader */
        $categoryLoader = Bootstrap::getObjectManager()->create('Magento\Catalog\Service\V1\Category\CategoryLoader');
        $category = $categoryLoader->load($categoryId);
        $productsPosition = $category->getProductsPosition();

        if (isset($productsPosition[$productId]) && $productsPosition[$productId] == $productPosition) {
            return true;
        } else {
            return false;
        }
    }
}
