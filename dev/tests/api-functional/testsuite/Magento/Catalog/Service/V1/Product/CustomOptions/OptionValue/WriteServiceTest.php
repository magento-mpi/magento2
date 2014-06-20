<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue;

use Magento\TestFramework\TestCase\WebapiAbstract;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductCustomOptionsOptionValueWriteServiceV1';
    const SERVICE_VERSION = 'V1';

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    protected function setUp()
    {
        $this->productFactory = \Magento\TestFramework\ObjectManager::getInstance()
            ->get('Magento\Catalog\Model\ProductFactory');
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     * @dataProvider validOptionDataProvider
     */
    public function testAdd($optionType)
    {
        $productId = 1;
        $fixtureOption = null;
        $value = [
            'price' => 100500,
            'price_type' => 'fixed',
            'sku' => 'new option sku',
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

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options/" . $fixtureOption->getId() . '/values',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Add'
            ]
        ];
        $this->_webApiCall(
            $serviceInfo,
            ['productSku' => $productSku, 'optionId' => $fixtureOption->getId(), 'value' => $value]
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
     * @dataProvider invalidOptionDataProvider
     */
    public function testAddWithNotAllowedTypes($optionType)
    {
        $productId = 1;
        $fixtureOption = null;
        $value = [
            'price' => 100500,
            'price_type' => 'fixed',
            'sku' => 'new option sku',
            'custom_attributes' => [
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

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options/" . $fixtureOption->getId() . '/values',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Add'
            ]
        ];

        $expected = 'Adding option value to option type ' . $optionType.  ' is not supported';
        try {
            $this->_webApiCall(
                $serviceInfo,
                ['productSku' => $productSku, 'optionId' => $fixtureOption->getId(), 'value' => $value]
            );
            $this->fail('Expected exception is not thrown');
        } catch (\SoapFault $exception) {
            $this->assertEquals($expected, $exception->getMessage());
        } catch (\Exception $exception) {
            $this->assertEquals($expected, json_decode($exception->getMessage())->message);
        }
    }

    public function invalidOptionDataProvider()
    {
        return [
            'field' => ['field'],
            'area' => ['area'],
            'file' => ['file'],
            'date' => ['date'],
            'date_time' => ['date_time'],
            'time' => ['time'],
        ];
    }
}
