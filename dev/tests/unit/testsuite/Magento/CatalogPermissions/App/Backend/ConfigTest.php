<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\App\Backend;

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
        $configMock = $this->getMockForAbstractClass('Magento\Framework\App\Config\ScopeConfigInterface');
        $configMock->expects(
            $this->once()
        )->method(
            $configMethod
        )->with(
            $path,
            'default'
        )->will(
            $this->returnValue($configValue)
        );
        $model = new Config($configMock);
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
                'isSetFlag',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_ENABLED,
                true,
                true
            ),
            array(
                'isEnabled',
                'isSetFlag',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_ENABLED,
                false,
                false
            ),
            array(
                'getCatalogCategoryViewMode',
                'getValue',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW,
                'value',
                'value'
            ),
            array(
                'getCatalogCategoryViewGroups',
                'getValue',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW . '_groups',
                array(),
                ''
            ),
            array(
                'getCatalogCategoryViewGroups',
                'getValue',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW . '_groups',
                array('value1', 'value2'),
                'value1,value2'
            ),
            array(
                'getCatalogProductPriceMode',
                'getValue',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE,
                'value',
                'value'
            ),
            array(
                'getCatalogProductPriceGroups',
                'getValue',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE . '_groups',
                array(),
                ''
            ),
            array(
                'getCatalogProductPriceGroups',
                'getValue',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE . '_groups',
                array('value1', 'value2'),
                'value1,value2'
            ),
            array(
                'getCheckoutItemsMode',
                'getValue',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS,
                'value',
                'value'
            ),
            array(
                'getCheckoutItemsGroups',
                'getValue',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS . '_groups',
                array(),
                ''
            ),
            array(
                'getCheckoutItemsGroups',
                'getValue',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS . '_groups',
                array('value1', 'value2'),
                'value1,value2'
            ),
            array(
                'getCatalogSearchDenyGroups',
                'getValue',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_DENY_CATALOG_SEARCH,
                array(),
                ''
            ),
            array(
                'getCatalogSearchDenyGroups',
                'getValue',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_DENY_CATALOG_SEARCH,
                array('value1', 'value2'),
                'value1,value2'
            ),
            array(
                'getRestrictedLandingPage',
                'getValue',
                \Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_LANDING_PAGE,
                'value',
                'value'
            )
        );
    }
}
