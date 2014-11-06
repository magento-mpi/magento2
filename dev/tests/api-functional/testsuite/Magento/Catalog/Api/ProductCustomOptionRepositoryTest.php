<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TestFramework\Helper\Bootstrap;

class ProductCustomOptionRepositoryTest extends WebapiAbstract
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->productFactory = $this->objectManager->get('Magento\Catalog\Model\ProductFactory');
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     */
    public function testRemove()
    {
        $sku = 'simple';
        /** @var  \Magento\Catalog\Model\Product $product */
        $product = $this->objectManager->create('Magento\Catalog\Model\Product');
        $product->load(1);
        $customOptions= $product->getOptions();
        $optionId = array_pop($customOptions)->getId();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/products/$sku/options/$optionId",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ]
        ];
        $this->assertTrue($this->_webApiCall($serviceInfo, ['productSku' => $sku, 'optionId' => $optionId]));
        /** @var  \Magento\Catalog\Model\Product $product */
        $product = $this->objectManager->create('Magento\Catalog\Model\Product');
        $product->load(1);
        $this->assertNull($product->getOptionById($optionId));
        $this->assertEquals(9, count($product->getOptions()));
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     */
    public function testGet()
    {
        $productSku = 'simple';
        $service = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface');
        $options = $service->getList('simple');
        $optionId = $options[0]->getOptionId();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options/" . $optionId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ]
        ];
        $option = $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'optionId' => $optionId]);
        unset($option['product_sku']);
        unset($option['option_id']);
        $excepted = include '_files/product_options.php';
        $this->assertEquals($excepted[0], $option);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     */
    public function testGetList()
    {
        $productSku = 'simple';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ]
        ];
        $options = $this->_webApiCall($serviceInfo, ['productSku' => $productSku]);

        /** Unset dynamic data */
        foreach ($options as $key => $value) {
            unset($options[$key]['product_sku']);
            unset($options[$key]['option_id']);
            if (!empty($options[$key]['values'])) {
                foreach ($options[$key]['values'] as $newKey => $valueData) {
                    unset($options[$key]['values'][$newKey]['option_type_id']);
                }
            }
        }

        $excepted = include '_files/product_options.php';
        $this->assertEquals(count($excepted), count($options));

        //in order to make assertion result readable we need to check each element separately
        foreach ($excepted as $index => $value) {
            $this->assertEquals($value, $options[$index]);
        }
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_without_options.php
     * @magentoAppIsolation enabled
     * @dataProvider optionDataProvider
     */
    public function testAdd($optionData)
    {
        $productSku = 'simple';

        $optionDataPost = $optionData;
        $optionDataPost['product_sku'] = $productSku;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/options',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ]
        ];

        $result = $this->_webApiCall($serviceInfo, ['option' => $optionDataPost]);
        unset($result['product_sku']);
        unset($result['option_id']);
        if (!empty($result['values']))
        {
            foreach($result['values'] as $key => $value)
            {
                unset($result['values'][$key]['option_type_id']);
            }
        }
        $this->assertEquals($optionData, $result);
    }

    public function optionDataProvider()
    {
        $fixtureOptions = array();
        $fixture = include '_files/product_options.php';
        foreach ($fixture as $item) {
            $fixtureOptions[$item['type']] = [
                'optionData' => $item,
            ];
        };

        return $fixtureOptions;
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_without_options.php
     * @magentoAppIsolation enabled
     * @dataProvider optionNegativeDataProvider
     */
    public function testAddNegative($optionData)
    {
        $productSku = 'simple';
        $optionDataPost = $optionData;
        $optionDataPost['product_sku'] = $productSku;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/products/options",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ]
        ];
        $this->setExpectedException('Exception', '', 400);
        $this->_webApiCall($serviceInfo, ['option' => $optionDataPost]);
    }

    public function optionNegativeDataProvider()
    {
        $fixtureOptions = array();
        $fixture = include '_files/product_options_negative.php';
        foreach ($fixture as $key => $item) {
            $fixtureOptions[$key] = [
                'optionData' => $item,
            ];
        };

        return $fixtureOptions;
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     */
    public function testUpdate()
    {
        $productSku = 'simple';
        /** @var \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface $optionReadService */
        $productRepository = $this->objectManager->create(
            'Magento\Catalog\Model\ProductRepository'
        );

        $options = $productRepository->get($productSku, true)->getOptions();
        $option = array_shift($options);
        $optionId = $option->getOptionId();
        $optionDataPost = [
            'product_sku' => $productSku,
            'title' => $option->getTitle() . "_updated",
            'type' => $option->getType(),
            'sort_order' => $option->getSortOrder(),
            'is_require' => $option->getIsRequire(),
            'price' => $option->getPrice(),
            'price_type' => $option->getPriceType(),
            'sku' => $option->getSku(),
            'max_characters' => 500
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/options/' . $optionId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ]
        ];

        $updatedOption = $this->_webApiCall(
            $serviceInfo, ['option' => $optionDataPost]
        );
        unset($updatedOption['values']);
        $optionDataPost['option_id'] = $option->getOptionId();
        $this->assertEquals($optionDataPost, $updatedOption);
    }

    /**
     * @param string $optionType
     *
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     * @dataProvider validOptionDataProvider
     */
    public function testUpdateOptionAddingNewValue($optionType)
    {
        $productId = 1;
        $fixtureOption = null;
        $value1 = [
            'price' => 100500,
            'price_type' => 'fixed',
            'sku' => 'new option sku ' . $optionType,
            'title' => 'New Option Title',
            'sort_order' => 100
        ];

        $product = $this->productFactory->create();
        $product->load($productId);

        /**@var $option \Magento\Catalog\Model\Product\Option */
        foreach ($product->getOptions() as $option) {
            if ($option->getType() == $optionType) {
                $fixtureOption = $option;
                break;
            }
        }

        $values = [];
        foreach($option->getValues() as $key => $value)
        {
            $values[$key] = [
                'price' => $value->getPrice(),
                'price_type' => $value->getPriceType(),
                'sku' => $value->getSku(),
                'title' => $value->getTitle(),
                'sort_order' => $value->getSortOrder()
        ];
        }
        $values[] = $value1;
        $data = array(
            'product_sku' => $option->getProductSku(),
            'title' => $option->getTitle(),
            'type' => $option->getType(),
            'is_require' => $option->getIsRequire(),
            'sort_order' => $option->getSortOrder(),
            'values' => $values
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/options/' . $fixtureOption->getId(),
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ]
        ];
        $valueObject = $this->_webApiCall(
            $serviceInfo, ['option' => $data]
        );
        $values = end($valueObject['values']);
        $this->assertEquals($value1['price'], $values['price']);
        $this->assertEquals($value1['price_type'], $values['price_type']);
        $this->assertEquals($value1['sku'], $values['sku']);
        $this->assertEquals('New Option Title', $values['title']);
        $this->assertEquals(100, $values['sort_order']);
    }

    public function validOptionDataProvider()
    {
        return [
            'drop_down' => ['drop_down'],
            'checkbox' => ['checkbox'],
            'radio' => ['radio'],
            'multiple' => ['multiple']
        ];
    }
}
