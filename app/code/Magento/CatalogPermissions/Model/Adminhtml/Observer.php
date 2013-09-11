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
 * Adminhtml observer
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
namespace Magento\CatalogPermissions\Model\Adminhtml;

class Observer
{
    const FORM_SELECT_ALL_VALUES = -1;

    protected $_indexQueue = array();
    protected $_indexProductQueue = array();

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(\Magento\AuthorizationInterface $authorization)
    {
        $this->_authorization = $authorization;
    }

    /**
     * Check permissions availability for current category
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Adminhtml\Observer
     */
    public function checkCategoryPermissions(\Magento\Event\Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();
        /* @var $category \Magento\Catalog\Model\Category */
        $helper = \Mage::helper('Magento\CatalogPermissions\Helper\Data');
        if (!$helper->isAllowedCategory($category) && $category->hasData('permissions')) {
            $category->unsetData('permissions');
        }

        return $this;
    }

    /**
     * Save category permissions on category after save event
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Adminhtml\Observer
     */
    public function saveCategoryPermissions(\Magento\Event\Observer $observer)
    {
        if (!\Mage::helper('Magento\CatalogPermissions\Helper\Data')->isEnabled()) {
            return $this;
        }

        $category = $observer->getEvent()->getCategory();
        /* @var $category \Magento\Catalog\Model\Category */
        if ($category->hasData('permissions') && is_array($category->getData('permissions'))
            && $this->_authorization
                ->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions')
        ) {
            foreach ($category->getData('permissions') as $data) {
                $permission = \Mage::getModel('Magento\CatalogPermissions\Model\Permission');
                if (!empty($data['id'])) {
                    $permission->load($data['id']);
                }

                if (!empty($data['_deleted'])) {
                    if ($permission->getId()) {
                        $permission->delete();
                    }
                    continue;
                }

                if ($data['website_id'] == self::FORM_SELECT_ALL_VALUES) {
                    $data['website_id'] = null;
                }

                if ($data['customer_group_id'] == self::FORM_SELECT_ALL_VALUES) {
                    $data['customer_group_id'] = null;
                }

                $permission->addData($data);
                $categoryViewPermission = $permission->getGrantCatalogCategoryView();
                if (\Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY == $categoryViewPermission) {
                    $permission->setGrantCatalogProductPrice(
                        \Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY
                    );
                }

                $productPricePermission = $permission->getGrantCatalogProductPrice();
                if (\Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY == $productPricePermission) {
                    $permission->setGrantCheckoutItems(\Magento\CatalogPermissions\Model\Permission::PERMISSION_DENY);
                }
                $permission->setCategoryId($category->getId());
                $permission->save();
            }
            $this->_indexQueue[] = $category->getPath();
        }
        return $this;
    }

    /**
     * Reindex category permissions on category move event
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Adminhtml\Observer
     */
    public function reindexCategoryPermissionOnMove(\Magento\Event\Observer $observer)
    {
        $category = \Mage::getModel('Magento\Catalog\Model\Category')
            ->load($observer->getEvent()->getCategoryId());
        $this->_indexQueue[] = $category->getPath();
        return $this;
    }

