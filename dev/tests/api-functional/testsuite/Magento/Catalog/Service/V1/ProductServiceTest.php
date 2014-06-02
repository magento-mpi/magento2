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
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $productData[Product::SKU],
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'delete'
            ]
        ];

        $response = (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) ?
            $this->_webApiCall($serviceInfo, ['sku' => $productData[Product::SKU]]) : $this->_webApiCall($serviceInfo);
        $this->assertTrue($response);
    }

    public function testDeleteNoSuchEntityException()
    {
        $invalidSku = '';
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
                $this->_webApiCall($serviceInfo, [Product::SKU => $invalidSku]);
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
     */
    public function testSearch($filterGroups, $expected, $sortData)
    {
        list($sortField, $sortValue) = $sortData;
        if ($sortValue === SearchCriteria::SORT_DESC && TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->markTestSkipped('Sorting doesn\'t work in SOAP');
        }
        /** @var $searchCriteriaBuilder  \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\V1\Data\SearchCriteriaBuilder'
        );
        foreach ($filterGroups as $filterGroup) {
            $group = array();
            foreach ($filterGroup as $filter) {
                list($filterKey, $filterValue) = $filter;
                $group[] = (new FilterBuilder())
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
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function searchDataProvider()
    {
        $product1 = $this->_createProduct($this->getSimpleProductData());
        $product2 = $this->_createProduct($this->getSimpleProductData());
        return array(
            array(
                [ //Groups
                    [ //Group(AND)
                        [Product::SKU, $product1[Product::SKU]] //Filters(OR)
                    ],
                ],
                [0 => $product1[Product::SKU]],
                [Product::ID, SearchCriteria::SORT_ASC]
            ),
            array(
                [ //Groups
                    [ //Group(AND)
                        [Product::SKU, $product1[Product::SKU]],
                    ],
                    [
                        [Product::NAME, $product1[Product::NAME]],
                    ],
                    [
                        [Product::VISIBILITY, $product1[Product::VISIBILITY]],
                    ],
                    [
                        [Product::TYPE_ID, $product1[Product::TYPE_ID]],
                    ],
                    [
                        [Product::PRICE, $product1[Product::PRICE]],
                    ],
                    [
                        [Product::STATUS, $product1[Product::STATUS]],
                    ],
                    [
                        [Product::TYPE_ID, $product1[Product::TYPE_ID]],
                    ]
                ],
                [0 => $product1[Product::SKU]],
                [Product::ID, SearchCriteria::SORT_ASC]
            ),
            array(
                [
                    [
                        [Product::SKU, $product1[Product::SKU]],
                        [Product::SKU, $product2[Product::SKU]]
                    ],
                ],
                [0 => $product2[Product::SKU], 1 => $product1[Product::SKU]],
                [Product::ID, SearchCriteria::SORT_DESC]
            ),
            array(
                [
                    [
                        [Product::SKU, $product1[Product::SKU]],
                        [Product::SKU, $product2[Product::SKU]]
                    ],
                ],
                [0 => $product1[Product::SKU], 1 => $product2[Product::SKU]],
                [Product::ID, SearchCriteria::SORT_ASC]
            ),
            array(
                [
                    [
                        [Product::SKU, $product1[Product::SKU]],
                        [Product::SKU, $product2[Product::SKU]],
                        [Product::SKU, 'N']
                    ],
                ],
                [0 => $product1[Product::SKU], 1 => $product2[Product::SKU]],
                [Product::ID, SearchCriteria::SORT_ASC]
            ),
            array(
                [ //Groups
                    [ //Group(AND)
                        [Product::SKU, $product1[Product::SKU]], //Filters(OR)
                        [Product::SKU, $product2[Product::SKU]]
                    ],
                    [
                        [Product::SKU, 'N']
                    ]
                ],
                [], //No Items expected
                [Product::ID, SearchCriteria::SORT_ASC]
            )
        );
    }

    protected function getSimpleProductData()
    {
        return array(
            Product::SKU => uniqid('sku-', true),
            Product::NAME => uniqid('name-', true),
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
            Product::SKU => $productSku,
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
            $this->_webApiCall($serviceInfo, ['sku' => $productData[Product::SKU]]) : $this->_webApiCall($serviceInfo);
        foreach ([Product::SKU, Product::NAME, Product::PRICE, Product::STATUS, Product::VISIBILITY] as $key) {
            $this->assertEquals($productData[$key], $response[$key]);
        }
    }

    public function testGetNoSuchEntityException()
    {
        $invalidSku = '';
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
                $this->_webApiCall($serviceInfo, [Product::SKU => $invalidSku]);
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
            $this->assertEquals(['fieldName' => Product::SKU, 'fieldValue' => $invalidSku], $errorObj['parameters']);
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
