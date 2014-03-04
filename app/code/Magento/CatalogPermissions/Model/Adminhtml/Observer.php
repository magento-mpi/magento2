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

use Magento\App\CacheInterface;
use Magento\AuthorizationInterface;
use Magento\Catalog\Block\Adminhtml\Category\Tabs;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\CatalogPermissions\App\ConfigInterface;
use Magento\CatalogPermissions\Helper\Data;
use Magento\CatalogPermissions\Model\Permission;
use Magento\CatalogPermissions\Model\Permission\Index;
use Magento\Event\Observer as EventObserver;
use Magento\Index\Model\Event;
use Magento\Index\Model\Indexer;

class Observer
{
    const FORM_SELECT_ALL_VALUES = -1;

    /**
     * @var array
     */
    protected $_indexQueue = array();

    /**
     * @var array
     */
    protected $_indexProductQueue = array();

    /**
     * @var AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Catalog permissions data
     *
     * @var Data
     */
    protected $_catalogPermData = null;

    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var Indexer
     */
    protected $_indexer;

    /**
     * @var CacheInterface
     */
    protected $_coreCache;

    /**
     * @var ConfigInterface
     */
    protected $_permissionsConfig;

    /**
     * @param ConfigInterface $permissionsConfig
     * @param CacheInterface $coreCache
     * @param CategoryFactory $categoryFactory
     * @param \Magento\CatalogPermissions\Model\PermissionFactory $permissionFactory
     * @param Indexer $indexer
     * @param Data $catalogPermData
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        ConfigInterface $permissionsConfig,
        CacheInterface $coreCache,
        CategoryFactory $categoryFactory,
        Indexer $indexer,
        Data $catalogPermData,
        AuthorizationInterface $authorization
    ) {
        $this->_permissionsConfig = $permissionsConfig;
        $this->_coreCache = $coreCache;
        $this->_indexer = $indexer;
        $this->_categoryFactory = $categoryFactory;
        $this->_catalogPermData = $catalogPermData;
        $this->_authorization = $authorization;
    }

    /**
     * Refresh category related cache on catalog permissions config save
     *
     * @return $this
     */
    public function cleanCacheOnConfigChange()
    {
        $this->_coreCache->cleanCache(array(Category::CACHE_TAG));
        $this->_indexer->processEntityAction(
            new \Magento\Object(),
            Index::ENTITY_CONFIG,
            Event::TYPE_SAVE
        );
        return $this;
    }

    /**
     * Add permission tab on category edit page
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function addCategoryPermissionTab(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }
        if (!$this->_authorization->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions')) {
            return $this;
        }

        $tabs = $observer->getEvent()->getTabs();
        /* @var $tabs Tabs */

        //if ($this->_catalogPermissionsData->isAllowedCategory($tabs->getCategory())) {
            $tabs->addTab(
                'permissions',
                'Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions'
            );
        //}

        return $this;
    }
}
