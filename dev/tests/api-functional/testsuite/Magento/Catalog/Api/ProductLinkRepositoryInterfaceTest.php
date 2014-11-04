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
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\TestFramework\Helper\Bootstrap;

class ProductLinkRepositoryInterfaceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductLinkRepositoryInterfaceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/products_related_multiple.php
     */
    public function testDelete()
    {
        $productSku = 'simple_with_cross';
        $linkedSku = 'simple';
        $linkType = 'related';
        $this->_webApiCall(
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH . $productSku . '/links/' . $linkType . '/' . $linkedSku,
                    'httpMethod' => RestConfig::HTTP_METHOD_DELETE
                ]
            ]
        );
        /** @var \Magento\Catalog\Model\ProductLink\Management $linkManagement */
        $linkManagement = $this->objectManager->create('\Magento\Catalog\Api\ProductLinkManagementInterface');
        $linkedProducts = $linkManagement->getLinkedItemsByType($productSku, $linkType);
        $this->assertCount(1, $linkedProducts);
        /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $product */
        $product = current($linkedProducts);
        $this->assertEquals($product->getLinkedProductSku(), 'simple_with_cross_two');
    }
}
