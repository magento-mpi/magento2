<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

use \Magento\CatalogPermissions\Model\Permission;
use \Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions\Row as PermissionsRow;

class Category
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $appConfig;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $authorization;

    /**
     * @var \Magento\CatalogPermissions\Model\PermissionFactory
     */
    protected $permissionFactory;

    /**
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param \Magento\CatalogPermissions\App\ConfigInterface $appConfig
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\CatalogPermissions\Model\PermissionFactory $permissionFactory
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerInterface $indexer,
        \Magento\CatalogPermissions\App\ConfigInterface $appConfig,
        \Magento\AuthorizationInterface $authorization,
        \Magento\CatalogPermissions\Model\PermissionFactory $permissionFactory
    ) {
        $this->indexer = $indexer;
        $this->appConfig = $appConfig;
        $this->authorization = $authorization;
        $this->permissionFactory = $permissionFactory;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * Save category permissions on category after save
     *
     * @param \Magento\Catalog\Model\Category $subject
     * @return \Magento\Catalog\Model\Category
     */
    public function afterSave(\Magento\Catalog\Model\Category $subject)
    {
        if (!$this->appConfig->isEnabled() || $this->getIndexer()->isScheduled()) {
            return $subject;
        }

        if ($subject->hasData('permissions') && is_array($subject->getData('permissions'))
            && $this->authorization
                ->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions')
        ) {
            foreach ($subject->getData('permissions') as $data) {
                /** @var Permission $permission */
                $permission = $this->permissionFactory->create();
                if (!empty($data['id'])) {
                    $permission->load($data['id']);
                }

                if (!empty($data['_deleted'])) {
                    if ($permission->getId()) {
                        $permission->delete();
                    }
                    continue;
                }

                if ($data['website_id'] == PermissionsRow::FORM_SELECT_ALL_VALUES) {
                    $data['website_id'] = null;
                }

                if ($data['customer_group_id'] == PermissionsRow::FORM_SELECT_ALL_VALUES) {
                    $data['customer_group_id'] = null;
                }

                $permission->addData($data);
                $viewPermission = $permission->getGrantCatalogCategoryView();
                if (Permission::PERMISSION_DENY == $viewPermission) {
                    $permission->setGrantCatalogProductPrice(Permission::PERMISSION_DENY);
                }

                $pricePermission = $permission->getGrantCatalogProductPrice();
                if (Permission::PERMISSION_DENY == $pricePermission) {
                    $permission->setGrantCheckoutItems(Permission::PERMISSION_DENY);
                }
                $permission->setCategoryId($subject->getId());
                $permission->save();
            }

            $this->getIndexer()->reindexRow($subject->getId());
        }

        return $subject;
    }

    /**
     * Reindex category permissions on category move event
     *
     * @param \Magento\Catalog\Model\Category $subject
     * @return \Magento\Catalog\Model\Category
     */
    public function afterMove(\Magento\Catalog\Model\Category $subject)
    {
        if (!$this->appConfig->isEnabled() || $this->getIndexer()->isScheduled()) {
            return $subject;
        }

        $this->getIndexer()->reindexRow($subject->getId());
        return $subject;
    }
}
