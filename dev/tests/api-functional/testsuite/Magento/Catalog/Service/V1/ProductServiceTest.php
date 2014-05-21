<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Framework\Service\V1\Data\SearchCriteria;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

use \Magento\Catalog\Service\V1\Data\Product;

/**
 * Class ProductServiceTest
 */
class ProductServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/product';


    /** @var ProductServiceInterface */
    private $productService;


    /** @var \Magento\Webapi\Helper\Data */
    private $helper;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->helper = Bootstrap::getObjectManager()->create('Magento\Webapi\Helper\Data');
    }

    /**
     * @todo: extract in class
     */
    protected function _createProduct()
    {
        return [
//            Product::ID => null,
            Product::SKU => uniqid('sku-', true),
            Product::VISIBILITY => 4,
            Product::TYPE_ID => 4,
            Product::STATUS => 1,
        ];

    }

    public function testSave()
    {
        $product = $this->_createProduct();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH  . '/save' ,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ]
        ];

        $requestData = ['product' => $product];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($response);
    }
}
