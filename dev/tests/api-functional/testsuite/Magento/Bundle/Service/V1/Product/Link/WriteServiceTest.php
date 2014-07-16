<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Product\Link;

use \Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\TestFramework\Helper\Bootstrap;

class WriteServiceTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'bundleProductLinkWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/bundle-products/';

    /**
     * @magentoApiDataFixture Magento/Bundle/_files/product.php
     */
    public function testRemoveChild()
    {
        $productSku = 'bundle-product';
        $childSku = 'simple';
        $optionIds = $this->getProductOptions(3);
        $optionId = array_shift($optionIds);
        $this->assertTrue($this->removeChild($productSku, $optionId, $childSku));
    }

    protected function removeChild($productSku, $optionId, $childSku)
    {
        $resourcePath = self::RESOURCE_PATH . '%s/options/%s/child/%s';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => sprintf($resourcePath, $productSku, $optionId, $childSku),
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'removeChild'
            ]
        ];
        $requestData = array('productSku' => $productSku, 'optionId' => $optionId, 'childSku' => $childSku);
        return $this->_webApiCall($serviceInfo, $requestData);
    }

    protected function getProductOptions($productId)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = Bootstrap::getObjectManager()->get('\Magento\Catalog\Model\Product');
        $product->load($productId);
        /** @var  \Magento\Bundle\Model\Product\Type $type */
        $type = Bootstrap::getObjectManager()->get('\Magento\Bundle\Model\Product\Type');
        return $type->getOptionsIds($product);
    }
}
