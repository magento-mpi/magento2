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
    array('_filterIndexData', 'Magento_Search_Model_Adapter_Abstract'),
    array('getSearchTextFields', 'Magento_Search_Model_Adapter_Abstract'),
    array('addAppliedRuleFilter', 'Magento_Banner_Model_Resource_Catalogrule_Collection'),
    array('addBannersFilter', 'Magento_Banner_Model_Resource_Catalogrule_Collection'),
    array('addBannersFilter', 'Magento_Banner_Model_Resource_Salesrule_Collection'),
    array('addCategoryFilter', 'Magento_Search_Model_Catalog_Layer_Filter_Category'),
    array('addCustomerSegmentFilter', 'Magento_Banner_Model_Resource_Catalogrule_Collection'),
    array('addCustomerSegmentFilter', 'Magento_Banner_Model_Resource_Salesrule_Collection'),
    array('addFieldsToBannerForm', 'Magento_CustomerSegment_Model_Observer'),
    array('setModelName', 'Magento_Logging_Model_Event_Changes'),
    array('getModelName', 'Magento_Logging_Model_Event_Changes'),
    array('setModelId', 'Magento_Logging_Model_Event_Changes'),
    array('getModelId', 'Magento_Logging_Model_Event_Changes'),
    array('_initAction', 'Magento_AdvancedCheckout_Controller_Adminhtml_Checkout'),
    array('getEventData', 'Magento_Logging_Block_Adminhtml_Container'),
    array('getEventXForwardedIp', 'Magento_Logging_Block_Adminhtml_Container'),
    array('getEventIp', 'Magento_Logging_Block_Adminhtml_Container'),
    array('getEventError', 'Magento_Logging_Block_Adminhtml_Container'),
    array('postDispatchSystemStoreSave', 'Magento_Logging_Model_Handler_Controllers'),
    array('getUrls', 'Magento_FullPageCache_Model_Crawler'),
    array('getUrlStmt', 'Magento_FullPageCache_Model_Resource_Crawler'),
    array('_getLinkCollection', 'Magento_TargetRule_Block_Checkout_Cart_Crosssell'),
    array('getCustomerSegments', 'Magento_CustomerSegment_Model_Resource_Customer'),
    array('getRequestUri', 'Magento_FullPageCache_Model_Processor_Default'),
    array('_getActiveEntity', 'Magento_GiftRegistry_Controller_Index'),
    array('getActiveEntity', 'Magento_GiftRegistry_Model_Entity'),
    array('_convertDateTime', 'Magento_CatalogEvent_Model_Event'),
    array('updateStatus', 'Magento_CatalogEvent_Model_Event'),
    array('getStateText', 'Magento_GiftCardAccount_Model_Giftcardaccount'),
    array('getStoreContent', 'Magento_Banner_Model_Banner'),
    array('_searchSuggestions', 'Magento_Search_Model_Adapter_HttpStream'),
    array('_searchSuggestions', 'Magento_Search_Model_Adapter_PhpExtension'),
    array('updateCategoryIndexData', 'Magento_Search_Model_Resource_Index'),
    array('updatePriceIndexData', 'Magento_Search_Model_Resource_Index'),
    array('_changeIndexesStatus', 'Magento_Search_Model_Indexer_Indexer'),
    array('cmsPageBlockLoadAfter', 'Magento_AdminGws_Model_Models'),
    array('applyEventStatus', 'Magento_CatalogEvent_Model_Observer'),
    array('checkQuoteItem', 'Magento_CatalogPermissions_Model_Observer'),
    array('increaseOrderInvoicedAmount', 'Magento_GiftCardAccount_Model_Observer'),
    array('initRewardType', 'Magento_Reward_Block_Tooltip'),
    array('initRewardType', 'Magento_Reward_Block_Tooltip_Checkout'),
    array('blockCreateAfter', 'Magento_FullPageCache_Model_Observer'),
    array('_checkViewedProducts', 'Magento_FullPageCache_Model_Observer'),
    array('invoiceSaveAfter', 'Magento_Reward_Model_Observer'),
    array('_calcMinMax', 'Magento_GiftCard_Block_Catalog_Product_Price'),
    array('_getAmounts', 'Magento_GiftCard_Block_Catalog_Product_Price'),
    array('searchSuggestions', 'Magento_Search_Model_Client_Solr'),
    array('_registerProductsView', 'Magento_FullPageCache_Model_Container_Viewedproducts'),
    array('_getForeignKeyName', 'Magento_DB_Adapter_Oracle'),
    array('getCacheInstance', 'Magento_FullPageCache_Model_Cache'),
    array('saveCustomerSegments', 'Magento_Banner_Model_Resource_Banner'),
    array('saveOptions', 'Magento_FullPageCache_Model_Cache'),
    array('refreshRequestIds', 'Magento_FullPageCache_Model_Processor',
        'Magento_FullPageCache_Model_Request_Identifier::refreshRequestIds'
    ),
    array('removeCartLink', 'Magento_PersistentHistory_Model_Observer'),
    array('resetColumns', 'Magento_Banner_Model_Resource_Salesrule_Collection'),
    array('resetSelect', 'Magento_Banner_Model_Resource_Catalogrule_Collection'),
    array('prepareCacheId', 'Magento_FullPageCache_Model_Processor',
        'Magento_FullPageCache_Model_Request_Identifier::prepareCacheId'
    ),
    array('_getQuote', 'Magento_AdvancedCheckout_Block_Adminhtml_Manage_Form_Coupon',
        'Magento_AdvancedCheckout_Block_Adminhtml_Manage_Form_Coupon::getQuote()'
    ),
    array('_getQuote', 'Magento_GiftCardAccount_Block_Checkout_Cart_Total',
        'Magento_GiftCardAccount_Block_Checkout_Cart_Total::getQuote()'
    ),
    array('_getQuote', 'Magento_GiftCardAccount_Block_Checkout_Onepage_Payment_Additional',
        'Magento_GiftCardAccount_Block_Checkout_Onepage_Payment_Additional::getQuote()'
    ),
    array('_getQuote', 'Magento_GiftWrapping_Block_Checkout_Options',
        'Magento_GiftWrapping_Block_Checkout_Options::getQuote()'
    ),
    array('addCustomerSegmentRelationsToCollection', 'Magento_TargetRule_Model_Resource_Rule'),
    array('_getRuleProductsTable', 'Magento_TargetRule_Model_Resource_Rule'),
    array('getCustomerSegmentRelations', 'Magento_TargetRule_Model_Resource_Rule'),
    array('_saveCustomerSegmentRelations', 'Magento_TargetRule_Model_Resource_Rule'),
    array('_prepareRuleProducts', 'Magento_TargetRule_Model_Resource_Rule'),
    array('getInetNtoaExpr', 'Magento_Logging_Model_Resource_Helper_Mysql4'),
);
