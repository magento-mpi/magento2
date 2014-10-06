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
    array('_coreConfig', 'Magento\CatalogPermissions\App\Backend\Config', 'coreConfig'),
    array('_scopeConfig', 'Magento\CatalogPermissions\App\Config', 'scopeConfig'),
    array('_scopeConfig', 'Magento\CatalogPermissions\Helper\Data', 'config'),
    array('_customerSession', 'Magento\CatalogPermissions\Helper\Data', 'customerSession'),
    array('_storeIds', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array('_insertData', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array('_tableFields', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array(
        '_permissionCache',
        'Magento\CatalogPermissions\Model\Resource\Permission\Index',
        'Magento\CatalogPermissions\Model\Indexer\AbstractAction::indexCategoryPermissions'
    ),
    array(
        '_grantsInheritance',
        'Magento\CatalogPermissions\Model\Resource\Permission\Index',
        'Magento\CatalogPermissions\Model\Indexer\AbstractAction::grantsInheritance'
    ),
    array('_storeManager', 'Magento\CatalogPermissions\Model\Resource\Permission\Index', 'storeManager'),
    array('_scopeConfig', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array('_websiteCollectionFactory', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array('_groupCollectionFactory', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array('_catalogPermData', 'Magento\CatalogPermissions\Model\Resource\Permission\Index', 'helper'),
    array('_matchedEntities', 'Magento\CatalogPermissions\Model\Permission\Index'),
    array('_isVisible', 'Magento\CatalogPermissions\Model\Permission\Index'),
    ['_transportBuilder', 'Magento\Rma\Model\Rma'],
    ['_rmaConfig', 'Magento\Rma\Model\Rma'],
    ['_historyFactory', 'Magento\Rma\Model\Rma'],
    ['_inlineTranslation', 'Magento\Rma\Model\Rma'],
    [
        '_searchedEntityIds',
        'Magento\Search\Model\Resource\Collection',
        'Magento\Search\Model\Resource\Collection::foundEntityIds'
    ],
    ['indexerFactory', 'Magento\Search\Model\Observer'],
    ['_coreRegistry', 'Magento\Search\Model\Observer'],
    ['_engineProvider', 'Magento\Search\Model\Observer'],
);
