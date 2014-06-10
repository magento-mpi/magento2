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
            'store_id' => $product->getStoreId(),
            'label' => $image['label'],
            'position' => $image['position'],
            'disabled' => (bool)$image['disabled'],
            'file' => $image['file']
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
        $actual = (array) $data;
        $this->assertEquals($expected['store_id'], $actual['store_id']);
        $this->assertEquals($expected['label'], $actual['label']);
        $this->assertEquals($expected['position'], $actual['position']);
        $this->assertEquals($expected['file'], $actual['file']);
        $this->assertEquals($expected['disabled'], (bool)$actual['disabled']);
    }
}
