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
        $storeConfigMock = $this->getMockForAbstractClass('Magento\Core\Model\Store\ConfigInterface');
        $storeConfigMock->expects(
            $this->once()
        )->method(
            $configMethod
        )->with(
            $path,
            null
        )->will(
            $this->returnValue($configValue)
        );
        $model = new Config($storeConfigMock);
        $this->assertEquals($value, $model->{$method}());
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function dataProviderMethods()
    {
        return array(
            array(
                'isEnabled',
                'getConfigFlag',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_ENABLED,
                true,
                true
            ),
            array(
                'isEnabled',
                'getConfigFlag',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_ENABLED,
                false,
                false
            ),
            array(
                'getCatalogCategoryViewMode',
                'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW,
                'value',
                'value'
            ),
            array(
                'getCatalogCategoryViewGroups',
                'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW . '_groups',
                array(),
                ''
            ),
            array(
                'getCatalogCategoryViewGroups',
                'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW . '_groups',
                array('value1', 'value2'),
                'value1,value2'
            ),
            array(
                'getCatalogProductPriceMode',
                'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE,
                'value',
                'value'
            ),
            array(
                'getCatalogProductPriceGroups',
                'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE . '_groups',
                array(),
                ''
            ),
            array(
                'getCatalogProductPriceGroups',
                'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE . '_groups',
                array('value1', 'value2'),
                'value1,value2'
            ),
            array(
                'getCheckoutItemsMode',
                'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS,
                'value',
                'value'
            ),
            array(
                'getCheckoutItemsGroups',
                'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS . '_groups',
                array(),
                ''
            ),
            array(
                'getCheckoutItemsGroups',
                'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS . '_groups',
                array('value1', 'value2'),
                'value1,value2'
            ),
            array(
                'getCatalogSearchDenyGroups',
                'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_DENY_CATALOG_SEARCH,
                array(),
                ''
            ),
            array(
                'getCatalogSearchDenyGroups',
                'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_DENY_CATALOG_SEARCH,
                array('value1', 'value2'),
                'value1,value2'
            ),
            array(
                'getRestrictedLandingPage',
                'getConfig',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_LANDING_PAGE,
                'value',
                'value'
            )
        );
    }
}
