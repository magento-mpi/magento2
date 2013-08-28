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
    array('_filterIndexData', 'Enterprise_Search_Model_Adapter_Abstract'),
    array('getSearchTextFields', 'Enterprise_Search_Model_Adapter_Abstract'),
    array('addAppliedRuleFilter', 'Enterprise_Banner_Model_Resource_Catalogrule_Collection'),
    array('addBannersFilter', 'Enterprise_Banner_Model_Resource_Catalogrule_Collection'),
    array('addBannersFilter', 'Enterprise_Banner_Model_Resource_Salesrule_Collection'),
    array('addCategoryFilter', 'Enterprise_Search_Model_Catalog_Layer_Filter_Category'),
    array('addCustomerSegmentFilter', 'Enterprise_Banner_Model_Resource_Catalogrule_Collection'),
    array('addCustomerSegmentFilter', 'Enterprise_Banner_Model_Resource_Salesrule_Collection'),
    array('addFieldsToBannerForm', 'Enterprise_CustomerSegment_Model_Observer'),
    array('setModelName', 'Enterprise_Logging_Model_Event_Changes'),
    array('getModelName', 'Enterprise_Logging_Model_Event_Changes'),
    array('setModelId', 'Enterprise_Logging_Model_Event_Changes'),
    array('getModelId', 'Enterprise_Logging_Model_Event_Changes'),
    array('_initAction', 'Enterprise_Checkout_Controller_Adminhtml_Checkout'),
    array('getEventData', 'Enterprise_Logging_Block_Adminhtml_Container'),
    array('getEventXForwardedIp', 'Enterprise_Logging_Block_Adminhtml_Container'),
    array('getEventIp', 'Enterprise_Logging_Block_Adminhtml_Container'),
    array('getEventError', 'Enterprise_Logging_Block_Adminhtml_Container'),
    array('postDispatchSystemStoreSave', 'Enterprise_Logging_Model_Handler_Controllers'),
    array('getUrls', 'Enterprise_PageCache_Model_Crawler'),
    array('getUrlStmt', 'Enterprise_PageCache_Model_Resource_Crawler'),
    array('_getLinkCollection', 'Enterprise_TargetRule_Block_Checkout_Cart_Crosssell'),
    array('getCustomerSegments', 'Enterprise_CustomerSegment_Model_Resource_Customer'),
    array('getRequestUri', 'Enterprise_PageCache_Model_Processor_Default'),
    array('_getActiveEntity', 'Enterprise_GiftRegistry_Controller_Index'),
    array('getActiveEntity', 'Enterprise_GiftRegistry_Model_Entity'),
    array('_convertDateTime', 'Enterprise_CatalogEvent_Model_Event'),
    array('updateStatus', 'Enterprise_CatalogEvent_Model_Event'),
    array('getStateText', 'Enterprise_GiftCardAccount_Model_Giftcardaccount'),
    array('getStoreContent', 'Enterprise_Banner_Model_Banner'),
    array('_searchSuggestions', 'Enterprise_Search_Model_Adapter_HttpStream'),
    array('_searchSuggestions', 'Enterprise_Search_Model_Adapter_PhpExtension'),
    array('updateCategoryIndexData', 'Enterprise_Search_Model_Resource_Index'),
    array('updatePriceIndexData', 'Enterprise_Search_Model_Resource_Index'),
    array('_changeIndexesStatus', 'Enterprise_Search_Model_Indexer_Indexer'),
    array('cmsPageBlockLoadAfter', 'Magento_AdminGws_Model_Models'),
    array('applyEventStatus', 'Enterprise_CatalogEvent_Model_Observer'),
    array('checkQuoteItem', 'Enterprise_CatalogPermissions_Model_Observer'),
    array('increaseOrderInvoicedAmount', 'Enterprise_GiftCardAccount_Model_Observer'),
    array('blockCreateAfter', 'Enterprise_PageCache_Model_Observer'),
    array('_checkViewedProducts', 'Enterprise_PageCache_Model_Observer'),
    array('invoiceSaveAfter', 'Enterprise_Reward_Model_Observer'),
    array('_calcMinMax', 'Enterprise_GiftCard_Block_Catalog_Product_Price'),
    array('_getAmounts', 'Enterprise_GiftCard_Block_Catalog_Product_Price'),
    array('searchSuggestions', 'Enterprise_Search_Model_Client_Solr'),
    array('_registerProductsView', 'Enterprise_PageCache_Model_Container_Viewedproducts'),
    array('_getForeignKeyName', 'Magento_DB_Adapter_Oracle'),
    array('getCacheInstance', 'Enterprise_PageCache_Model_Cache'),
    array('saveCustomerSegments', 'Enterprise_Banner_Model_Resource_Banner'),
    array('saveOptions', 'Enterprise_PageCache_Model_Cache'),
    array('refreshRequestIds', 'Enterprise_PageCache_Model_Processor',
        'Enterprise_PageCache_Model_Request_Identifier::refreshRequestIds'
    ),
    array('resetColumns', 'Enterprise_Banner_Model_Resource_Salesrule_Collection'),
    array('resetSelect', 'Enterprise_Banner_Model_Resource_Catalogrule_Collection'),
    array('prepareCacheId', 'Enterprise_PageCache_Model_Processor',
        'Enterprise_PageCache_Model_Request_Identifier::prepareCacheId'
    ),
    array('_getQuote', 'Enterprise_Checkout_Block_Adminhtml_Manage_Form_Coupon',
        'Enterprise_Checkout_Block_Adminhtml_Manage_Form_Coupon::getQuote()'
    ),
    array('_getQuote', 'Enterprise_GiftCardAccount_Block_Checkout_Cart_Total',
        'Enterprise_GiftCardAccount_Block_Checkout_Cart_Total::getQuote()'
    ),
    array('_getQuote', 'Enterprise_GiftCardAccount_Block_Checkout_Onepage_Payment_Additional',
        'Enterprise_GiftCardAccount_Block_Checkout_Onepage_Payment_Additional::getQuote()'
    ),
    array('_getQuote', 'Enterprise_GiftWrapping_Block_Checkout_Options',
        'Enterprise_GiftWrapping_Block_Checkout_Options::getQuote()'
    ),
    array('addCustomerSegmentRelationsToCollection', 'Enterprise_TargetRule_Model_Resource_Rule'),
    array('_getRuleProductsTable', 'Enterprise_TargetRule_Model_Resource_Rule'),
    array('getCustomerSegmentRelations', 'Enterprise_TargetRule_Model_Resource_Rule'),
    array('_saveCustomerSegmentRelations', 'Enterprise_TargetRule_Model_Resource_Rule'),
    array('_prepareRuleProducts', 'Enterprise_TargetRule_Model_Resource_Rule'),
    array('getInetNtoaExpr', 'Enterprise_Logging_Model_Resource_Helper_Mysql4'),
);
