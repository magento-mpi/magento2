<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Attribute;

use Magento\TestFramework\Helper\Bootstrap;

class PriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Gdata\Gshopping\Entry $entry
     * @dataProvider convertAttributeDataProvider
     */
    public function testConvertAttribute($product, $entry)
    {
        /** @var \Magento\GoogleShopping\Model\Attribute\Price $model */
        $model = Bootstrap::getObjectManager()->create('Magento\GoogleShopping\Model\Attribute\Price');
        $customerGroupService = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\CustomerGroupServiceInterface'
        );
        $defaultCustomerGroup = $customerGroupService->getDefaultGroup($product->getStoreId());
        $model->convertAttribute($product, $entry);
        $this->assertEquals($defaultCustomerGroup->getId(), $product->getCustomerGroupId());
    }

    /**
     * @return array
     */
    public function convertAttributeDataProvider()
    {
        $product = Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
        $entry = Bootstrap::getObjectManager()->create('Magento\Framework\Gdata\Gshopping\Entry');
        return [
            [$product, $entry]
        ];
    }
}
