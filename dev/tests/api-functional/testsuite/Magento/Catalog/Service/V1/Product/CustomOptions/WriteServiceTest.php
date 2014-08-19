<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions;

use Magento\TestFramework\TestCase\WebapiAbstract;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductCustomOptionsWriteServiceV1';
    const SERVICE_VERSION = 'V1';

    /**
     * @var Data\OptionBuilder
     */
    protected $optionBuilder;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager =  \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->optionBuilder = $this->objectManager->get(
            'Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionBuilder'
        );

        $this->productFactory = $this->objectManager->get('Magento\Catalog\Model\ProductFactory');
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

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Add'
            ]
        ];
        $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'option' => $optionDataPost]);

        /** @var \Magento\Catalog\Model\ProductRepository $repository */
        $repository = $this->objectManager->get('Magento\Catalog\Model\ProductRepository');

        /** @var \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ReaderInterface $reader */
        $reader = $this->objectManager->get(
            'Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ReaderInterface'
        );

        $product = $repository->get('simple');
        $options = $product->getOptions();

        $this->assertEquals(1, count($options));
        /** @var \Magento\Catalog\Model\Product\Option $option */
        $option = current($options);

        $data = array(
            Data\Option::TITLE => $option->getTitle(),
            Data\Option::TYPE => $option->getType(),
            Data\Option::IS_REQUIRE => $option->getIsRequire(),
            Data\Option::SORT_ORDER => $option->getSortOrder(),
            Data\Option::METADATA => $reader->read($option)
        );
        $optionObject = $this->optionBuilder->populateWithArray($data)->create();
        $format = function ($element) {
                $element['price'] = intval($element['price']);
                if (isset($element['custom_attributes'])) {
                    $attributes = [];
                    foreach ($element['custom_attributes'] as $attribute) {
                        if ($attribute['attribute_code'] == 'option_type_id') {
                            continue;
                        }
                        $attributes[] = $attribute;
                    }
                    $element['custom_attributes'] = $attributes;
                }
            return $element;
        };

        $actual = $optionObject->__toArray();
        $actual['metadata'] = array_map($format, $actual['metadata']);
        $this->assertEquals($optionData, $actual);
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
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Add'
            ]
        ];


        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->setExpectedException('SoapFault', 'Could not save product option');
        } else {
            $this->setExpectedException('Exception', '', 400);
        }
        $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'option' => $optionDataPost]);
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
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Remove'
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
    public function testUpdate()
    {
        $productSku = 'simple';
        /** @var \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface $optionReadService */
        $optionReadService = $this->objectManager->create(
            'Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface'
        );

        $options = $optionReadService->getList($productSku);
        $optionId = $options[0]->getOptionId();
        $optionDataPost = $options[0]->__toArray();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options/" . $optionId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Update'
            ]
        ];

        $this->assertEquals(10, $optionDataPost['metadata'][0]['custom_attributes']['max_characters']['value']);
        $optionDataPost['title'] = $optionDataPost['title'] . "_updated";
        $optionDataPost['metadata'][0]['custom_attributes']['max_characters']['value'] = 500;

        $this->_webApiCall(
            $serviceInfo, ['productSku' => $productSku, 'optionId' => $optionId, 'option' => $optionDataPost]
        );

        /** @var \Magento\Catalog\Model\ProductRepository $repository */
        $productRepository = $this->objectManager->create('Magento\Catalog\Model\ProductRepository');

        /** @var \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface $optionReadService */
        $optionReadService = $this->objectManager->create(
            'Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface',
            array('productRepository' => $productRepository)
        );
        $updatedOption = $optionReadService->get($productSku, $optionId)->__toArray();
        $this->assertEquals($optionDataPost, $updatedOption);
    }

    /**
     * @param string $optionType
     *
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     * @dataProvider validOptionDataProvider
     */
    public function testUpdateOptionAddingNewOptionMetadata($optionType)
    {
        $productId = 1;
        $fixtureOption = null;
        $value = [
            'price' => 100500,
            'price_type' => 'fixed',
            'sku' => 'new option sku ' . $optionType,
            'custom_attributes' => [
                ['attribute_code' => 'title', 'value' => 'New Option Title'],
                ['attribute_code' => 'sort_order', 'value' => 100]
            ]
        ];

        $product = $this->productFactory->create();
        $product->load($productId);
        $productSku = $product->getSku();

        /**@var $option \Magento\Catalog\Model\Product\Option */
        foreach ($product->getOptions() as $option) {
            if ($option->getType() == $optionType) {
                $fixtureOption = $option;
                break;
            }
        }

        /** @var \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ReaderInterface $reader */
        $reader = $this->objectManager->get(
            'Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ReaderInterface'
        );

        $data = array(
            Data\Option::TITLE => $option->getTitle(),
            Data\Option::TYPE => $option->getType(),
            Data\Option::IS_REQUIRE => $option->getIsRequire(),
            Data\Option::SORT_ORDER => $option->getSortOrder(),
            Data\Option::METADATA => $reader->read($fixtureOption)
        );
        $optionObject = $this->optionBuilder->populateWithArray($data)->create();
        $optionDataPost = $optionObject->__toArray();
        $optionDataPost['metadata'][] = $value;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options/" . $fixtureOption->getId(),
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Update'
            ]
        ];
        $this->_webApiCall(
            $serviceInfo,
            ['productSku' => $productSku, 'optionId' => $fixtureOption->getId(), 'option' => $optionDataPost]
        );

        $actualProduct = $this->productFactory->create();
        $actualProduct->load($productId);

        $actualOption = $actualProduct->getOptionById($fixtureOption->getId());
        $values = $actualOption->getValues();

        /** @var \Magento\Catalog\Model\Product\Option\Value $valueObject */
        $valueObject = end($values);

        $this->assertEquals($value['price'], $valueObject->getPrice());
        $this->assertEquals($value['price_type'], $valueObject->getPriceType());
        $this->assertEquals($value['sku'], $valueObject->getSku());
        $this->assertEquals('New Option Title', $valueObject->getTitle());
        $this->assertEquals(100, $valueObject->getSortOrder());
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

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     * @dataProvider optionMetadataUpdateDataProvider
     */
    public function testUpdateOptionMetadata($optionType)
    {
        $productId = 1;
        $fixtureOption = null;

        $product = $this->productFactory->create();
        $product->load($productId);
        $productSku = $product->getSku();

        /**@var $option \Magento\Catalog\Model\Product\Option */
        foreach ($product->getOptions() as $option) {
            if ($option->getType() == $optionType) {
                $fixtureOption = $option;
                break;
            }
        }

        /** @var \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ReaderInterface $reader */
        $reader = \Magento\TestFramework\ObjectManager::getInstance()
            ->get('Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ReaderInterface');

        $data = array(
            Data\Option::TITLE => $option->getTitle(),
            Data\Option::TYPE => $option->getType(),
            Data\Option::IS_REQUIRE => $option->getIsRequire(),
            Data\Option::SORT_ORDER => $option->getSortOrder(),
            Data\Option::METADATA => $reader->read($fixtureOption)
        );
        $optionObject = $this->optionBuilder->populateWithArray($data)->create();
        $optionDataPost = $optionObject->__toArray();

        $updatedValues = $optionDataPost['metadata'];

        foreach ($updatedValues as &$item) {
            $item['price'] = $item['price'] * 5;
            $item['sku'] = $item['sku'] . '_updated';
        }
        $optionDataPost['metadata'] = $updatedValues;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options/" . $fixtureOption->getId(),
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Update'
            ]
        ];
        $this->_webApiCall(
            $serviceInfo,
            ['productSku' => $productSku, 'optionId' => $fixtureOption->getId(), 'option' => $optionDataPost]
        );

        $actualProduct = $this->productFactory->create();
        $actualProduct->load($productId);

        $values = $actualProduct->getOptionById($fixtureOption->getId())->getValues();
        foreach (array_values($values) as $index => $value) {
            /** Skipped because of framework issue. There is no ability to work with global scope */
            //$this->assertEquals($updatedValues[$index]['price'], intval($value->getPrice()));
            $this->assertEquals($updatedValues[$index]['sku'], $value->getSku());
        }
    }

    public function optionMetadataUpdateDataProvider()
    {
        return [
            'drop_down' => ['drop_down'],
            'checkbox' => ['checkbox'],
            'radio' => ['radio'],
            'multiple' => ['multiple'],
            'field' => ['field'],
            'area' => ['area'],
            'file' => ['file'],
            'date' => ['date'],
            'date_time' => ['date_time'],
            'time' => ['time']
        ];
    }


    /**
     * Look through all options to find a title
     *
     * @param string $title
     * @param \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $options
     * @return bool
     */
    protected function getOptionIndexByTitle($title, $options)
    {
        foreach($options as $index => $option) {
            if ($title == $option->getTitle()) {
                return $index;
            }
        }
        return false;
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @dataProvider selectTitlesProvider
     * @magentoAppIsolation enabled
     */
    public function testOptionMetadataRemoval($optionTitle)
    {
        $productSku = 'simple';

        /** @var \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface $optionReadService */
        $optionReadService = $this->objectManager->create(
            'Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface'
        );

        $options = $optionReadService->getList($productSku);
        $index = $this->getOptionIndexByTitle($optionTitle, $options);
        $this->assertEquals(2, count($options[$index]->getMetadata()));

        $optionId = $options[$index]->getOptionId();
        $optionDataPost = $options[$index]->__toArray();
        // remove one value
        array_pop($optionDataPost['metadata']);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options/" . $optionId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Update'
            ]
        ];

        $this->_webApiCall(
            $serviceInfo, ['productSku' => $productSku, 'optionId' => $optionId, 'option' => $optionDataPost]
        );

        // reset product repository for read service
        $productRepository = $this->objectManager->create('\Magento\Catalog\Model\ProductRepository');
        $optionReadService = $this->objectManager->create(
            'Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface',
            [
                'productRepository' => $productRepository,
            ]
        );
        $updatedOptions = $optionReadService->getList($productSku);
        $this->assertEquals(1, count($updatedOptions[$index]->getMetadata()));
    }

    /**
     * Select group option titles from fixture product(product_with_options.php)
     *
     * @return array
     */
    public function selectTitlesProvider()
    {
        return [
            'radio' => ['radio option'],
            'checkbox' => ['checkbox option'],
            'multiple' => ['multiple option'],
            'drop_down' => ['drop_down option'],
        ];
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @dataProvider selectTitlesProvider
     * @magentoAppIsolation enabled
     */
    public function testLastOptionMetadataRemoval($optionTitle)
    {
        $productSku = 'simple';

        /** @var \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface $optionReadService */
        $optionReadService = $this->objectManager->create(
            'Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface'
        );

        $options = $optionReadService->getList($productSku);
        $index = $this->getOptionIndexByTitle($optionTitle, $options);
        $this->assertEquals(2, count($options[$index]->getMetadata()));

        $optionId = $options[$index]->getOptionId();
        $optionDataPost = $options[$index]->__toArray();
        // remove all values
        $optionDataPost['metadata'] = [];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options/" . $optionId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Update'
            ]
        ];

        try {
            $this->_webApiCall(
                $serviceInfo, ['productSku' => $productSku, 'optionId' => $optionId, 'option' => $optionDataPost]
            );
        } catch (\SoapFault $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = json_decode($e->getMessage())->message;
        }
        $this->assertEquals($message, 'Could not save custom option');
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_without_options.php
     * @magentoAppIsolation enabled
     */
    public function testAddNegativeOptionIdExists()
    {
        $fixture = include '_files/product_options.php';
        $optionData = $fixture[0];
        $productSku = 'simple';
        $optionData['option_id'] = 1;
        $optionDataPost = $optionData;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Add'
            ]
        ];


        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->setExpectedException('SoapFault', 'Unable to save option. Please, check input data.');
        } else {
            $this->setExpectedException('Exception', '', 400);
        }
        $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'option' => $optionDataPost]);
    }
}

