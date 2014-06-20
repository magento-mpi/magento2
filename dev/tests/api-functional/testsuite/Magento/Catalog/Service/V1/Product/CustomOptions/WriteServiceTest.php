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

    protected function setUp()
    {
        $this->optionBuilder = \Magento\TestFramework\ObjectManager::getInstance()
            ->get('Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionBuilder');
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
        $optionDataPost['option_id'] = null;

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
        $repository = \Magento\TestFramework\ObjectManager::getInstance()
            ->get('Magento\Catalog\Model\ProductRepository');

        /** @var \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\ReaderInterface $reader */
        $reader = \Magento\TestFramework\ObjectManager::getInstance()
            ->get('Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\ReaderInterface');

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
            Data\Option::VALUE => $reader->read($option)
        );
        $optionObject = $this->optionBuilder->populateWithArray($data)->create();
        $format = function ($element) {
                $element['price'] = intval($element['price']);
                if (isset($element['custom_attributes'])) {
                    $element['custom_attributes'] = array_values($element['custom_attributes']);
                }
            unset($element['option_type_id']);
            return $element;
        };

        $actual = $optionObject->__toArray();
        $actual['value'] = array_map($format, $actual['value']);
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
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     */
    public function testRemove()
    {
        $sku = 'simple';
        /** @var  \Magento\Catalog\Model\Product $product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->load(1);
        $customOptions= $product->getOptions();
        $optionId = array_shift($customOptions)->getId();
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
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->load(1);
        $this->assertNull($product->getOptionById($optionId));
    }
}
