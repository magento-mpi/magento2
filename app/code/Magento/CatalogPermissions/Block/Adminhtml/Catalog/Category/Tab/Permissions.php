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
namespace Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab;

class Permissions
    extends \Magento\Catalog\Block\Adminhtml\Category\AbstractCategory
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_template = 'catalog/category/tab/permissions.phtml';

    /**
     * Catalog permissions data
     *
     * @var \Magento\CatalogPermissions\Helper\Data
     */
    protected $_catalogPermData = null;

    /**
     * @var \Magento\Customer\Model\Resource\Group\CollectionFactory
     */
    protected $_groupCollFactory;

    /**
     * @var \Magento\CatalogPermissions\Model\Resource\Permission\CollectionFactory
     */
    protected $_permissionCollFactory;

    /**
     * @var \Magento\CatalogPermissions\Model\Permission\IndexFactory
     */
    protected $_permIndexFactory;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Model\Resource\Category\Tree $categoryTree
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\CatalogPermissions\Model\Permission\IndexFactory $permIndexFactory
     * @param \Magento\CatalogPermissions\Model\Resource\Permission\CollectionFactory $permissionCollFactory
     * @param \Magento\Customer\Model\Resource\Group\CollectionFactory $groupCollFactory
     * @param \Magento\CatalogPermissions\Helper\Data $catalogPermData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\Resource\Category\Tree $categoryTree,
        \Magento\Core\Model\Registry $registry,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\CatalogPermissions\Model\Permission\IndexFactory $permIndexFactory,
        \Magento\CatalogPermissions\Model\Resource\Permission\CollectionFactory $permissionCollFactory,
        \Magento\Customer\Model\Resource\Group\CollectionFactory $groupCollFactory,
        \Magento\CatalogPermissions\Helper\Data $catalogPermData,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_permIndexFactory = $permIndexFactory;
        $this->_permissionCollFactory = $permissionCollFactory;
        $this->_groupCollFactory = $groupCollFactory;
        $this->_catalogPermData = $catalogPermData;
        parent::__construct($context, $categoryTree, $registry, $data);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions
     */
    protected function _prepareLayout()
    {
        $this->addChild('row', 'Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions\Row');

        $this->addChild('add_button', 'Magento\Backend\Block\Widget\Button', array(
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

        return $this->_jsonEncoder->encode($config);
    }

    /**
     * Retrieve permission collection
     *
     * @return \Magento\CatalogPermissions\Model\Resource\Permission\Collection
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
        elseif ($this->getRequest()->getParam('parent')) {
            // parent category
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

        /* @var $helper \Magento\CatalogPermissions\Helper\Data */
        $helper   = $this->_catalogPermData;

        $parent = (string)\Magento\CatalogPermissions\Model\Permission::PERMISSION_PARENT;
        $allow  = (string)\Magento\CatalogPermissions\Model\Permission::PERMISSION_ALLOW;
        $deny   = (string)\Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY;

        foreach ($groups as $groupId) {
            foreach ($websites as $website) {
                /* @var $website \Magento\Core\Model\Website */
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
