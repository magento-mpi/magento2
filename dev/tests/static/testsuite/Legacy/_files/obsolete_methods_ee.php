<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
     $this->_getRule('_filterIndexData', 'Enterprise_Search_Model_Adapter_Abstract'),
     $this->_getRule('getSearchTextFields', 'Enterprise_Search_Model_Adapter_Abstract'),
     $this->_getRule('addCategoryFilter', 'Enterprise_Search_Model_Catalog_Layer_Filter_Category'),
     $this->_getRule('setModelName', 'Enterprise_Logging_Model_Event_Changes'),
     $this->_getRule('getModelName', 'Enterprise_Logging_Model_Event_Changes'),
     $this->_getRule('setModelId', 'Enterprise_Logging_Model_Event_Changes'),
     $this->_getRule('getModelId', 'Enterprise_Logging_Model_Event_Changes'),
     $this->_getRule('_initAction', 'Enterprise_Checkout_Adminhtml_CheckoutController'),
     $this->_getRule('getEventData', 'Enterprise_Logging_Block_Adminhtml_Container'),
     $this->_getRule('getEventXForwardedIp', 'Enterprise_Logging_Block_Adminhtml_Container'),
     $this->_getRule('getEventIp', 'Enterprise_Logging_Block_Adminhtml_Container'),
     $this->_getRule('getEventError', 'Enterprise_Logging_Block_Adminhtml_Container'),
     $this->_getRule('postDispatchSystemStoreSave', 'Enterprise_Logging_Model_Handler_Controllers'),
     $this->_getRule('getUrls', 'Enterprise_PageCache_Model_Crawler'),
     $this->_getRule('getUrlStmt', 'Enterprise_PageCache_Model_Resource_Crawler'),
     $this->_getRule('_getLinkCollection', 'Enterprise_TargetRule_Block_Checkout_Cart_Crosssell'),
     $this->_getRule('getCustomerSegments', 'Enterprise_CustomerSegment_Model_Resource_Customer'),
     $this->_getRule('getRequestUri', 'Enterprise_PageCache_Model_Processor_Default'),
     $this->_getRule('_getActiveEntity', 'Enterprise_GiftRegistry_IndexController'),
     $this->_getRule('getActiveEntity', 'Enterprise_GiftRegistry_Model_Entity'),
     $this->_getRule('_convertDateTime', 'Enterprise_CatalogEvent_Model_Event'),
     $this->_getRule('updateStatus', 'Enterprise_CatalogEvent_Model_Event'),
     $this->_getRule('getStateText', 'Enterprise_GiftCardAccount_Model_Giftcardaccount'),
     $this->_getRule('_searchSuggestions', 'Enterprise_Search_Model_Adapter_HttpStream'),
     $this->_getRule('_searchSuggestions', 'Enterprise_Search_Model_Adapter_PhpExtension'),
     $this->_getRule('updateCategoryIndexData', 'Enterprise_Search_Model_Resource_Index'),
     $this->_getRule('updatePriceIndexData', 'Enterprise_Search_Model_Resource_Index'),
     $this->_getRule('_changeIndexesStatus', 'Enterprise_Search_Model_Indexer_Indexer'),
     $this->_getRule('cmsPageBlockLoadAfter', 'Enterprise_AdminGws_Model_Models'),
     $this->_getRule('applyEventStatus', 'Enterprise_CatalogEvent_Model_Observer'),
     $this->_getRule('checkQuoteItem', 'Enterprise_CatalogPermissions_Model_Observer'),
     $this->_getRule('increaseOrderInvoicedAmount', 'Enterprise_GiftCardAccount_Model_Observer'),
     $this->_getRule('blockCreateAfter', 'Enterprise_PageCache_Model_Observer'),
     $this->_getRule('_checkViewedProducts', 'Enterprise_PageCache_Model_Observer'),
     $this->_getRule('invoiceSaveAfter', 'Enterprise_Reward_Model_Observer'),
     $this->_getRule('_calcMinMax', 'Enterprise_GiftCard_Block_Catalog_Product_Price'),
     $this->_getRule('_getAmounts', 'Enterprise_GiftCard_Block_Catalog_Product_Price'),
     $this->_getRule('searchSuggestions', 'Enterprise_Search_Model_Client_Solr'),
     $this->_getRule('_registerProductsView', 'Enterprise_PageCache_Model_Container_Viewedproducts'),
     $this->_getRule('_getForeignKeyName', 'Varien_Db_Adapter_Oracle'),
);
