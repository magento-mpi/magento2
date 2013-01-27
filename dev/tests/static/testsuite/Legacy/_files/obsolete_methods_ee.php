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
     array('addCategoryFilter', 'Enterprise_Search_Model_Catalog_Layer_Filter_Category'),
     array('setModelName', 'Enterprise_Logging_Model_Event_Changes'),
     array('getModelName', 'Enterprise_Logging_Model_Event_Changes'),
     array('setModelId', 'Enterprise_Logging_Model_Event_Changes'),
     array('getModelId', 'Enterprise_Logging_Model_Event_Changes'),
     array('_initAction', 'Enterprise_Checkout_Adminhtml_CheckoutController'),
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
     array('_getActiveEntity', 'Enterprise_GiftRegistry_IndexController'),
     array('getActiveEntity', 'Enterprise_GiftRegistry_Model_Entity'),
     array('_convertDateTime', 'Enterprise_CatalogEvent_Model_Event'),
     array('updateStatus', 'Enterprise_CatalogEvent_Model_Event'),
     array('getStateText', 'Enterprise_GiftCardAccount_Model_Giftcardaccount'),
     array('_searchSuggestions', 'Enterprise_Search_Model_Adapter_HttpStream'),
     array('_searchSuggestions', 'Enterprise_Search_Model_Adapter_PhpExtension'),
     array('updateCategoryIndexData', 'Enterprise_Search_Model_Resource_Index'),
     array('updatePriceIndexData', 'Enterprise_Search_Model_Resource_Index'),
     array('_changeIndexesStatus', 'Enterprise_Search_Model_Indexer_Indexer'),
     array('cmsPageBlockLoadAfter', 'Enterprise_AdminGws_Model_Models'),
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
     array('_getForeignKeyName', 'Varien_Db_Adapter_Oracle'),
);
