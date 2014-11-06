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

class ProductTierPriceManagementTest extends WebapiAbstract
{
    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @dataProvider getListDataProvider
     */
    public function testGetList($customerGroupId, $count, $value, $qty)
    {
        $productSku = 'simple';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . '/group-prices/' . $customerGroupId . '/tiers',
                'httpMethod' => 'GET',
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ],
        ];

            $groupPriceList = $this->_webApiCall($serviceInfo);

        $this->assertCount($count, $groupPriceList);
        if ($count) {
            $this->assertEquals($value, $groupPriceList[0]['value']);
            $this->assertEquals($qty, $groupPriceList[0]['qty']);
        }
    }

    public function getListDataProvider()
    {
        return array(
            array(0, 1, 5, 3),
            array(1, 0, null, null),
            array('all', 2, 8, 2),
        );
    }

    /**
     * @param string|int $customerGroupId
     * @param int $qty
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @dataProvider deleteDataProvider
     */
    public function testDelete($customerGroupId, $qty)
    {
        $productSku = 'simple';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/products/$productSku/group-prices/$customerGroupId/tiers/$qty",
                'httpMethod' => 'DELETE',
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ],
        ];
        $requestData = array('productSku' => $productSku, 'customerGroupId' => $customerGroupId, 'qty' => $qty);
        $this->assertTrue( $this->_webApiCall($serviceInfo, $requestData));
    }


    public function deleteDataProvider()
    {
        return array(
            'delete_tier_price_for_specific_customer_group' => array(0, 3),
            'delete_tier_price_for_all_customer_group' => array('all', 5)
        );
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoAppIsolation enabled
     */
    public function testAdd()
    {
        $productSku = 'simple';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . '/group-prices/1/tiers/50/price/10',
                'httpMethod' => 'POST',
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ],
        ];

            $this->_webApiCall($serviceInfo);
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Api\ProductTierPriceManagementInterface $service */
        $service = $objectManager->get('\Magento\Catalog\Api\ProductTierPriceManagementInterface');
        $prices = $service->getList($productSku, 1);
        $this->assertCount(1, $prices);
        $this->assertEquals(10, $prices[0]->getValue());
        $this->assertEquals(50, $prices[0]->getQty());
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoAppIsolation enabled
     */
    public function testAddWithAllCustomerGrouped()
    {
        $productSku = 'simple';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . '/group-prices/all/tiers/50/price/20',
                'httpMethod' => 'POST',
            ],
            'soap' => [
                // @todo fix this configuration after SOAP test framework is functional
            ],
        ];

            $this->_webApiCall($serviceInfo);
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Api\ProductTierPriceManagementInterface $service */
        $service = $objectManager->get('\Magento\Catalog\Api\ProductTierPriceManagementInterface');
        $prices = $service->getList($productSku, 'all');
        $this->assertCount(3, $prices);
        $this->assertEquals(20, (int)$prices[2]->getValue());
        $this->assertEquals(50, (int)$prices[2]->getQty());
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoAppIsolation enabled
     */
    public function testUpdateWithAllGroups()
    {
        $productSku = 'simple';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . '/group-prices/all/tiers/2/price/20',
                'httpMethod' => 'POST',
            ],
            'soap' => [
                'service' => 'catalogProductTierPriceServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductTierPriceServiceV1set',
            ],
        ];

            $this->_webApiCall($serviceInfo);
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Api\ProductTierPriceManagementInterface $service */
        $service = $objectManager->get('\Magento\Catalog\Api\ProductTierPriceManagementInterface');
        $prices = $service->getList($productSku, 'all');
        $this->assertCount(2, $prices);
        $this->assertEquals(20, (int)$prices[0]->getValue());
        $this->assertEquals(2, (int)$prices[0]->getQty());
    }
}
