<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Webapi\Exception as HTTPExceptionCodes;

use \Magento\Catalog\Service\V1\Data\Product;

/**
 * Class ProductServiceTest
 */
class ProductServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products';

    /**
     * @return array
     */
    public static function productCreationProvider()
    {
        $productBuilder = function($data) {
            return array_replace_recursive(
                [
                    Product::SKU => uniqid('sku-', true),
                    Product::NAME => uniqid('name-', true),
                    Product::VISIBILITY => 4,
                    Product::TYPE_ID => 'simple',
                    Product::PRICE => 3.62,
                    Product::STATUS => 1,
                    'custom_attributes' => [
                        [
                            'attribute_code' => 'description',
                            'value' => 'test description'
                        ],
                        [
                            'attribute_code' => 'meta_title',
                            'value' => 'meta_title'
                        ],
                    ]
                ],
                $data
            );
        };
        return [
            [$productBuilder([Product::TYPE_ID => 'simple'])],
            [$productBuilder([Product::TYPE_ID => 'virtual'])],
        ];
    }

    /**
     * @dataProvider productCreationProvider
     */
    public function testCreate($product)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'create'
            ],
        ];

        $requestData = ['product' => $product];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertGreaterThan(0, $response);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testDelete()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH  . '/1' ,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'delete'
            ]
        ];

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $response = $this->_webApiCall($serviceInfo, ['id' => 1]);
        } else {
            $response = $this->_webApiCall($serviceInfo);
        }
        $this->assertTrue($response);
    }

    public function testDeleteNoSuchEntityException()
    {
        $invalidId = -1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH  . '/' . $invalidId,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'delete'
            ]
        ];

        $expectedMessage = 'No such entity with %fieldName = %fieldValue';

        try {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $this->_webApiCall($serviceInfo, ['id' => $invalidId]);
            } else {
                $this->_webApiCall($serviceInfo);
            }
            $this->fail("Expected exception");
        } catch (\SoapFault $e) {
            $this->assertContains(
                $expectedMessage,
                $e->getMessage(),
                "SoapFault does not contain expected message."
            );
        } catch (\Exception $e) {
            $errorObj = $this->_processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals(['fieldName' => 'id', 'fieldValue' => $invalidId], $errorObj['parameters']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @param \Exception $e
     * @return array
     * <pre> ex.
     * 'message' => "No such entity with %fieldName1 = %value1, %fieldName2 = %value2"
     * 'parameters' => [
     *      "fieldName1" => "email",
     *      "value1" => "dummy@example.com",
     *      "fieldName2" => "websiteId",
     *      "value2" => 0
     * ]
     *
     * </pre>
     */
    protected function _processRestExceptionResult(\Exception $e)
    {
        $error = json_decode($e->getMessage(), true);
        //Remove line breaks and replace with space
        $error['message'] = trim(preg_replace('/\s+/', ' ', $error['message']));
        // remove trace and type, will only be present if server is in dev mode
        unset($error['trace']);
        unset($error['type']);
        return $error;
    }
}
