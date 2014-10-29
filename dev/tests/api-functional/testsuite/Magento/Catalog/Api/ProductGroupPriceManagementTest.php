<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product;

use Magento\TestFramework\TestCase\WebapiAbstract;

class ProductGroupPriceManagementTest extends WebapiAbstract
{
    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_group_prices.php
     */
    public function testGetList()
    {
        $productSku = 'simple_with_group_price';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . '/group-prices',
                'httpMethod' => 'GET',
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ],
        ];
        $groupPriceList = $this->_webApiCall($serviceInfo);
        $this->assertCount(2, $groupPriceList);
        $this->assertEquals(9, $groupPriceList[0]['value']);
        $this->assertEquals(7, $groupPriceList[1]['value']);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_group_prices.php
     */
    public function testDelete()
    {
        $productSku = 'simple_with_group_price';
        $customerGroupId = \Magento\Customer\Service\V1\CustomerGroupServiceInterface::NOT_LOGGED_IN_ID;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/products/$productSku/group-prices/$customerGroupId",
                'httpMethod' => 'DELETE',
            ],
            'soap' => [
                'service' => 'catalogProductGroupPriceServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductGroupPriceServiceV1Delete',
            ],
        ];
        $requestData = array('productSku' => $productSku, 'customerGroupId' => $customerGroupId);
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testAdd()
    {
        $productSku = 'simple';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . '/group-prices/1/price/10',
                'httpMethod' => 'POST',
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ]
        ];
        $this->_webApiCall($serviceInfo);
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Api\ProductGroupPriceManagementInterface $service */
        $service = $objectManager->get('\Magento\Catalog\Api\ProductGroupPriceManagementInterface');
        $prices = $service->getList($productSku);
        $this->assertCount(1, $prices);
        $this->assertEquals(10, $prices[0]->getValue());
        $this->assertEquals(1, $prices[0]->getCustomerGroupId());
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoApiDataFixture Magento/Store/_files/website.php
     */
    public function testAddForDifferentWebsite()
    {
        $productSku = 'simple';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . '/group-prices/1/price/10',
                'httpMethod' => 'POST',
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ],
        ];
        $this->_webApiCall($serviceInfo, ['productSku' => $productSku]);
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Api\ProductGroupPriceManagementInterface $service */
        $service = $objectManager->get('\Magento\Catalog\Api\ProductGroupPriceManagementInterface');
        $prices = $service->getList($productSku);
        $this->assertCount(1, $prices);
        $this->assertEquals(10, $prices[0]->getValue());
        $this->assertEquals(1, $prices[0]->getCustomerGroupId());
    }
}
