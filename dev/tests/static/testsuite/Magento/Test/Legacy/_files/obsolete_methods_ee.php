<?php
/**
 * Same as obsolete_methods.php, but specific to Magento EE
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    array('_filterIndexData', 'Magento\Solr\Model\Adapter\AbstractAdapter'),
    array('getSearchTextFields', 'Magento\Solr\Model\Adapter\AbstractAdapter'),
    array('addAppliedRuleFilter', 'Magento\Banner\Model\Resource\Catalogrule\Collection'),
    array('addBannersFilter', 'Magento\Banner\Model\Resource\Catalogrule\Collection'),
    array('addBannersFilter', 'Magento\Banner\Model\Resource\Salesrule\Collection'),
    array('addCategoryFilter', 'Magento\Solr\Model\Catalog\Layer\Filter\Category'),
    array('addCustomerSegmentFilter', 'Magento\Banner\Model\Resource\Catalogrule\Collection'),
    array('addCustomerSegmentFilter', 'Magento\Banner\Model\Resource\Salesrule\Collection'),
    array('addDashboardLink', 'Magento\Rma\Block\Link'),
    array('addFieldsToBannerForm', 'Magento\CustomerSegment\Model\Observer'),
    array('addRenderer', 'Magento\CustomAttributeManagement\Block\Form'),
    array('setModelName', 'Magento\Logging\Model\Event\Changes'),
    array('getModelName', 'Magento\Logging\Model\Event\Changes'),
    array('setModelId', 'Magento\Logging\Model\Event\Changes'),
    array('getModelId', 'Magento\Logging\Model\Event\Changes'),
    array('_initAction', 'Magento\AdvancedCheckout\Controller\Adminhtml\Checkout'),
    array('getEventData', 'Magento\Logging\Block\Adminhtml\Container'),
    array('getEventXForwardedIp', 'Magento\Logging\Block\Adminhtml\Container'),
    array('getEventIp', 'Magento\Logging\Block\Adminhtml\Container'),
    array('getEventError', 'Magento\Logging\Block\Adminhtml\Container'),
    array('postDispatchSystemStoreSave', 'Magento\Logging\Model\Handler\Controllers'),
    array('getUrls', 'Magento\FullPageCache\Model\Crawler'),
    array('getUrlStmt', 'Magento\FullPageCache\Model\Resource\Crawler'),
    array('_getLinkCollection', 'Magento\TargetRule\Block\Checkout\Cart\Crosssell'),
    array('getCustomerSegments', 'Magento\CustomerSegment\Model\Resource\Customer'),
    array('getRequestUri', 'Magento\FullPageCache\Model\Processor\DefaultProcessor'),
    array('_getActiveEntity', 'Magento\GiftRegistry\Controller\Index'),
    array('getActiveEntity', 'Magento\GiftRegistry\Model\Entity'),
    array('_convertDateTime', 'Magento\CatalogEvent\Model\Event'),
    array('updateStatus', 'Magento\CatalogEvent\Model\Event'),
    array('getStateText', 'Magento\GiftCardAccount\Model\Giftcardaccount'),
    array('getStoreContent', 'Magento\Banner\Model\Banner'),
    array('_searchSuggestions', 'Magento\Solr\Model\Adapter\HttpStream'),
    array('_searchSuggestions', 'Magento\Solr\Model\Adapter\PhpExtension'),
    array('updateCategoryIndexData', 'Magento\Solr\Model\Resource\Index'),
    array('updatePriceIndexData', 'Magento\Solr\Model\Resource\Index'),
    array('_changeIndexesStatus', 'Magento\Solr\Model\Indexer\Indexer'),
    array('cmsPageBlockLoadAfter', 'Magento\AdminGws\Model\Models'),
    array('applyEventStatus', 'Magento\CatalogEvent\Model\Observer'),
    array('checkQuoteItem', 'Magento\CatalogPermissions\Model\Observer'),
    array('increaseOrderInvoicedAmount', 'Magento\GiftCardAccount\Model\Observer'),
    array('initRewardType', 'Magento\Reward\Block\Tooltip'),
    array('initRewardType', 'Magento\Reward\Block\Tooltip\Checkout'),
    array('blockCreateAfter', 'Magento\FullPageCache\Model\Observer'),
    array('_checkViewedProducts', 'Magento\FullPageCache\Model\Observer'),
    array('invoiceSaveAfter', 'Magento\Reward\Model\Observer'),
    array('_calcMinMax', 'Magento\GiftCard\Block\Catalog\Product\Price'),
    array('_getAmounts', 'Magento\GiftCard\Block\Catalog\Product\Price'),
    array('searchSuggestions', 'Magento\Solr\Model\Client\Solr'),
    array('_registerProductsView', 'Magento\FullPageCache\Model\Container\Viewedproducts'),
    array('_getForeignKeyName', 'Magento\Framework\DB\Adapter\Oracle'),
    array('getCacheInstance', 'Magento\FullPageCache\Model\Cache'),
    array('saveCustomerSegments', 'Magento\Banner\Model\Resource\Banner'),
    array('saveOptions', 'Magento\FullPageCache\Model\Cache'),
    array(
        'refreshRequestIds',
        'Magento\FullPageCache\Model\Processor',
        'Magento_FullPageCache_Model_Request_Identifier::refreshRequestIds'
    ),
    array('removeCartLink', 'Magento\PersistentHistory\Model\Observer'),
    array('resetColumns', 'Magento\Banner\Model\Resource\Salesrule\Collection'),
    array('resetSelect', 'Magento\Banner\Model\Resource\Catalogrule\Collection'),
    array(
        'prepareCacheId',
        'Magento\FullPageCache\Model\Processor',
        'Magento_FullPageCache_Model_Request_Identifier::prepareCacheId'
    ),
    array(
        '_getQuote',
        'Magento\AdvancedCheckout\Block\Adminhtml\Manage\Form\Coupon',
        'Magento_AdvancedCheckout_Block_Adminhtml_Manage_Form_Coupon::getQuote()'
    ),
    array(
        '_getQuote',
        'Magento\GiftCardAccount\Block\Checkout\Cart\Total',
        'Magento_GiftCardAccount_Block_Checkout_Cart_Total::getQuote()'
    ),
    array(
        '_getQuote',
        'Magento\GiftCardAccount\Block\Checkout\Onepage\Payment\Additional',
        'Magento_GiftCardAccount_Block_Checkout_Onepage_Payment_Additional::getQuote()'
    ),
    array(
        '_getQuote',
        'Magento\GiftWrapping\Block\Checkout\Options',
        'Magento_GiftWrapping_Block_Checkout_Options::getQuote()'
    ),
    array('addCustomerSegmentRelationsToCollection', 'Magento\TargetRule\Model\Resource\Rule'),
    array('_getRuleProductsTable', 'Magento\TargetRule\Model\Resource\Rule'),
    array('getCustomerSegmentRelations', 'Magento\TargetRule\Model\Resource\Rule'),
    array('setCustomerSegmentRelations', 'Magento\TargetRule\Model\Resource\Rule'),
    array('_saveCustomerSegmentRelations', 'Magento\TargetRule\Model\Resource\Rule'),
    array('_prepareRuleProducts', 'Magento\TargetRule\Model\Resource\Rule'),
    array('getInetNtoaExpr', 'Magento\Logging\Model\Resource\Helper'),
    array('catalogCategoryIsCatalogPermissionsAllowed', 'Magento\AdminGws\Model\Models'),
    array('catalogCategoryMoveBefore', 'Magento\AdminGws\Model\Models'),
    array('catalogProductActionWithWebsitesAfter', 'Magento\AdminGws\Model\Models'),
    array('restrictCustomerRegistration', 'Magento\Invitation\Model\Observer'),
    array('restrictCustomersRegistration', 'Magento\WebsiteRestriction\Model\Observer'),
    array('checkCategoryPermissions', 'Magento\CatalogPermissions\Model\Adminhtml\Observer'),
    array('chargeById', 'Magento\GiftCardAccount\Model\Observer'),
    array('_helper', 'Magento\GiftRegistry\Model\Entity'),
    array('_getIndexModel', 'Magento\CatalogPermissions\Model\Observer'),
    array('_getConfig', 'Magento\SalesArchive\Model\Resource\Archive'),
    array('_getCart', 'Magento\AdvancedCheckout\Model\Cart'),
    array('getMaxInvitationsPerSend', '\Magento\Invitation\Helper\Data'),
    array('getInvitationRequired', '\Magento\Invitation\Helper\Data'),
    array('getUseInviterGroup', '\Magento\Invitation\Helper\Data'),
    array('isInvitationMessageAllowed', '\Magento\Invitation\Helper\Data'),
    array('isEnabled', '\Magento\Invitation\Helper\Data'),
    array('checkMessages', '\Magento\FullPageCache\Model\Observer'),
    array('appendGiftcardAdditionalData', 'Magento\GiftCard\Model\Observer'),
    array('_getResource', 'Magento\GiftCard\Model\Attribute\Backend\Giftcard\Amount'),
    array('getNode', 'Magento\Logging\Model\Config'),
    array('isActive', 'Magento\Logging\Model\Config'),
    array('_getCallbackFunction', 'Magento\Logging\Model\Processor'),
    array('_getOrderCreateModel', 'Magento\Reward\Block\Adminhtml\Sales\Order\Create\Payment'),
    array(
        'getEntityResourceModel',
        'Magento\SalesArchive\Model\Archive',
        'Magento_SalesArchive_Model_ArchivalList::getResource'
    ),
    array(
        'detectArchiveEntity',
        'Magento\SalesArchive\Model\Archive',
        'Magento_SalesArchive_Model_ArchivalList::getEntityByObject'
    ),
    array('applyIndexChanges', 'Magento\Solr\Model\Observer'),
    array('holdCommit', 'Magento\Solr\Model\Observer'),
    array('getDefaultMenuLayoutCode', 'Magento\VersionsCms\Model\Hierarchy\Config'),
    array('coreBlockAbstractToHtmlBefore', 'Magento\PricePermissions\Model\Observer', 'viewBlockAbstractToHtmlBefore'),
    array(
        'coreBlockAbstractToHtmlBefore',
        'Magento\PromotionPermissions\Model\Observer',
        'viewBlockAbstractToHtmlBefore'
    ),
    array('getServerIoDriver', '\Magento\ScheduledImportExport\Model\Scheduled\Operation'),
    array(
        'addPrivacyHeader',
        '\Magento\Pbridge\Model\Observer',
        '\Magento\Pbridge\App\Action\Plugin\PrivacyHeader::afterDispatch'
    ),
    array('_isConfigured', '\Magento\AdvancedCheckout\Model\Cart'),
    array('_getIsAllowedGrant', 'Magento\CatalogPermissions\Helper\Data', 'isAllowedGrant'),
    array(
        'isEnabled',
        'Magento\CatalogPermissions\Helper\Data',
        'Magento\CatalogPermissions\App\ConfigInterface::isEnabled'
    ),
    array(
        'applyCategoryPermissionOnProductCount',
        'Magento\CatalogPermissions\Model\Observer',
        'applyProductPermissionOnCollection'
    ),
    array(
        'addIndexToProductCount',
        'Magento\CatalogPermissions\Model\Permission\Index',
        'addIndexToProductCollection'
    ),
    array('applyPriceGrantToPriceIndex', 'Magento\CatalogPermissions\Model\Permission\Index'),
    array(
        'addIndexToProductCount',
        'Magento\CatalogPermissions\Model\Resource\Permission\Index',
        'addIndexToProductCollection'
    ),
    array(
        'reindex',
        'Magento\CatalogPermissions\Model\Resource\Permission\Index',
        'Magento\CatalogPermissions\Model\Indexer\AbstractAction::reindex'
    ),
    array('reindexProducts', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array(
        'reindexProductsStandalone',
        'Magento\CatalogPermissions\Model\Resource\Permission\Index',
        'Magento\CatalogPermissions\Model\Indexer\AbstractAction::populateProductIndex'
    ),
    array(
        '_getConfigGrantDbExpr',
        'Magento\CatalogPermissions\Model\Resource\Permission\Index',
        'Magento\CatalogPermissions\Model\Indexer\AbstractAction::getConfigGrantDbExpr'
    ),
    array('_getStoreIds', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array('applyPriceGrantToPriceIndex', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array('_beginInsert', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array('_commitInsert', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array('_insert', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array(
        '_inheritCategoryPermission',
        'Magento\CatalogPermissions\Model\Resource\Permission\Index',
        'Magento\CatalogPermissions\Model\Indexer\AbstractAction::prepareInheritedCategoryIndexPermissions'
    ),
    array(
        'reindexAll',
        'Magento\CatalogPermissions\Model\Resource\Permission\Index',
        'Magento\CatalogPermissions\Model\Indexer\AbstractAction::reindex'
    ),
    array(
        'reindex',
        'Magento\CatalogPermissions\Model\Permission\Index',
        'Magento\CatalogPermissions\Model\Indexer\Category\Action\Rows::execute'
    ),
    array('reindexProducts', 'Magento\CatalogPermissions\Model\Permission\Index'),
    array('getName', 'Magento\CatalogPermissions\Model\Permission\Index'),
    array('_registerEvent', 'Magento\CatalogPermissions\Model\Permission\Index'),
    array('_processEvent', 'Magento\CatalogPermissions\Model\Permission\Index'),
    array(
        'reindexProductsStandalone',
        'Magento\CatalogPermissions\Model\Permission\Index',
        'Magento\CatalogPermissions\Model\Indexer\Product\Action\Rows::execute'
    ),
    array('reindex', 'Magento\CatalogPermissions\Model\Adminhtml\Observer'),
    array('saveCategoryPermissions', 'Magento\CatalogPermissions\Model\Adminhtml\Observer'),
    array('reindexCategoryPermissionOnMove', 'Magento\CatalogPermissions\Model\Adminhtml\Observer'),
    array('reindexPermissions', 'Magento\CatalogPermissions\Model\Adminhtml\Observer'),
    array('reindexAfterProductAssignedWebsite', 'Magento\CatalogPermissions\Model\Adminhtml\Observer'),
    array('saveProductPermissionIndex', 'Magento\CatalogPermissions\Model\Adminhtml\Observer'),
    array('reindexProducts', 'Magento\CatalogPermissions\Model\Adminhtml\Observer'),
    array('getCacheIdTags', 'Magento\CatalogEvent\Model\Event'),
    ['_compareSortOrder', 'Magento\Rma\Block\Returns\Create'],
    ['getTierPriceHtml', 'Magento\AdvancedCheckout\Block\Sku\Products\Info'],
    ['sendNewRmaEmail', 'Magento\Rma\Model\Rma', 'Magento\Rma\Model\Rma\Status\History::sendNewRmaEmail'],
    ['sendAuthorizeEmail', 'Magento\Rma\Model\Rma', 'Magento\Rma\Model\Rma\Status\History::sendAuthorizeEmail'],
    ['_sendRmaEmailWithItems', 'Magento\Rma\Model\Rma', 'Magento\Rma\Model\Rma\Status\History::_sendRmaEmailWithItems'],
    [
        'beforeRebuildIndex',
        'Magento\Solr\Model\Plugin\FulltextIndexRebuild',
        'Magento\Solr\Model\Plugin\FulltextIndexRebuild::beforeExecuteFull'
    ],
    [
        'afterRebuildIndex',
        'Magento\Solr\Model\Plugin\FulltextIndexRebuild',
        'Magento\Solr\Model\Plugin\FulltextIndexRebuild::afterExecuteFull'
    ],
    [
        'reindexAll',
        'Magento\ScheduledImportExport\Model\Import',
        'Magento\ImportExport\Model\Import::invalidateIndex'
    ],
    ['_beforeLoad', 'Magento\Solr\Model\Resource\Collection'],
    ['_afterLoad', 'Magento\Solr\Model\Resource\Collection'],
    ['setEngine', 'Magento\Solr\Model\Resource\Collection'],
    ['customerGroupSaveAfter', 'Magento\Solr\Model\Observer'],
    ['saveStoreIdsBeforeScopeDelete', 'Magento\Solr\Model\Observer'],
    ['clearIndexForStores', 'Magento\Solr\Model\Observer'],
    ['runFulltextReindexAfterPriceReindex', 'Magento\Solr\Model\Observer'],
    ['_beforeLoad', 'Magento\Search\Model\Resource\Collection'],
    ['_afterLoad', 'Magento\Search\Model\Resource\Collection'],
    ['setEngine', 'Magento\Search\Model\Resource\Collection'],
    ['customerGroupSaveAfter', 'Magento\Search\Model\Observer'],
    ['saveStoreIdsBeforeScopeDelete', 'Magento\Search\Model\Observer'],
    ['clearIndexForStores', 'Magento\Search\Model\Observer'],
    ['runFulltextReindexAfterPriceReindex', 'Magento\Search\Model\Observer'],
    ['getDateModel', '\Magento\ScheduledImportExport\Model\Export'],
    ['getDateModel', '\Magento\ScheduledImportExport\Model\Scheduled\Operation'],
    ['modifyExpiredQuotesCleanup', 'Magento\PersistentHistory\Model\Observer'],
    ['loadPrices', 'Magento\Solr\Model\Layer\Category\Filter\Price', 'Magento\Solr\Model\Price\Interval::load'],
    [
        'loadPreviousPrices',
        'Magento\Solr\Model\Layer\Category\Filter\Price',
        'Magento\Solr\Model\Price\Interval::loadPrevious'
    ],
    [
        'loadNextPrices',
        'Magento\Solr\Model\Layer\Category\Filter\Price',
        'Magento\Solr\Model\Price\Interval::loadNext'
    ],
);
