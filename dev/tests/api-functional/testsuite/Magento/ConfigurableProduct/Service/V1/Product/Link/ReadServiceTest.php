<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Link;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'configurableProductProductLinkReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/configurable-products/:productId/children';

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testGetChildren()
    {
        $this->markTestSkipped(
            'The test is skipped to be fixed on https://jira.corp.x.com/browse/MAGETWO-27788'
        );
        $productSku = 'configurable';

        /** @var array $result */
        $result = $this->getChildren($productSku);

        $this->assertCount(2, $result);

        foreach ($result as $product) {
            $this->assertArrayHasKey('custom_attributes', $product);
            $this->assertArrayHasKey('price', $product);
            $this->assertArrayHasKey('updated_at', $product);

            $this->assertArrayHasKey('name', $product);
            $this->assertContains('Configurable Option', $product['name']);

            $this->assertArrayHasKey('sku', $product);
            $this->assertContains('simple_', $product['sku']);

            $this->assertArrayHasKey('status', $product);
            $this->assertEquals('1', $product['status']);

            $this->assertArrayHasKey('visibility', $product);
            $this->assertEquals('1', $product['visibility']);
        }
    }

    /**
     * @param string $productSku
     * @return string
     */
    protected function getChildren($productSku)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productId', $productSku, self::RESOURCE_PATH),
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'getChildren'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productId' => $productSku]);
    }
}
