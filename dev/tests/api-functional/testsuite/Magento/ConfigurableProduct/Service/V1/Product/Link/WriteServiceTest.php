<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Service\V1\Product\Link;

use \Magento\Webapi\Model\Rest\Config as RestConfig;

class WriteServiceTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'configurableProductProductLinkWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/configurable-products/';

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testRemoveChild()
    {
        $productSku = 'configurable';
        $childSku = 'simple_10';
        $this->assertTrue($this->removeChild($productSku, $childSku));
    }

    protected function removeChild($productSku, $childSku)
    {
        $resourcePath = self::RESOURCE_PATH . '%s/children/%s';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => sprintf($resourcePath, $productSku, $childSku),
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'removeChild'
            ]
        ];
        $requestData = array('productSku' => $productSku, 'childSku' => $childSku);
        return $this->_webApiCall($serviceInfo, $requestData);
    }
}
