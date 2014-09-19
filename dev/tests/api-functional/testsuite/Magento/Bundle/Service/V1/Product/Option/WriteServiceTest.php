<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'bundleProductOptionWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/bundle-products/:productSku/option';

    /**
     * @magentoApiDataFixture Magento/Bundle/_files/product.php
     */
    public function testAdd()
    {
        $productSku = 'bundle-product';
        $request = [
            'required' => true,
            'position' => 0,
            'type' => 'select',
            'title' => 'test product',
            'product_links' => []
        ];

        $optionId = $this->add($productSku, $request);
        $this->assertGreaterThan(0, $optionId);
        $result = $this->get($productSku, $optionId);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('sku', $result);
        unset($result['id'], $result['sku']);

        ksort($result);
        ksort($request);
        $this->assertEquals($request, $result);
    }

    /**
     * @magentoApiDataFixture Magento/Bundle/_files/product.php
     */
    public function testUpdate()
    {
        $productSku = 'bundle-product';
        $request = ['title' => 'someTitle'];

        $optionId = $this->getList($productSku)[0]['id'];
        $result = $this->update($productSku, $optionId, $request);

        $this->assertTrue($result);

        $result = $this->get($productSku, $optionId);

        $this->assertCount(7, $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertEquals($request['title'], $result['title']);
    }

    /**
     * @magentoApiDataFixture Magento/Bundle/_files/product.php
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemove()
    {
        $productSku = 'bundle-product';

        $optionId = $this->getList($productSku)[0]['id'];
        $result = $this->remove($productSku, $optionId);

        $this->assertTrue($result);

        try {
            $this->get($productSku, $optionId);
        } catch (\Exception $e) {
            throw new NoSuchEntityException();
        }
    }

    /**
     * @param string $productSku
     * @param int $optionId
     * @return string
     */
    protected function remove($productSku, $optionId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productSku', $productSku, self::RESOURCE_PATH) . '/' . $optionId,
                'httpMethod' => Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'remove'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'optionId' => $optionId]);
    }

    /**
     * @param string $productSku
     * @param int $optionId
     * @param array $option
     * @return string
     */
    protected function update($productSku, $optionId, $option)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productSku', $productSku, self::RESOURCE_PATH) . '/' . $optionId,
                'httpMethod' => Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'update'
            ]
        ];
        return $this->_webApiCall(
            $serviceInfo,
            ['productSku' => $productSku, 'optionId' => $optionId, 'option' => $option]
        );
    }

    /**
     * @param string $productSku
     * @param array $option
     * @return string
     */
    protected function add($productSku, $option)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productSku', $productSku, self::RESOURCE_PATH) . '/add',
                'httpMethod' => Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'add'
            ]
        ];
        return $this->_webApiCall(
            $serviceInfo,
            ['productSku' => $productSku, 'option' => $option]
        );
    }

    /**
     * @param string $productSku
     * @return string
     */
    protected function getList($productSku)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productSku', $productSku, ReadServiceTest::RESOURCE_PATH) . '/all',
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => ReadServiceTest::SERVICE_READ_NAME,
                'serviceVersion' => ReadServiceTest::SERVICE_VERSION,
                'operation' => ReadServiceTest::SERVICE_READ_NAME . 'getList'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productSku' => $productSku]);
    }

    /**
     * @param string $productSku
     * @param int $optionId
     * @return string
     */
    protected function get($productSku, $optionId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productSku', $productSku, ReadServiceTest::RESOURCE_PATH)
                    . '/' . $optionId,
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => ReadServiceTest::SERVICE_READ_NAME,
                'serviceVersion' => ReadServiceTest::SERVICE_VERSION,
                'operation' => ReadServiceTest::SERVICE_READ_NAME . 'get'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'optionId' => $optionId]);
    }
}
