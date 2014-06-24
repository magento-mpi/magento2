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
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Catalog\Service\V1\Data\Product;

/**
 * Class ProductServiceTest
 */
class ProductServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products';

    private $productData = [
        [Product::SKU => 'sku1', Product::NAME => 'name1', Product::TYPE_ID => 'simple', Product::PRICE => 3.62],
        [Product::SKU => 'sku2', Product::NAME => 'name2', Product::TYPE_ID => 'simple', Product::PRICE => 3.62],
    ];

    /**
     * @return array
     */
    public function productCreationProvider()
    {
        $productBuilder = function ($data) {
            return array_replace_recursive(
                $this->getSimpleProductData(),
                [
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
        $response = $this->_createProduct($product);
        $this->assertArrayHasKey(Product::SKU, $response);
    }

    /**
     * @depends testCreate
     */
    public function testDelete()
    {
        $productData = $this->_createProduct($this->getSimpleProductData());
        $response = $this->_deleteProduct($productData[Product::SKU]);
        $this->assertTrue($response);
    }

    public function testDeleteNoSuchEntityException()
    {
        $invalidSku = '(nonExistingSku)';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $invalidSku,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'delete'
            ]
        ];

        $expectedMessage = 'There is no product with provided SKU';

        try {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $this->_webApiCall($serviceInfo, ['id' => $invalidSku]);
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
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @dataProvider searchDataProvider
     * @depends      testCreate
     * @depends      testDelete
     */
    public function testSearch($filterGroups, $expected, $sortData)
    {
        $this->_createProduct($this->getSimpleProductData($this->productData[0]));
        $this->_createProduct($this->getSimpleProductData($this->productData[1]));
        list($sortField, $sortValue) = $sortData;
        if ($sortValue === SearchCriteria::SORT_DESC && TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->markTestSkipped('Sorting doesn\'t work in SOAP');
        }
        /** @var $searchCriteriaBuilder  \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\V1\Data\SearchCriteriaBuilder'
        );
        /** @var $filterBuilder  \Magento\Framework\Service\V1\Data\FilterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\V1\Data\FilterBuilder'
        );
        foreach ($filterGroups as $filterGroup) {
            $group = array();
            foreach ($filterGroup as $filter) {
                list($filterKey, $filterValue) = $filter;
                $group[] = $filterBuilder
                    ->setField($filterKey)
                    ->setValue($filterValue)
                    ->create();
            }
            $searchCriteriaBuilder->addFilter($group);
        }

        $searchCriteriaBuilder->setSortOrders([$sortField => $sortValue]);
        $searchData = $searchCriteriaBuilder->create()->__toArray();
        $requestData = ['searchCriteria' => $searchData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'search'
            ]
        ];


        $searchResults = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertArrayHasKey('items', $searchResults);
        $this->assertEquals(count($expected), count($searchResults['items']));
        $this->assertEquals(count($expected), $searchResults['total_count']);
        foreach ($expected as $key => $productSku) {
            $this->assertEquals($productSku, $searchResults['items'][$key][Product::SKU]);
        }
        $this->_deleteProduct($this->productData[0][Product::SKU]);
        $this->_deleteProduct($this->productData[1][Product::SKU]);
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function searchDataProvider()
    {
        return array(
            array(
                [ //Groups
                    [ //Group(AND)
                        [Product::SKU, $this->productData[0][Product::SKU]] //Filters(OR)
                    ],
                ],
                [0 => $this->productData[0][Product::SKU]]
            ),
            array(
                [ //Groups
                    [ //Group(AND)
                        [Product::SKU, $this->productData[0][Product::SKU]],
                    ],
                    [
                        [Product::NAME, $this->productData[0][Product::NAME]],
                    ],
                    [
                        [Product::TYPE_ID, $this->productData[0][Product::TYPE_ID]],
                    ],
                    [
                        [Product::PRICE, $this->productData[0][Product::PRICE]],
                    ],
                ],
                [0 => $this->productData[0][Product::SKU]]
            ),
            array(
                [
                    [
                        [Product::SKU, $this->productData[0][Product::SKU]],
                        [Product::SKU, $this->productData[1][Product::SKU]]
                    ],
                ],
                [0 => $this->productData[1][Product::SKU], 1 => $this->productData[0][Product::SKU]]
            ),
            array(
                [
                    [
                        [Product::SKU, $this->productData[0][Product::SKU]],
                        [Product::SKU, $this->productData[1][Product::SKU]]
                    ],
                ],
                [0 => $this->productData[0][Product::SKU], 1 => $this->productData[1][Product::SKU]]
            ),
            array(
                [
                    [
                        [Product::SKU, $this->productData[0][Product::SKU]],
                        [Product::SKU, $this->productData[1][Product::SKU]],
                        [Product::SKU, 'N']
                    ],
                ],
                [0 => $this->productData[0][Product::SKU], 1 => $this->productData[1][Product::SKU]]
            ),
            array(
                [ //Groups
                    [ //Group(AND)
                        [Product::SKU, $this->productData[0][Product::SKU]], //Filters(OR)
                        [Product::SKU, $this->productData[1][Product::SKU]]
                    ],
                    [
                        [Product::SKU, 'N']
                    ]
                ],
                [], //No Items expected
            )
        );
    }

    /**
     * Get Simple Product Data
     *
     * @param array $productData
     * @return array
     */
    protected function getSimpleProductData($productData = array())
    {
        return array(
            Product::SKU => isset($productData[Product::SKU]) ? $productData[Product::SKU] : uniqid('sku-', true),
            Product::NAME => isset($productData[Product::NAME]) ? $productData[Product::NAME] : uniqid('sku-', true),
            Product::VISIBILITY => 4,
            Product::TYPE_ID => 'simple',
            Product::PRICE => 3.62,
            Product::STATUS => 1,
            Product::TYPE_ID => 'simple'
        );
    }

    protected function _createProduct($product)
    {
        $serviceInfo = [
            'rest' => ['resourcePath' => self::RESOURCE_PATH, 'httpMethod' => RestConfig::HTTP_METHOD_POST],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'create'
            ],
        ];
        $requestData = ['product' => $product];
        $product[Product::SKU] = $this->_webApiCall($serviceInfo, $requestData);
        return $product;
    }

    /**
     * Delete Product
     *
     * @param string $sku
     * @return boolean
     */
    protected function _deleteProduct($sku)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $sku,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'delete'
            ]
        ];

        return (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) ?
            $this->_webApiCall($serviceInfo, ['id' => $sku]) : $this->_webApiCall($serviceInfo);
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateEmpty()
    {
        $this->_createProduct([]);
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateEmptySku()
    {
        $this->_createProduct([
            Product::SKU => '',
            Product::NAME => 'name',
            Product::PRICE => '10',
        ]);
    }

    public function testUpdate()
    {
        $response = $this->_createProduct($this->getSimpleProductData());
        $productSku = $response[Product::SKU];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $productSku,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'update'
            ]
        ];

        $requestData = [
            'id' => $productSku,
            'product' => [
                Product::NAME => uniqid('name-', true),
            ]
        ];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($productSku, $response);
    }

    /**
     * @depends testCreate
     */
    public function testGet()
    {
        $productData = $this->_createProduct($this->getSimpleProductData());
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $productData[Product::SKU],
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'get'
            ]
        ];

        $response = (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) ?
            $this->_webApiCall($serviceInfo, ['id' => $productData[Product::SKU]]) : $this->_webApiCall($serviceInfo);
        foreach ([Product::SKU, Product::NAME, Product::PRICE, Product::STATUS, Product::VISIBILITY] as $key) {
            $this->assertEquals($productData[$key], $response[$key]);
        }
    }

    public function testGetNoSuchEntityException()
    {
        $invalidSku = '(nonExistingSku)';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $invalidSku,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'get'
            ]
        ];

        $expectedMessage = 'There is no product with provided SKU';

        try {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $this->_webApiCall($serviceInfo, ['id' => $invalidSku]);
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
