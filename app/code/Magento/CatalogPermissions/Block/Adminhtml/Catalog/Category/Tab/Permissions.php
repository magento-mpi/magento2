<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml permission tab on category page
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
class Magento_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions
    extends Magento_Adminhtml_Block_Catalog_Category_Abstract
    implements Magento_Backend_Block_Widget_Tab_Interface
{

    protected $_template = 'catalog/category/tab/permissions.phtml';

    /**
     * Catalog permissions data
     *
     * @var Magento_CatalogPermissions_Helper_Data
     */
    protected $_catalogPermData = null;

    /**
     * @var Magento_Customer_Model_Resource_Group_CollectionFactory
     */
    protected $_groupCollFactory;

    /**
     * @var Magento_CatalogPermissions_Model_Resource_Permission_CollectionFactory
     */
    protected $_permissionCollFactory;

    /**
     * @var Magento_CatalogPermissions_Model_Permission_IndexFactory
     */
    protected $_permIndexFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_CatalogPermissions_Model_Permission_IndexFactory $permIndexFactory
     * @param Magento_CatalogPermissions_Model_Resource_Permission_CollectionFactory $permissionCollFactory
     * @param Magento_Customer_Model_Resource_Group_CollectionFactory $groupCollFactory
     * @param Magento_CatalogPermissions_Helper_Data $catalogPermData
     * @param Magento_Catalog_Model_Resource_Category_Tree $categoryTree
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_CatalogPermissions_Model_Permission_IndexFactory $permIndexFactory,
        Magento_CatalogPermissions_Model_Resource_Permission_CollectionFactory $permissionCollFactory,
        Magento_Customer_Model_Resource_Group_CollectionFactory $groupCollFactory,
        Magento_CatalogPermissions_Helper_Data $catalogPermData,
        Magento_Catalog_Model_Resource_Category_Tree $categoryTree,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_permIndexFactory = $permIndexFactory;
        $this->_permissionCollFactory = $permissionCollFactory;
        $this->_groupCollFactory = $groupCollFactory;
        $this->_catalogPermData = $catalogPermData;
        parent::__construct($categoryTree, $coreData, $context, $registry, $data);
    }

    /**
     * Prepare layout
     *
     * @return Magento_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions
     */
    protected function _prepareLayout()
    {
        $this->addChild('row', 'Magento_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions_Row');

        $this->addChild('add_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('New Permission'),
            'class' => 'add' . ($this->isReadonly() ? ' disabled' : ''),
            'type'  => 'button',
            'disabled' => $this->isReadonly()
        ));

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
            'permissions'  => array()
        );

        if ($this->getCategoryId()) {
            foreach ($this->getPermissionCollection() as $permission) {
                $config['permissions']['permission' . $permission->getId()] = $permission->getData();
            }
        }

        $config['single_mode']  = $this->_storeManager->hasSingleStore();
        $config['website_id']   = $this->_storeManager->getStore(true)->getWebsiteId();
        $config['parent_vals']  = $this->getParentPermissions();

        $config['use_parent_allow'] = __('(Allow)');
        $config['use_parent_deny'] = __('(Deny)');
        //$config['use_parent_config'] = __('(Config)');
        $config['use_parent_config'] = '';

        $additionalConfig = $this->getAdditionConfigData();
        if (is_array($additionalConfig)) {
            $config = array_merge($additionalConfig, $config);
        }

        return $this->_coreData->jsonEncode($config);
    }

    /**
     * Retrieve permission collection
     *
     * @return Magento_CatalogPermissions_Model_Resource_Permission_Collection
     */
    public function getPermissionCollection()
    {
        if (!$this->hasData('permission_collection')) {
            $collection = $this->_permissionCollFactory->create()
                ->addFieldToFilter('category_id', $this->getCategoryId())
                ->setOrder('permission_id', 'asc');
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
        }
        // parent category
        else if ($this->getRequest()->getParam('parent')) {
            $categoryId = $this->getRequest()->getParam('parent');
        }

        $permissions = array();
        if ($categoryId) {
            $index  = $this->_permIndexFactory->create()
                ->getIndexForCategory($categoryId, null, null);
            foreach ($index as $row) {
                $permissionKey = $row['website_id'] . '_' . $row['customer_group_id'];
                $permissions[$permissionKey] = array(
                    'category'  => $row['grant_catalog_category_view'],
                    'product'   => $row['grant_catalog_product_price'],
                    'checkout'  => $row['grant_checkout_items']
                );
            }
        }

        $websites = $this->_storeManager->getWebsites(false);
        $groups   = $this->_groupCollFactory->create()->getAllIds();

        /* @var $helper Magento_CatalogPermissions_Helper_Data */
        $helper   = $this->_catalogPermData;

        $parent = (string)Magento_CatalogPermissions_Model_Permission::PERMISSION_PARENT;
        $allow  = (string)Magento_CatalogPermissions_Model_Permission::PERMISSION_ALLOW;
        $deny   = (string)Magento_CatalogPermissions_Model_Permission::PERMISSION_DENY;

        foreach ($groups as $groupId) {
            foreach ($websites as $website) {
                /* @var $website Magento_Core_Model_Website */
                $websiteId = $website->getId();

                $store = $website->getDefaultStore();
                $category = $helper->isAllowedCategoryView($store, $groupId);
                $product  = $helper->isAllowedProductPrice($store, $groupId);
                $checkout = $helper->isAllowedCheckoutItems($store, $groupId);

                $permissionKey = $websiteId . '_' . $groupId;
                if (!isset($permissions[$permissionKey])) {
                    $permissions[$permissionKey] = array(
                        'category'  => $category ? $allow : $deny,
                        'product'   => $product ? $allow : $deny,
                        'checkout'  => $checkout ? $allow : $deny
                    );
                } else {
                    // validate and rewrite parent values for exists data
                    $data = $permissions[$permissionKey];
                    $permissions[$permissionKey] = array(
                        'category'  => $data['category'] == $parent ? ($category ? $allow : $deny) : $data['category'],
                        'product'   => $data['product'] == $parent ? ($checkout ? $allow : $deny) : $data['product'],
                        'checkout'  => $data['checkout'] == $parent ? ($product ? $allow : $deny) : $data['checkout'],
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
     * @return boolean
     */
    public function canShowTab()
    {
        $canShow = $this->getCanShowTab();
        if (is_null($canShow)) {
            $canShow = $this->_authorization
                ->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions');
        }
        return $canShow;
    }

    /**
     * Tab visibility
     *
     * @return boolean
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
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getCategory()->getPermissionsReadonly();
    }
}
