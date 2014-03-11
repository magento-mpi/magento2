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

use Magento\AuthorizationInterface;
use Magento\Catalog\Block\Adminhtml\Category\Tabs;
use Magento\Catalog\Model\Category;
use Magento\CatalogPermissions\App\ConfigInterface;
use Magento\Event\Observer as EventObserver;

class Observer
{
    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $appConfig;

    /**
     * @param AuthorizationInterface $authorization
     * @param ConfigInterface $appConfig
     */
    public function __construct(
        AuthorizationInterface $authorization,
        ConfigInterface $appConfig
    ) {
        $this->appConfig = $appConfig;
        $this->authorization = $authorization;
    }

    /**
     * Add permission tab on category edit page
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function addCategoryPermissionTab(EventObserver $observer)
    {
        if (!$this->appConfig->isEnabled()) {
            return $this;
        }
        if (!$this->authorization->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions')) {
            return $this;
        }

        $tabs = $observer->getEvent()->getTabs();
        /* @var $tabs Tabs */

        $tabs->addTab(
            'permissions',
            'Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions'
        );

        return $this;
    }
}
