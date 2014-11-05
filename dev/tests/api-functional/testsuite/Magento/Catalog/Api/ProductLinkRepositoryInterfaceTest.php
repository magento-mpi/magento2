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

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/products_related.php
     */
    public function testSave()
    {
        $productSku = 'simple_with_cross';
        $linkType = 'related';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $productSku . '/links/' . $linkType,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save'
            ]
        ];

        /** @var \Magento\Catalog\Model\ProductLink\Management $linkManagement */
        $linkManagement = $this->objectManager->create('\Magento\Catalog\Api\ProductLinkManagementInterface');
        $linkedProducts = $linkManagement->getLinkedItemsByType($productSku, $linkType);

        /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $current */
        $current = current($linkedProducts);
        $this->assertCount(1, $linkedProducts, 'Invalid linked products count');
        //$this->assertEquals(1, $current->getPosition(), 'Invalid product position');

        /** @var \Magento\Catalog\Api\Data\ProductLinkInterfaceDataBuilder $builder */
        $builder = $this->objectManager->get('Magento\Catalog\Api\Data\ProductLinkInterfaceDataBuilder');
        $builder->populateWithArray($current->__toArray())->setPosition(2);
        $updatedLink = $builder->create();

        $this->_webApiCall(
            $serviceInfo,
            ['entity' => $updatedLink->__toArray()]
        );

        $actual = $linkManagement->getLinkedItemsByType($productSku, $linkType);
        $this->assertCount(1, $actual, 'Invalid actual linked products count');
        $this->assertEquals(2, $actual[0]->getPosition());
    }
}
