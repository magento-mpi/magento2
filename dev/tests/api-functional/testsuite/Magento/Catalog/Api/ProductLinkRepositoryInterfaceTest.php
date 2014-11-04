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
     * @magentoApiDataFixture Magento/Catalog/_files/products_related.php
     */
    public function testDelete()
    {
        $this->markTestIncomplete('TBD');
        $productSku = 'simple_with_cross';
        $linkedSku = 'simple';
        $linkType = 'related';

        $actualLinks = $this->getLinkedProducts($productSku, $linkType);
        $this->assertCount(1, $actualLinks, 'fixture seems to be broken');

        $entity = array_shift($actualLinks)->__toArray();
        $this->_webApiCall(
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH . $productSku . '/links/' . $linkType . '/' . $linkedSku,
                    'httpMethod' => RestConfig::HTTP_METHOD_DELETE
                ],
                'soap' => [
                    'service' => self::SERVICE_NAME,
                    'serviceVersion' => self::SERVICE_VERSION,
                    'operation' => self::SERVICE_NAME . 'Remove'
                ]
            ],
            [
                'productSku' => $productSku,
                'linkedProductSku' => $linkedSku,
                'type' => $linkType,
                'entity' => $entity
            ]
        );

        $actualLinks = $this->getLinkedProducts($productSku, $linkType);
        $this->assertEmpty($actualLinks);
    }

    protected function getLinkedProducts($sku, $linkType)
    {
        /** @var \Magento\Catalog\Model\ProductLink\Management $linkManagement */
        $linkManagement = $this->objectManager->get('\Magento\Catalog\Api\ProductLinkManagementInterface');
        $linkedProducts = $linkManagement->getLinkedItemsByType($sku, $linkType);

        return $linkedProducts;
    }



}