    /**
     * Reindex permissions in queue on postdipatch
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\CatalogPermissions\Model\Adminhtml\Observer
     */
    public function reindexPermissions()
    {
        if (!empty($this->_indexQueue)) {
            /** @var $indexer \Magento\Index\Model\Indexer */
            $indexer = \Mage::getSingleton('Magento\Index\Model\Indexer');
            foreach ($this->_indexQueue as $item) {
                $indexer->logEvent(
                    new \Magento\Object(array('id' => $item)),
                    \Magento\CatalogPermissions\Model\Permission\Index::ENTITY_CATEGORY,
                    \Magento\CatalogPermissions\Model\Permission\Index::EVENT_TYPE_REINDEX_PRODUCTS
                );
            }
            $this->_indexQueue = array();
            $indexer->indexEvents(
                \Magento\CatalogPermissions\Model\Permission\Index::ENTITY_CATEGORY,
                \Magento\CatalogPermissions\Model\Permission\Index::EVENT_TYPE_REINDEX_PRODUCTS
            );
            \Mage::app()->cleanCache(array(\Magento\Catalog\Model\Category::CACHE_TAG));
        }

        if (!empty($this->_indexProductQueue)) {
            /** @var $indexer \Magento\Index\Model\Indexer */
            $indexer = \Mage::getSingleton('Magento\Index\Model\Indexer');
            foreach ($this->_indexProductQueue as $item) {
                $indexer->logEvent(
                    new \Magento\Object(array('id' => $item)),
                    \Magento\CatalogPermissions\Model\Permission\Index::ENTITY_PRODUCT,
                    \Magento\CatalogPermissions\Model\Permission\Index::EVENT_TYPE_REINDEX_PRODUCTS
                );
            }
            $indexer->indexEvents(
                \Magento\CatalogPermissions\Model\Permission\Index::ENTITY_PRODUCT,
                \Magento\CatalogPermissions\Model\Permission\Index::EVENT_TYPE_REINDEX_PRODUCTS
            );
            $this->_indexProductQueue = array();
        }

        return $this;
    }

    /**
     * Refresh category related cache on catalog permissions config save
     *
     * @return \Magento\CatalogPermissions\Model\Adminhtml\Observer
     */
    public function cleanCacheOnConfigChange()
    {
        \Mage::app()->cleanCache(array(\Magento\Catalog\Model\Category::CACHE_TAG));
        \Mage::getSingleton('Magento\Index\Model\Indexer')->processEntityAction(
            new \Magento\Object(),
            \Magento\CatalogPermissions\Model\Permission\Index::ENTITY_CONFIG,
            \Magento\Index\Model\Event::TYPE_SAVE
        );
        return $this;
    }

    /**
     * Rebuild index for products
     *
     * @return  \Magento\CatalogPermissions\Model\Adminhtml\Observer
     */
    public function reindexProducts()
    {
        $this->_indexProductQueue[] = null;
        return $this;
    }

    /**
     * Rebuild index
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\CatalogPermissions\Model\Adminhtml\Observer
     */
    public function reindex()
    {
        $this->_indexQueue[] = '1';
        return $this;
    }

    /**
     * Rebuild index after product assigned websites
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\CatalogPermissions\Model\Adminhtml\Observer
     */
    public function reindexAfterProductAssignedWebsite(\Magento\Event\Observer $observer)
    {
        $productIds = $observer->getEvent()->getProducts();
        $this->_indexProductQueue = array_merge($this->_indexProductQueue, $productIds);
        return $this;
    }


    /**
     * Save product permission index
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\CatalogPermissions\Model\Adminhtml\Observer
     */
    public function saveProductPermissionIndex(\Magento\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_indexProductQueue[] = $product->getId();
        return $this;
    }

    /**
     * Add permission tab on category edit page
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CatalogPermissions\Model\Adminhtml\Observer
     */
    public function addCategoryPermissionTab(\Magento\Event\Observer $observer)
    {
        if (!\Mage::helper('Magento\CatalogPermissions\Helper\Data')->isEnabled()) {
            return $this;
        }
        if (!$this->_authorization->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions')) {
            return $this;
        }

        $tabs = $observer->getEvent()->getTabs();
        /* @var $tabs \Magento\Adminhtml\Block\Catalog\Category\Tabs */

        //if (\Mage::helper('Magento\CatalogPermissions\Helper\Data')->isAllowedCategory($tabs->getCategory())) {
            $tabs->addTab(
                'permissions',
                '\Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions'
            );
        //}

        return $this;
    }

    /**
     * Apply categories and products permissions after reindex category products
     *
     * @param \Magento\Event\Observer $observer
     */
    public function applyPermissionsAfterReindex(\Magento\Event\Observer $observer)
    {
        \Mage::getSingleton('Magento\Index\Model\Indexer')->getProcessByCode('catalogpermissions')->reindexEverything();
    }
}
