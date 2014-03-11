<?php
/**
 * Same as obsolete_properties.php, but specific to Magento EE
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    array('_eventData', 'Magento\Logging\Block\Adminhtml\Container'),
    array('_customerSegments', 'Magento\CustomerSegment\Model\Customer'),
    array('_limit', 'Magento\Search\Model\Resource\Index'),
    array('_amountCache', 'Magento\GiftCard\Block\Catalog\Product\Price'),
    array('_minMaxCache', 'Magento\GiftCard\Block\Catalog\Product\Price'),
    array('_skipFields', 'Magento\Logging\Model\Processor'),
    array('_layoutUpdate', 'Magento\WebsiteRestriction\Controller\Index'),
    array('_importExportConfig', 'Magento\ScheduledImportExport\Model\Scheduled\Operation\Data'),
    array('_importModel', 'Magento\ScheduledImportExport\Model\Scheduled\Operation\Data'),
    array('_coreUrl', 'Magento\FullPageCache\Model\Observer'),
    array('_coreSession', 'Magento\FullPageCache\Model\Observer'),
    array('_application', 'Magento\FullPageCache\Model\Observer'),
    array('_app', 'Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Content'),
    array('_backendSession', 'Magento\AdvancedCheckout\Block\Adminhtml\Manage\Messages', 'backendSession'),
    array('_coreMessage', 'Magento\AdvancedCheckout\Model\Cart', 'messageFactory'),
    ['_coreConfig', 'Magento\CatalogPermissions\App\Backend\Config', 'coreConfig'],
    ['_coreStoreConfig', 'Magento\CatalogPermissions\App\Config', 'coreStoreConfig'],
    ['_coreStoreConfig', 'Magento\CatalogPermissions\Helper\Data', 'config'],
    ['_customerSession', 'Magento\CatalogPermissions\Helper\Data', 'customerSession'],
    ['_storeIds', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'],
    ['_insertData', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'],
    ['_tableFields', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'],
    ['_permissionCache', 'Magento\CatalogPermissions\Model\Resource\Permission\Index',
        'Magento\CatalogPermissions\Model\Indexer\AbstractAction::indexCategoryPermissions'],
    ['_grantsInheritance', 'Magento\CatalogPermissions\Model\Resource\Permission\Index',
        'Magento\CatalogPermissions\Model\Indexer\AbstractAction::grantsInheritance'],
    ['_storeManager', 'Magento\CatalogPermissions\Model\Resource\Permission\Index', 'storeManager'],
    ['_coreStoreConfig', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'],
    ['_websiteCollectionFactory', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'],
    ['_groupCollectionFactory', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'],
    ['_catalogPermData', 'Magento\CatalogPermissions\Model\Resource\Permission\Index', 'helper'],
    ['_matchedEntities', 'Magento\CatalogPermissions\Model\Permission\Index'],
    ['_isVisible', 'Magento\CatalogPermissions\Model\Permission\Index'],
);
