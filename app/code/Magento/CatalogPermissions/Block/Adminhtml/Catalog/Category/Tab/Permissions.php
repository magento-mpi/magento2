<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml permission tab on category page
 *
 */
namespace Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Block\Adminhtml\Category\AbstractCategory;
use Magento\Catalog\Model\Resource\Category\Tree;
use Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions as TabPermissions;
use Magento\CatalogPermissions\Helper\Data;
use Magento\CatalogPermissions\Model\Permission;
use Magento\CatalogPermissions\Model\Resource\Permission\Collection as PermissionCollection;
use Magento\Framework\Registry;
use Magento\Catalog\Model\CategoryFactory ;
use Magento\Customer\Model\Resource\Group\CollectionFactory as GroupCollectionFactory;
use Magento\Store\Model\Website;
use Magento\Framework\Json\EncoderInterface;

class Permissions extends AbstractCategory implements TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'catalog/category/tab/permissions.phtml';

    /**
     * Catalog permissions data
     *
     * @var Data
     */
    protected $_catalogPermData = null;

    /**
     * @var GroupCollectionFactory
     */
    protected $_groupCollectionFactory;

    /**
     * @var \Magento\CatalogPermissions\Model\Resource\Permission\CollectionFactory
     */
    protected $_permissionCollectionFactory;

    /**
     * @var \Magento\CatalogPermissions\Model\Permission\IndexFactory
     */
    protected $_permIndexFactory;

    /**
     * @var EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param Tree $categoryTree
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param EncoderInterface $jsonEncoder
     * @param \Magento\CatalogPermissions\Model\Permission\IndexFactory $permIndexFactory
     * @param \Magento\CatalogPermissions\Model\Resource\Permission\CollectionFactory $permissionCollectionFactory
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param Data $catalogPermData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Tree $categoryTree,
        Registry $registry,
        CategoryFactory $categoryFactory,
        EncoderInterface $jsonEncoder,
        \Magento\CatalogPermissions\Model\Permission\IndexFactory $permIndexFactory,
        \Magento\CatalogPermissions\Model\Resource\Permission\CollectionFactory $permissionCollectionFactory,
        GroupCollectionFactory $groupCollectionFactory,
        Data $catalogPermData,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_permIndexFactory = $permIndexFactory;
        $this->_permissionCollectionFactory = $permissionCollectionFactory;
        $this->_groupCollectionFactory = $groupCollectionFactory;
        $this->_catalogPermData = $catalogPermData;
        parent::__construct($context, $categoryTree, $registry, $categoryFactory, $data);
    }

    /**
     * Prepare layout
     *
     * @return TabPermissions
     */
    protected function _prepareLayout()
    {
        $this->addChild('row', 'Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions\Row');

        $this->addChild(
            'add_button',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('New Permission'),
                'class' => 'add' . ($this->isReadonly() ? ' disabled' : ''),
                'type' => 'button',
                'disabled' => $this->isReadonly()
            )
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve block config as JSON
     *
     * @return string
     */
    public function getConfigJson()
    {
        $config = array(
            'row' => $this->getChildHtml('row'),
            'duplicate_message' => __('You already have a permission with this scope.'),
            'permissions' => array()
        );

        if ($this->getCategoryId()) {
            foreach ($this->getPermissionCollection() as $permission) {
                $config['permissions']['permission' . $permission->getId()] = $permission->getData();
            }
        }

        $config['single_mode'] = $this->_storeManager->hasSingleStore();
        $config['website_id'] = $this->_storeManager->getStore(true)->getWebsiteId();
        $config['parent_vals'] = $this->getParentPermissions();

        $config['use_parent_allow'] = __('(Allow)');
        $config['use_parent_deny'] = __('(Deny)');
        //$config['use_parent_config'] = __('(Config)');
        $config['use_parent_config'] = '';

        $additionalConfig = $this->getAdditionConfigData();
        if (is_array($additionalConfig)) {
            $config = array_merge($additionalConfig, $config);
        }

        return $this->_jsonEncoder->encode($config);
    }

    /**
     * Retrieve permission collection
     *
     * @return PermissionCollection
     */
    public function getPermissionCollection()
    {
        if (!$this->hasData('permission_collection')) {
            $collection = $this->_permissionCollectionFactory->create()->addFieldToFilter(
                'category_id',
                $this->getCategoryId()
            )->setOrder(
                'permission_id',
                'asc'
            );
            $this->setData('permisssion_collection', $collection);
        }

        return $this->getData('permisssion_collection');
    }

    /**
     * Retrieve Use Parent permissions per website and customer group
     *
     * @return array
     */
    public function getParentPermissions()
    {
        $categoryId = null;
        if ($this->getCategoryId()) {
            $categoryId = $this->getCategory()->getParentId();
        } elseif ($this->getRequest()->getParam('parent')) {
            // parent category
            $categoryId = $this->getRequest()->getParam('parent');
        }

        $permissions = array();
        if ($categoryId) {
            $index = $this->_permIndexFactory->create()->getIndexForCategory($categoryId, null, null);
            foreach ($index as $row) {
                $permissionKey = $row['website_id'] . '_' . $row['customer_group_id'];
                $permissions[$permissionKey] = array(
                    'category' => $row['grant_catalog_category_view'],
                    'product' => $row['grant_catalog_product_price'],
                    'checkout' => $row['grant_checkout_items']
                );
            }
        }

        $websites = $this->_storeManager->getWebsites(false);
        $groups = $this->_groupCollectionFactory->create()->getAllIds();

        /* @var $helper Data */
        $helper = $this->_catalogPermData;

        $parent = (string)Permission::PERMISSION_PARENT;
        $allow = (string)Permission::PERMISSION_ALLOW;
        $deny = (string)Permission::PERMISSION_DENY;

        foreach ($groups as $groupId) {
            foreach ($websites as $website) {
                /* @var $website Website */
                $websiteId = $website->getId();

                $store = $website->getDefaultStore();
                $category = $helper->isAllowedCategoryView($store, $groupId);
                $product = $helper->isAllowedProductPrice($store, $groupId);
                $checkout = $helper->isAllowedCheckoutItems($store, $groupId);

                $permissionKey = $websiteId . '_' . $groupId;
                if (!isset($permissions[$permissionKey])) {
                    $permissions[$permissionKey] = array(
                        'category' => $category ? $allow : $deny,
                        'product' => $product ? $allow : $deny,
                        'checkout' => $checkout ? $allow : $deny
                    );
                } else {
                    // validate and rewrite parent values for exists data
                    $data = $permissions[$permissionKey];
                    $permissions[$permissionKey] = array(
                        'category' => $data['category'] == $parent ? $category ? $allow : $deny : $data['category'],
                        'product' => $data['product'] == $parent ? $checkout ? $allow : $deny : $data['product'],
                        'checkout' => $data['checkout'] == $parent ? $product ? $allow : $deny : $data['checkout']
                    );
                }
            }
        }

        return $permissions;
    }

    /**
     * Retrieve tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Category Permissions');
    }

    /**
     * Retrieve tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Category Permissions');
    }

    /**
     * Tab visibility
     *
     * @return bool
     */
    public function canShowTab()
    {
        $canShow = $this->getCanShowTab();
        if (is_null($canShow)) {
            $canShow = $this->_authorization->isAllowed(
                'Magento_CatalogPermissions::catalog_magento_catalogpermissions'
            );
        }
        return $canShow;
    }

    /**
     * Tab visibility
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrieve add button html
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Check is block readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getCategory()->getPermissionsReadonly();
    }
}
