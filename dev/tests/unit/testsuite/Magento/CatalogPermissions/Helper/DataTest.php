<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Helper\Data
     */
    protected $model;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderMock;

    protected function setUp()
    {
        $this->sessionMock = $this->getMock(
            'Magento\Customer\Model\Session',
            array('__wakeup', 'getCustomerGroupId'),
            array(),
            '',
            false
        );

        $this->configMock = $this->getMockForAbstractClass(
            'Magento\CatalogPermissions\App\ConfigInterface',
            array(),
            '',
            false,
            false,
            true,
            array()
        );

        $this->urlBuilderMock = $this->getMockForAbstractClass(
            '\Magento\Framework\UrlInterface',
            array(),
            '',
            false,
            false,
            true,
            array()
        );

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            'Magento\CatalogPermissions\Helper\Data',
            array(
                'config' => $this->configMock,
                'customerSession' => $this->sessionMock,
                'urlBuilder' => $this->urlBuilderMock
            )
        );
    }

    /**
     * @param string $method
     * @param string $modeMethod
     * @param string $groupsMethod
     * @param string $mode
     * @param string[] $groups
     * @param int|null $customerGroupId
     * @param bool $result
     * @dataProvider dataProviderIsGrantMethods
     */
    public function testIsGrantMethods($method, $modeMethod, $groupsMethod, $mode, $groups, $customerGroupId, $result)
    {
        $this->configMock->expects($this->once())->method($modeMethod)->with('store')->will($this->returnValue($mode));
        $this->configMock->expects(
            $this->once()
        )->method(
            $groupsMethod
        )->with(
            'store'
        )->will(
            $this->returnValue($groups)
        );
        $this->sessionMock->expects(
            $this->any()
        )->method(
            'getCustomerGroupId'
        )->will(
            $this->returnValue($customerGroupId)
        );
        $this->assertEquals($result, $this->model->{$method}('store', $customerGroupId));
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function dataProviderIsGrantMethods()
    {
        return array(
            array(
                'isAllowedCategoryView',
                'getCatalogCategoryViewMode',
                'getCatalogCategoryViewGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_NONE,
                array(),
                1,
                false
            ),
            array(
                'isAllowedCategoryView',
                'getCatalogCategoryViewMode',
                'getCatalogCategoryViewGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_ALL,
                array(),
                2,
                true
            ),
            array(
                'isAllowedCategoryView',
                'getCatalogCategoryViewMode',
                'getCatalogCategoryViewGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_CUSTOMER_GROUP,
                array(),
                3,
                false
            ),
            array(
                'isAllowedCategoryView',
                'getCatalogCategoryViewMode',
                'getCatalogCategoryViewGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_CUSTOMER_GROUP,
                array('1', '2'),
                0,
                false
            ),
            array(
                'isAllowedCategoryView',
                'getCatalogCategoryViewMode',
                'getCatalogCategoryViewGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_CUSTOMER_GROUP,
                array('1', '2'),
                1,
                true
            ),
            array(
                'isAllowedProductPrice',
                'getCatalogProductPriceMode',
                'getCatalogProductPriceGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_NONE,
                array(),
                null,
                false
            ),
            array(
                'isAllowedProductPrice',
                'getCatalogProductPriceMode',
                'getCatalogProductPriceGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_ALL,
                array(),
                null,
                true
            ),
            array(
                'isAllowedProductPrice',
                'getCatalogProductPriceMode',
                'getCatalogProductPriceGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_CUSTOMER_GROUP,
                array(),
                null,
                false
            ),
            array(
                'isAllowedProductPrice',
                'getCatalogProductPriceMode',
                'getCatalogProductPriceGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_CUSTOMER_GROUP,
                array('1', '2'),
                null,
                false
            ),
            array(
                'isAllowedProductPrice',
                'getCatalogProductPriceMode',
                'getCatalogProductPriceGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_CUSTOMER_GROUP,
                array('1', '2'),
                1,
                true
            ),
            array(
                'isAllowedCheckoutItems',
                'getCheckoutItemsMode',
                'getCheckoutItemsGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_NONE,
                array('1', '2'),
                1,
                false
            ),
            array(
                'isAllowedCheckoutItems',
                'getCheckoutItemsMode',
                'getCheckoutItemsGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_ALL,
                array('1'),
                1,
                true
            ),
            array(
                'isAllowedCheckoutItems',
                'getCheckoutItemsMode',
                'getCheckoutItemsGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_CUSTOMER_GROUP,
                array(),
                null,
                false
            ),
            array(
                'isAllowedCheckoutItems',
                'getCheckoutItemsMode',
                'getCheckoutItemsGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_CUSTOMER_GROUP,
                array('1', '2'),
                '0',
                false
            ),
            array(
                'isAllowedCheckoutItems',
                'getCheckoutItemsMode',
                'getCheckoutItemsGroups',
                \Magento\CatalogPermissions\App\ConfigInterface::GRANT_CUSTOMER_GROUP,
                array('1', '2'),
                '1',
                true
            )
        );
    }

    /**
     * @param string[] $groups
     * @param int|null $customerGroupId
     * @param bool $result
     * @dataProvider dataProviderIsAllowedCatalogSearch
     */
    public function testIsAllowedCatalogSearch($groups, $customerGroupId, $result)
    {
        $this->configMock->expects(
            $this->once()
        )->method(
            'getCatalogSearchDenyGroups'
        )->will(
            $this->returnValue($groups)
        );
        $this->sessionMock->expects(
            $this->any()
        )->method(
            'getCustomerGroupId'
        )->will(
            $this->returnValue($customerGroupId)
        );
        $this->assertEquals($result, $this->model->isAllowedCatalogSearch());
    }

    /**
     * @return array
     */
    public function dataProviderIsAllowedCatalogSearch()
    {
        return array(
            array(array(), 1, true),
            array(array(), null, true),
            array(array('1', '2'), null, true),
            array(array('1', '2'), 3, true),
            array(array('1', '2'), 1, false)
        );
    }

    public function testGetLandingPageUrl()
    {
        $this->configMock->expects(
            $this->once()
        )->method(
            'getRestrictedLandingPage'
        )->will(
            $this->returnValue('some uri')
        );
        $this->urlBuilderMock->expects(
            $this->once()
        )->method(
            'getUrl'
        )->with(
            '',
            array('_direct' => 'some uri')
        )->will(
            $this->returnValue('some url')
        );
        $this->assertEquals('some url', $this->model->getLandingPageUrl());
    }
}
