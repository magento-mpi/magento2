<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link;

use Magento\Webapi\Model\Rest\Config as RestConfig;
use \Magento\Catalog\Model\Product\Link;

class WriteServiceTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductLinkWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/';

    /**
     * @var string
     */
    protected $productSku;

    /**
     * @var string
     */
    protected $linkType;

    /**
     * @var array
     */
    protected $productData;

    /**
     * @var array
     */
    protected $serviceInfo;

    protected function setUp()
    {
        $this->productSku = 'simple';
        $this->linkType = 'related';
        $this->productData =
            [
                Data\LinkedProductEntity::ID => 21,
                Data\LinkedProductEntity::TYPE => 'virtual',
                Data\LinkedProductEntity::ATTRIBUTE_SET_ID => 4,
                Data\LinkedProductEntity::SKU => 'virtual-product',
                Data\LinkedProductEntity::POSITION => 3,
            ];
        $this->serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $this->productSku . '/links/' . $this->linkType,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Assign'
            ]
        ];
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoApiDataFixture Magento/Catalog/_files/product_virtual.php
     */
    public function testAssign()
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Service\V1\Product\Link\ReadServiceInterface $service */
        $service = $objectManager->get('Magento\Catalog\Service\V1\Product\Link\ReadServiceInterface');

        $this->_webApiCall(
            $this->serviceInfo,
            ['productSku' => $this->productSku, 'assignedProducts' => [$this->productData], 'type' => $this->linkType]
        );
        $actual = $service->getLinkedProducts($this->productSku, 'related');
        array_walk($actual, function (&$item){
            $item = $item->__toArray();
        });
        $this->assertEquals([$this->productData], $actual);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testAssignWithInvalidLinkedProducts()
    {

        $expectedException = 'Invalid data provided for linked products';
        try {
            $this->_webApiCall(
                $this->serviceInfo,
                [
                    'productSku' => $this->productSku,
                    'assignedProducts' => [$this->productData],
                    'type' => $this->linkType
                ]
            );
            $this->fail('Expected exception');
        } catch (\SoapFault $exception) {
            $this->assertContains(
                $expectedException,
                $exception->getMessage(),
                'SoapFault does not contain expected message'
            );
        } catch (\Exception $exception) {
            $this->assertContains(
                $expectedException,
                $exception->getMessage(),
                'Exception does not contain expected message'
            );
        }
    }

    public function testAssignWithInvalidProductSku()
    {
        $expectedException = 'There is no product with provided SKU';
        try {
            $this->_webApiCall(
                $this->serviceInfo,
                [
                    'productSku' => $this->productSku,
                    'assignedProducts' => [$this->productData],
                    'type' => $this->linkType
                ]
            );
            $this->fail('Expected exception');
        } catch (\SoapFault $exception) {
            $this->assertContains(
                $expectedException,
                $exception->getMessage(),
                'SoapFault does not contain expected message'
            );
        } catch (\Exception $exception) {
            $this->assertContains(
                $expectedException,
                $exception->getMessage(),
                'Exception does not contain expected message'
            );
        }
    }
}
