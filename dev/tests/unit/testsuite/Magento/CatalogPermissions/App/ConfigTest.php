<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\App;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $method
     * @param string $configMethod
     * @param string $path
     * @param string|string[]|bool $value
     * @param string|bool $configValue
     * @dataProvider dataProviderMethods
     */
    public function testMethods($method, $configMethod, $path, $value, $configValue)
    {
        $storeConfigMock = $this->getMockForAbstractClass(
            'Magento\Core\Model\Store\ConfigInterface'
        );
        $storeConfigMock->expects($this->once())
            ->method($configMethod)
            ->with($path, null)
            ->will($this->returnValue($configValue));
        $model = new Config($storeConfigMock);
        $this->assertEquals($value, $model->$method());
    }

    /**
     * @return array
     */
    public function dataProviderMethods()
    {
        return [
            [
                'isEnabled', 'getConfigFlag',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_ENABLED,
                true, true,
            ],
            [
                'isEnabled', 'getConfigFlag',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_ENABLED,
                false, false,
            ],
            [
                'getCatalogCategoryViewMode', 'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW,
                'value', 'value',
            ],
            [
                'getCatalogCategoryViewGroups', 'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW . '_groups',
                [], '',
            ],
            [
                'getCatalogCategoryViewGroups', 'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW . '_groups',
                ['value1', 'value2'], 'value1,value2',
            ],
            [
                'getCatalogProductPriceMode', 'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE,
                'value', 'value',
            ],
            [
                'getCatalogProductPriceGroups', 'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE . '_groups',
                [], '',
            ],
            [
                'getCatalogProductPriceGroups', 'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE . '_groups',
                ['value1', 'value2'], 'value1,value2',
            ],
            [
                'getCheckoutItemsMode', 'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS,
                'value', 'value',
            ],
            [
                'getCheckoutItemsGroups', 'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS . '_groups',
                [], '',
            ],
            [
                'getCheckoutItemsGroups', 'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS . '_groups',
                ['value1', 'value2'], 'value1,value2',
            ],
            [
                'getCatalogSearchDenyGroups', 'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_DENY_CATALOG_SEARCH,
                [], '',
            ],
            [
                'getCatalogSearchDenyGroups', 'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_DENY_CATALOG_SEARCH,
                ['value1', 'value2'], 'value1,value2',
            ],
            [
                'getRestrictedLandingPage', 'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_LANDING_PAGE,
                'value', 'value',
            ],
        ];
    }
}
