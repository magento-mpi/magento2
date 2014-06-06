<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Attribute\Media;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class ReadServiceTest extends WebapiAbstract
{
    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testInfo()
    {
        $productSku = 'simple';

        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Model\ProductRepository $repository */
        $repository = $objectManager->get('Magento\Catalog\Model\ProductRepository');
        $product = $repository->get($productSku);
        $image = current($product->getMediaGallery('images'));
        $imageId = $image['value_id'];

        $expected = [
            'file' => $image['file'],
            'label' => $image['label'],
            'position' => $image['position'],
            'disabled' => $image['disabled'],
            'store_id' => $product->getStoreId()
        ];

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/products/' . $productSku . '/media/' . $imageId,
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => 'catalogProductAttributeMediaReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeMediaReadServiceV1Info',
            ),
        );
        $requestData = [
            'productSku' => $productSku,
            'imageId' => $imageId,
        ];
        $data = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($expected, (array) $data);
    }
}
