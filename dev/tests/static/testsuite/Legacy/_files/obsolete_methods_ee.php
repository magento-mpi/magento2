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
     $this->_addRule('_filterIndexData', 'Enterprise_Search_Model_Adapter_Abstract'),
     $this->_addRule('getSearchTextFields', 'Enterprise_Search_Model_Adapter_Abstract'),
     $this->_addRule('_getCreateSql', 'Enterprise_Staging_Model_Resource_Adapter_Abstract'),
     $this->_addRule('_getFieldSql', 'Enterprise_Staging_Model_Resource_Adapter_Abstract'),
     $this->_addRule('_getKeySql', 'Enterprise_Staging_Model_Resource_Adapter_Abstract'),
     $this->_addRule('_getFlatConstraintSql', 'Enterprise_Staging_Model_Resource_Adapter_Abstract'),
     $this->_addRule('_getConstraintSql', 'Enterprise_Staging_Model_Resource_Adapter_Abstract'),
     $this->_addRule('_prepareFields', 'Enterprise_Staging_Model_Resource_Adapter_Abstract'),
     $this->_addRule('canShow', 'Enterprise_Staging_Helper_Store'),
     $this->_addRule('canShow', 'Enterprise_Staging_Helper_Website'),
     $this->_addRule('addCategoryFilter', 'Enterprise_Search_Model_Catalog_Layer_Filter_Category'),
     $this->_addRule('setModelName', 'Enterprise_Logging_Model_Event_Changes'),
     $this->_addRule('getModelName', 'Enterprise_Logging_Model_Event_Changes'),
     $this->_addRule('setModelId', 'Enterprise_Logging_Model_Event_Changes'),
     $this->_addRule('getModelId', 'Enterprise_Logging_Model_Event_Changes'),
     $this->_addRule('_initAction', 'Enterprise_Checkout_Adminhtml_CheckoutController'),
     $this->_addRule('getEventData', 'Enterprise_Logging_Block_Adminhtml_Container'),
     $this->_addRule('getEventXForwardedIp', 'Enterprise_Logging_Block_Adminhtml_Container'),
     $this->_addRule('getEventIp', 'Enterprise_Logging_Block_Adminhtml_Container'),
     $this->_addRule('getEventError', 'Enterprise_Logging_Block_Adminhtml_Container'),
     $this->_addRule('postDispatchSystemStoreSave', 'Enterprise_Logging_Model_Handler_Controllers'),
     $this->_addRule('getUrls', 'Enterprise_PageCache_Model_Crawler'),
     $this->_addRule('getUrlStmt', 'Enterprise_PageCache_Model_Resource_Crawler'),
     $this->_addRule('_getLinkCollection', 'Enterprise_TargetRule_Block_Checkout_Cart_Crosssell'),
     $this->_addRule('getCustomerSegments', 'Enterprise_CustomerSegment_Model_Resource_Customer'),
     $this->_addRule('getRequestUri', 'Enterprise_PageCache_Model_Processor_Default'),
     $this->_addRule('_getActiveEntity', 'Enterprise_GiftRegistry_IndexController'),
     $this->_addRule('getActiveEntity', 'Enterprise_GiftRegistry_Model_Entity'),
     $this->_addRule('_convertDateTime', 'Enterprise_CatalogEvent_Model_Event'),
     $this->_addRule('updateStatus', 'Enterprise_CatalogEvent_Model_Event'),
     $this->_addRule('getStateText', 'Enterprise_GiftCardAccount_Model_Giftcardaccount'),
     $this->_addRule('_searchSuggestions', 'Enterprise_Search_Model_Adapter_HttpStream'),
     $this->_addRule('_searchSuggestions', 'Enterprise_Search_Model_Adapter_PhpExtension'),
     $this->_addRule('updateCategoryIndexData', 'Enterprise_Search_Model_Resource_Index'),
     $this->_addRule('updatePriceIndexData', 'Enterprise_Search_Model_Resource_Index'),
     $this->_addRule('_changeIndexesStatus', 'Enterprise_Search_Model_Indexer_Indexer'),
     $this->_addRule('cmsPageBlockLoadAfter', 'Enterprise_AdminGws_Model_Models'),
     $this->_addRule('applyEventStatus', 'Enterprise_CatalogEvent_Model_Observer'),
     $this->_addRule('checkQuoteItem', 'Enterprise_CatalogPermissions_Model_Observer'),
     $this->_addRule('increaseOrderInvoicedAmount', 'Enterprise_GiftCardAccount_Model_Observer'),
     $this->_addRule('blockCreateAfter', 'Enterprise_PageCache_Model_Observer'),
     $this->_addRule('_checkViewedProducts', 'Enterprise_PageCache_Model_Observer'),
     $this->_addRule('invoiceSaveAfter', 'Enterprise_Reward_Model_Observer'),
     $this->_addRule('_calcMinMax', 'Enterprise_GiftCard_Block_Catalog_Product_Price'),
     $this->_addRule('_getAmounts', 'Enterprise_GiftCard_Block_Catalog_Product_Price'),
     $this->_addRule('searchSuggestions', 'Enterprise_Search_Model_Client_Solr'),
     $this->_addRule('_registerProductsView', 'Enterprise_PageCache_Model_Container_Viewedproducts'),
     $this->_addRule('_getForeignKeyName', 'Varien_Db_Adapter_Oracle'),
);
