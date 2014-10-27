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
            'label' => $image['label'],
            'position' => $image['position'],
            'disabled' => (bool)$image['disabled'],
            'file' => $image['file'],
            'types' => array('image', 'small_image', 'thumbnail'),
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
        $this->assertEquals($expected['label'], $actual['label']);
        $this->assertEquals($expected['position'], $actual['position']);
        $this->assertEquals($expected['file'], $actual['file']);
        $this->assertEquals($expected['types'], $actual['types']);
        $this->assertEquals($expected['disabled'], (bool)$actual['disabled']);
    }

    /**
     * Fetch attribute info set by name
     *
     * @param string $name
     * @return \Magento\Eav\Model\Entity\Attribute\Set
     */
    protected function getAttributeSetByName($name)
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $entityType = $objectManager->create('Magento\Eav\Model\Entity\Type')->loadByCode('catalog_product');

        /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attributeSetCollection */
        $attributeSetCollection = $objectManager->create('\Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection');
        $attributeSetCollection->addFilter('attribute_set_name', $name);
        $attributeSetCollection->addFilter('entity_type_id', $entityType->getId());
        $attributeSetCollection->setOrder('attribute_set_id'); // descending by default
        $attributeSetCollection->setPageSize(1);
        $attributeSetCollection->load();

        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
        $attributeSet = $attributeSetCollection->fetchItem();
        return $attributeSet;
    }

    /**
     * Fetch attribute to check by code
     *
     * @param array $all
     * @param string $attributeCode
     * @return bool
     */
    protected function getAttributeByCode($all, $attributeCode)
    {
        foreach($all as $attribute) {
            if ($attributeCode == $attribute['code']) {
                return $attribute;
            }
        }

        return false;
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/attribute_set_with_image_attribute.php
     */
    public function testTypes()
    {
        $attributeSet = $this->getAttributeSetByName('attribute_set_with_media_attribute'); // from fixture
        $this->assertNotEmpty($attributeSet, 'Fixture failed to create attribute set');

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/media/types/' . $attributeSet->getId(),
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => 'catalogProductAttributeMediaReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeMediaReadServiceV1Types'
            ]
        ];

        $requestData = [
            'attributeSetId' => $attributeSet->getId()
        ];

        $types = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertNotEmpty($types);
        $attribute = $this->getAttributeByCode($types,  'funny_image');

        $this->assertEquals('Funny image', $attribute['frontend_label']);
        $this->assertEquals(1, $attribute['is_user_defined']);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testGetList()
    {
        $productSku = 'simple'; //from fixture
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . urlencode($productSku) . '/media',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => 'catalogProductAttributeMediaReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeMediaReadServiceV1GetList'
            ]
        ];

        $requestData = [
            'productSku' => $productSku
        ];
        $imageList = $this->_webApiCall($serviceInfo, $requestData);

        $image = reset($imageList);
        $this->assertEquals('/m/a/magento_image.jpg', $image['file']);
        $this->assertNotEmpty($image['types']);
        $imageTypes = $image['types'];
        $this->assertContains('image', $imageTypes);
        $this->assertContains('small_image', $imageTypes);
        $this->assertContains('thumbnail', $imageTypes);
    }

    public function testGetListForAbsentSku()
    {
        $productSku = 'absent_sku_' . time();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . urlencode($productSku) . '/media',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => 'catalogProductAttributeMediaReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductAttributeMediaReadServiceV1GetList'
            ]
        ];

        $requestData = [
            'productSku' => $productSku
        ];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->setExpectedException('SoapFault', 'Requested product doesn\'t exist');
        } else {
            $this->setExpectedException('Exception', '', 404);
        }
        $this->_webApiCall($serviceInfo, $requestData);
    }
}
