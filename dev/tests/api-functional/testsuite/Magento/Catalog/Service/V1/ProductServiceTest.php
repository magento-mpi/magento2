<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Service\V1\Data\Product;
use Magento\Framework\Api\SearchCriteria;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Exception as HTTPExceptionCodes;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class ProductServiceTest
 */
class ProductServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products';

    private $productData = [
        [
            Product::SKU => 'simple',
            Product::NAME => 'Simple Related Product',
            Product::TYPE_ID => 'simple',
            Product::PRICE => 10
        ],
        [
            Product::SKU => 'simple_with_cross',
            Product::NAME => 'Simple Product With Related Product',
            Product::TYPE_ID => 'simple',
            Product::PRICE => 10
        ],
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
            [$productBuilder([Product::TYPE_ID => 'simple', Product::SKU => 'psku-test-1'])],
            [$productBuilder([Product::TYPE_ID => 'virtual', Product::SKU => 'psku-test-2'])],
        ];
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/Model/Product/_files/service_product_create.php
     * @dataProvider productCreationProvider
     */
    public function testCreate($product)
    {
        $response = $this->createProduct($product);
        $this->assertArrayHasKey(Product::SKU, $response);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testDelete()
    {
        $response = $this->deleteProduct('simple');
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
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * Create another store one time for testSearch
     *
     * @magentoApiDataFixture Magento/Core/_files/store.php
     */
    public function testCreateAnotherStore()
    {
        /** @var $store \Magento\Store\Model\Store */
        $store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Store');
        $store->load('fixturestore');
        $this->assertNotNull($store->getId());
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/products_related.php
     * @depends testCreateAnotherStore
     * @dataProvider searchDataProvider
     */
    public function testSearch($filterGroups, $expected, $sortData)
    {
        list($sortField, $sortValue) = $sortData;
        if ($sortValue === SearchCriteria::SORT_DESC && TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->markTestSkipped('Sorting doesn\'t work in SOAP');
        }
        /** @var $searchCriteriaBuilder  \Magento\Framework\Api\SearchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\SearchCriteriaBuilder'
        );
        /** @var $filterBuilder  \Magento\Framework\Api\FilterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\FilterBuilder'
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
        /**@var \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Api\SortOrderBuilder'
        );
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $sortOrderBuilder->setField($sortField)->setDirection($sortValue)->create();
        $searchCriteriaBuilder->setSortOrders([$sortOrder]);
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
        return array(
            array(
                [ //Groups
                    [ //Group(AND)
                        [Product::SKU, $this->productData[0][Product::SKU]] //Filters(OR)
                    ],
                ],
                [0 => $this->productData[0][Product::SKU]],
                [Product::SKU, SearchCriteria::SORT_ASC]
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
                [0 => $this->productData[0][Product::SKU]],
                [Product::SKU, SearchCriteria::SORT_ASC]
            ),
            array(
                [
                    [
                        [Product::SKU, $this->productData[0][Product::SKU]],
                        [Product::SKU, $this->productData[1][Product::SKU]]
                    ],
                ],
                [0 => $this->productData[1][Product::SKU], 1 => $this->productData[0][Product::SKU]],
                [Product::SKU, SearchCriteria::SORT_DESC]
            ),
            array(
                [
                    [
                        [Product::SKU, $this->productData[0][Product::SKU]],
                        [Product::SKU, $this->productData[1][Product::SKU]]
                    ],
                ],
                [0 => $this->productData[0][Product::SKU], 1 => $this->productData[1][Product::SKU]],
                [Product::SKU, SearchCriteria::SORT_ASC]
            ),
            array(
                [
                    [
                        [Product::SKU, $this->productData[0][Product::SKU]],
                        [Product::SKU, $this->productData[1][Product::SKU]],
                        [Product::SKU, 'N']
                    ],
                ],
                [0 => $this->productData[0][Product::SKU], 1 => $this->productData[1][Product::SKU]],
                [Product::SKU, SearchCriteria::SORT_ASC]
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
                [Product::SKU, SearchCriteria::SORT_ASC]
            )
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateEmpty()
    {
        $this->createProduct([]);
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateEmptySku()
    {
        $this->createProduct(
            [
                Product::SKU => '',
                Product::NAME => 'name',
                Product::PRICE => '10',
            ]
        );
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testUpdate()
    {
        $productSku = 'simple';
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
     * @magentoApiDataFixture Magento/Catalog/_files/products_related.php
     */
    public function testGet()
    {
        $productData = $this->productData[0];
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
        foreach ([Product::SKU, Product::NAME, Product::PRICE] as $key) {
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
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_NOT_FOUND, $e->getCode());
        }
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

    protected function createProduct($product)
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
    protected function deleteProduct($sku)
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
     * Remove test store
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        /** @var \Magento\Framework\Registry $registry */
        $registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Registry');

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        /** @var $store \Magento\Store\Model\Store */
        $store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Store');
        $store->load('fixturestore');
        if ($store->getId()) {
            $store->delete();
        }

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
    }
}
