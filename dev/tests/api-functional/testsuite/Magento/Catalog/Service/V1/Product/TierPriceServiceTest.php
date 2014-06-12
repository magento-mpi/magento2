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

class TierPriceServiceTest extends WebapiAbstract
{
    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @dataProvider getListDataProvider
     */
    public function testGetList($customerGroupId, $count, $value, $qty)
    {
        $productSku = 'simple';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/products/' . $productSku . '/group-prices/' . $customerGroupId . '/tiers',
                'httpMethod' => 'GET',
            ),
            'soap' => array(
                'service' => 'catalogProductTierPriceServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductTierPriceServiceV1GetList',
            ),
        );

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $groupPriceList = $this->_webApiCall(
                $serviceInfo, ['productSku' => $productSku, 'customerGroupId' => $customerGroupId]
            );
        } else {
            $groupPriceList = $this->_webApiCall($serviceInfo);
        }

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
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => "/V1/products/$productSku/group-prices/$customerGroupId/tiers/$qty",
                'httpMethod' => 'DELETE',
            ),
            'soap' => array(
                'service' => 'catalogProductTierPriceServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductTierPriceServiceV1Delete',
            ),
        );
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
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/products/' . $productSku . '/group-prices/1/tiers',
                'httpMethod' => 'POST',
            ),
            'soap' => array(
                'service' => 'catalogProductTierPriceServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductTierPriceServiceV1set',
            ),
        );
        $price = ['qty' => 50, 'value' => 10];

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->_webApiCall(
                $serviceInfo,
                ['productSku' => $productSku, 'customer_group_id' => 1, 'price' => $price]
            );
        } else {
            $this->_webApiCall($serviceInfo, ['price' => $price]);
        }
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Service\V1\Product\TierPriceServiceInterface $service */
        $service = $objectManager->get('\Magento\Catalog\Service\V1\Product\TierPriceServiceInterface');
        $prices = $service->getList($productSku, 1);
        $this->assertCount(3, $prices);
        $this->assertEquals(10, $prices[2]->getValue());
        $this->assertEquals(50, $prices[2]->getQty());
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testAddWithAllCustomerGrouped()
    {
        $productSku = 'simple';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/products/' . $productSku . '/group-prices/all/tiers',
                'httpMethod' => 'POST',
            ),
            'soap' => array(
                'service' => 'catalogProductTierPriceServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductTierPriceServiceV1set',
            ),
        );
        $price = ['qty' => 50, 'value' => 20];

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $this->_webApiCall(
                $serviceInfo,
                ['productSku' => $productSku, 'customer_group_id' => 'all', 'price' => $price]
            );
        } else {
            $this->_webApiCall($serviceInfo, ['price' => $price]);
        }
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Service\V1\Product\TierPriceServiceInterface $service */
        $service = $objectManager->get('\Magento\Catalog\Service\V1\Product\TierPriceServiceInterface');
        $prices = $service->getList($productSku, 'all');
        $this->assertCount(1, $prices);
        $this->assertEquals(20, (int)$prices[0]->getValue());
        $this->assertEquals(50, (int)$prices[0]->getQty());
    }
}
