<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab;

class PermissionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions
     */
    protected $model;

    /**
     * @var \Magento\Backend\Block\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $categoryTree;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\CatalogPermissions\Model\Permission\IndexFactory
     */
    protected $permIndexFactory;

    /**
     * @var \Magento\CatalogPermissions\Model\Resource\Permission\CollectionFactory
     */
    protected $permissionCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Resource\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var \Magento\CatalogPermissions\Helper\Data
     */
    protected $catalogPermData;

    protected function setUp()
    {
        $this->storeManagerMock = $this->getMock(
            '\Magento\Store\Model\StoreManagerInterface',
            array(),
            array(),
            '',
            false
        );

        $this->requestMock = $this->getMock(
            '\Magento\Framework\App\RequestInterface',
            array(),
            array(),
            '',
            false
        );

        $this->context = $this->getMock(
            '\Magento\Backend\Block\Template\Context',
            array('getStoreManager', 'getRequest'),
            array(),
            '',
            false
        );

        $this->context->expects($this->any())->method('getStoreManager')->will(
            $this->returnValue($this->storeManagerMock)
        );

        $this->context->expects($this->any())->method('getRequest')->will(
            $this->returnValue($this->requestMock)
        );

        $this->categoryTree = $this->getMock(
            '\Magento\Catalog\Model\Resource\Category\Tree',
            array(),
            array(),
            '',
            false
        );

        $this->registry = $this->getMock(
            '\Magento\Framework\Registry',
            array('registry'),
            array(),
            '',
            false
        );

        $this->categoryFactory = $this->getMock(
            '\Magento\Catalog\Model\CategoryFactory',
            array(),
            array(),
            '',
            false
        );

        $this->jsonEncoder = $this->getMock(
            '\Magento\Framework\Json\EncoderInterface',
            array(),
            array(),
            '',
            false
        );

        $this->permIndexFactory = $this->getMock(
            '\Magento\CatalogPermissions\Model\Permission\IndexFactory',
            array('create', 'getIndexForCategory'),
            array(),
            '',
            false
        );

        $this->permissionCollectionFactory = $this->getMock(
            '\Magento\CatalogPermissions\Model\Resource\Permission\CollectionFactory',
            array(),
            array(),
            '',
            false
        );

        $this->groupCollectionFactory = $this->getMock(
            '\Magento\Customer\Model\Resource\Group\CollectionFactory',
            array('create', 'getAllIds'),
            array(),
            '',
            false
        );

        $this->catalogPermData = $this->getMock(
            '\Magento\CatalogPermissions\Helper\Data',
            array(),
            array(),
            '',
            false
        );

        $this->model = new \Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions(
            $this->context,
            $this->categoryTree,
            $this->registry,
            $this->categoryFactory,
            $this->jsonEncoder,
            $this->permIndexFactory,
            $this->permissionCollectionFactory,
            $this->groupCollectionFactory,
            $this->catalogPermData
        );
    }

    /**
     * @param int $categoryId
     * @param array $index
     * @param array $groupIds
     * @param array $result
     * @dataProvider getParentPermissionsDataProvider
     */
    public function testGetParentPermissions($categoryId, $index, $groupIds, $result)
    {
        $categoryMock = $this->getMock(
            '\Magento\Catalog\Model\Category',
            array('getId', 'getParentId'),
            array(),
            '',
            false
        );

        $websiteMock = $this->getMock(
            '\Magento\Store\Model\Website',
            array('getId', 'getDefaultStore'),
            array(),
            '',
            false
        );

        $categoryMock->expects($this->any())->method('getId')->will($this->returnValue($categoryId));
        $categoryMock->expects($this->any())->method('getParentId')->will($this->returnValue(1));
        $websiteMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $websiteMock->expects($this->any())->method('getDefaultStore')->will($this->returnValue(1));

        $this->registry->expects($this->any())->method('registry')->will($this->returnValue($categoryMock));
        $this->permIndexFactory->expects($this->any())->method('create')->will($this->returnSelf());
        $this->permIndexFactory->expects($this->any())->method('getIndexForCategory')->will($this->returnValue($index));
        $this->catalogPermData->expects($this->any())->method('isAllowedCategoryView')->will($this->returnValue(true));
        $this->catalogPermData->expects($this->any())->method('isAllowedProductPrice')->will($this->returnValue(true));
        $this->catalogPermData->expects($this->any())->method('isAllowedCheckoutItems')->will($this->returnValue(true));
        $this->groupCollectionFactory->expects($this->any())->method('create')->will($this->returnSelf());
        $this->groupCollectionFactory->expects($this->any())->method('getAllIds')->will($this->returnValue($groupIds));
        $this->requestMock->expects($this->any())->method('getParam')->will($this->returnValue(1));
        $this->storeManagerMock->expects($this->any())->method('getWebsites')->will(
            $this->returnValue(array($websiteMock))
        );
        $this->assertEquals($result, $this->model->getParentPermissions());
    }

    /**
     * @return array
     */
    public function getParentPermissionsDataProvider()
    {
        $index = array(
            1 => array(
                'website_id' => 1,
                'customer_group_id' => 1,
                'grant_catalog_category_view' => '0',
                'grant_catalog_product_price' => '-1',
                'grant_checkout_items' => '-2'
            ),
            2 => array(
                'website_id' => 2,
                'customer_group_id' => 2,
                'grant_catalog_category_view' => '-1',
                'grant_catalog_product_price' => '-2',
                'grant_checkout_items' => '0'
            )
        );
        $groupIds = array(1, 2);
        $groupIdsSecond = array(1, 2, 3);
        $result = array(
            '1_1' => array('category' => '-1', 'product' => '-1', 'checkout' => '-2'),
            '2_2' => array('category' => '-1', 'product' => '-2', 'checkout' => '0'),
            '1_2' => array('category' => '-1', 'product' => '-1', 'checkout' => '-1')
        );
        $resultSecond = array(
            '1_1' => array('category' => '-1', 'product' => '-1', 'checkout' => '-2'),
            '2_2' => array('category' => '-1', 'product' => '-2', 'checkout' => '0'),
            '1_2' => array('category' => '-1', 'product' => '-1', 'checkout' => '-1'),
            '1_3' => array('category' => '-1', 'product' => '-1', 'checkout' => '-1')
        );
        return array(array(3, $index, $groupIds, $result), array(0, $index, $groupIdsSecond, $resultSecond));
    }
}
