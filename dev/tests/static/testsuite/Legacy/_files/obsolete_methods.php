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
    $this->_addRule('__get', 'Varien_Object'),
    $this->_addRule('__set', 'Varien_Object'),
    $this->_addRule('_addMinimalPrice', 'Mage_Catalog_Model_Resource_Product_Collection'),
    $this->_addRule('_addTaxPercents', 'Mage_Catalog_Model_Resource_Product_Collection'),
    $this->_addRule('_addToXml', 'Mage_XmlConnect_Block_Checkout_Payment_Method_List'),
    $this->_addRule('_afterSaveCommit', 'Mage_Core_Model_Abstract'),
    $this->_addRule('_afterSetConfig', 'Mage_Eav_Model_Entity_Abstract'),
    $this->_addRule('_aggregateByOrderCreatedAt', 'Mage_SalesRule_Model_Resource_Report_Rule'),
    $this->_addRule('_amountByCookies', 'Mage_Sendfriend_Model_Sendfriend'),
    $this->_addRule('_amountByIp', 'Mage_Sendfriend_Model_Sendfriend'),
    $this->_addRule('_applyCustomDesignSettings'),
    $this->_addRule('_applyDesign', 'Mage_Catalog_Model_Design'),
    $this->_addRule('_applyDesignRecursively', 'Mage_Catalog_Model_Design'),
    $this->_addRule('_avoidDoubleTransactionProcessing'),
    $this->_addRule('_beforeChildToHtml'),
    $this->_addRule('_calculatePrice', 'Mage_Sales_Model_Quote_Item_Abstract'),
    $this->_addRule('_checkUrlSettings', 'Mage_Adminhtml_Controller_Action'),
    $this->_addRule('_collectOrigData', 'Mage_Catalog_Model_Resource_Abstract'),
    $this->_addRule('_decodeInput', 'Mage_Adminhtml_Catalog_ProductController'),
    $this->_addRule('_emailOrderConfirmation', 'Mage_Checkout_Model_Type_Abstract'),
    $this->_addRule('_escapeValue', 'Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract'),
    $this->_addRule('_formatAddress', 'Mage_XmlConnect_Block_Customer_Order_Details'),
    $this->_addRule('_getAddressTaxRequest', 'Mage_Tax_Model_Sales_Total_Quote_Shipping'),
    $this->_addRule('_getAggregationPerStoreView'),
    $this->_addRule('_getAttributeFilterBlockName', 'Mage_Catalog_Block_Layer_View'),
    $this->_addRule('_getAttributeFilterBlockName', 'Mage_CatalogSearch_Block_Layer'),
    $this->_addRule('_getAttributeFilterBlockName'),
    $this->_addRule('_getAvailable', 'Mage_GiftMessage_Model_Observer'),
    $this->_addRule('_getCacheId', 'Mage_Core_Model_App'),
    $this->_addRule('_getCacheKey', 'Mage_Catalog_Model_Layer_Filter_Price'),
    $this->_addRule('_getCacheTags', 'Mage_Core_Model_App'),
    $this->_addRule('_getChildHtml'),
    $this->_addRule('_getCollectionNames', 'Mage_Adminhtml_Report_SalesController'),
    $this->_addRule('_getConnenctionType', 'Mage_Install_Model_Installer_Db'),
    $this->_addRule('_getDateFromToHtml', 'Mage_ImportExport_Block_Adminhtml_Export_Filter'),
    $this->_addRule('_getExistingBasePopularity'),
    $this->_addRule('_getFieldTableAlias', 'Mage_Newsletter_Model_Resource_Subscriber_Collection'),
    $this->_addRule('_getForeignKeyName', 'Varien_Db_Adapter_Pdo_Mysql'),
    $this->_addRule('_getGiftmessageSaveModel', 'Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid'),
    $this->_addRule('_getGlobalAggregation'),
    $this->_addRule('_getGroupByDateFormat', 'Mage_Log_Model_Resource_Visitor_Collection'),
    $this->_addRule('_getInputHtml', 'Mage_ImportExport_Block_Adminhtml_Export_Filter'),
    $this->_addRule('_getLabelForStore', 'Mage_Catalog_Model_Resource_Eav_Attribute'),
    $this->_addRule('_getMultiSelectHtml', 'Mage_ImportExport_Block_Adminhtml_Export_Filter'),
    $this->_addRule('_getNumberFromToHtml', 'Mage_ImportExport_Block_Adminhtml_Export_Filter'),
    $this->_addRule('_getPriceFilter', 'Mage_Catalog_Block_Layer_View'),
    $this->_addRule('_getProductQtyForCheck', 'Mage_CatalogInventory_Model_Observer'),
    $this->_addRule('_getRangeByType', 'Mage_Log_Model_Resource_Visitor_Collection'),
    $this->_addRule('_getRecentProductsCollection'),
    $this->_addRule('_getSelectHtml', 'Mage_ImportExport_Block_Adminhtml_Export_Filter'),
    $this->_addRule('_getSetData', 'Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main'),
    $this->_addRule('_getSHAInSet', null, 'Mage_Ogone_Model_Api::getHash'),
    $this->_addRule('_getStoreTaxRequest', 'Mage_Tax_Model_Sales_Total_Quote_Shipping'),
    $this->_addRule('_importAddress', 'Mage_Paypal_Model_Api_Nvp'),
    $this->_addRule('_inheritDesign', 'Mage_Catalog_Model_Design'),
    $this->_addRule('_initOrder', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_addRule('_initShipment', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_addRule('_inludeControllerClass', null, '_includeControllerClass'),
    $this->_addRule('_isApplyDesign', 'Mage_Catalog_Model_Design'),
    $this->_addRule('_isApplyFor', 'Mage_Catalog_Model_Design'),
    $this->_addRule('_isPositiveDecimalNumber', 'Mage_Shipping_Model_Resource_Carrier_Tablerate'),
    $this->_addRule('_loadOldRates', 'Mage_Tax_Model_Resource_Setup'),
    $this->_addRule('_needSubtractShippingTax'),
    $this->_addRule('_needSubtractTax'),
    $this->_addRule('_needToAddDummy'),
    $this->_addRule('_needToAddDummyForShipment'),
    $this->_addRule('_parseDescription', 'Mage_Sales_Model_Order_Pdf_Items_Abstract'),
    $this->_addRule('_parseXmlTrackingResponse', 'Mage_Usa_Model_Shipping_Carrier_Fedex'),
    $this->_addRule('_prepareCondition', 'Mage_CatalogSearch_Model_Advanced'),
    $this->_addRule('_prepareConfigurableProductData', 'Mage_ImportExport_Model_Export_Entity_Product'),
    $this->_addRule('_prepareConfigurableProductPrice', 'Mage_ImportExport_Model_Export_Entity_Product'),
    $this->_addRule('_prepareOptionsForCart', 'Mage_Catalog_Model_Product_Type_Abstract'),
    $this->_addRule('_preparePackageTheme', 'Mage_Widget_Model_Widget_Instance'),
    $this->_addRule('_processItem', 'Mage_Weee_Model_Total_Quote_Weee'),
    $this->_addRule('_processShippingAmount'),
    $this->_addRule('_processValidateCustomer', 'Mage_Checkout_Model_Type_Onepage'),
    $this->_addRule('_putCustomerIntoQuote', 'Mage_Adminhtml_Model_Sales_Order_Create'),
    $this->_addRule('_quoteRow', 'Mage_Backup_Model_Resource_Db'),
    $this->_addRule('_recollectItem', 'Mage_Tax_Model_Sales_Total_Quote_Subtotal'),
    $this->_addRule('_resetItemPriceInclTax'),
    $this->_addRule('_saveCustomerAfterOrder', 'Mage_Adminhtml_Model_Sales_Order_Create'),
    $this->_addRule('_saveCustomers', 'Mage_Adminhtml_Model_Sales_Order_Create'),
    $this->_addRule('_sendUploadResponse', 'Mage_Adminhtml_CustomerController'),
    $this->_addRule('_sendUploadResponse', 'Mage_Adminhtml_Newsletter_SubscriberController'),
    $this->_addRule('_setAttribteValue'),
    $this->_addRule('addBackupedFilter'),
    $this->_addRule('addConfigField', 'Mage_Core_Model_Resource_Setup'),
    $this->_addRule('addConstraint', 'Varien_Db_Adapter_Pdo_Mysql'),
    $this->_addRule('addCustomersToAlertQueueAction'),
    $this->_addRule('addCustomerToSegments'),
    $this->_addRule('addGroupByTag', 'Mage_Tag_Model_Resource_Reports_Collection'),
    $this->_addRule('addKey', 'Varien_Db_Adapter_Pdo_Mysql'),
    $this->_addRule('addSaleableFilterToCollection'),
    $this->_addRule('addSearchQfFilter'),
    $this->_addRule('addStoresFilter', 'Mage_Poll_Model_Resource_Poll_Collection'),
    $this->_addRule('addSummary', 'Mage_Tag_Model_Resource_Tag'),
    $this->_addRule('addSummary', 'Mage_Tag_Model_Tag'),
    $this->_addRule('addTemplateData', 'Mage_Newsletter_Model_Queue'),
    $this->_addRule('addToAlersAction'),
    $this->_addRule('addToChildGroup'),
    $this->_addRule('addVisibleFilterToCollection', 'Mage_Catalog_Model_Product_Status'),
    $this->_addRule('addVisibleInCatalogFilterToCollection', null,
        '$collection->setVisibility(Mage_Catalog_Model_Product_Visibility->getVisibleInCatalogIds());'),
    $this->_addRule('addVisibleInSearchFilterToCollection', null,
        '$collection->setVisibility(Mage_Catalog_Model_Product_Visibility->getVisibleInSearchIds());'),
    $this->_addRule('addVisibleInSiteFilterToCollection', null,
        '$collection->setVisibility(Mage_Catalog_Model_Product_Visibility->getVisibleInSiteIds());'),
    $this->_addRule('addWishlistLink', 'Mage_Wishlist_Block_Links'),
    $this->_addRule('addWishListSortOrder', 'Mage_Wishlist_Model_Resource_Item_Collection'),
    $this->_addRule('aggregate', 'Mage_Tag_Model_Resource_Tag'),
    $this->_addRule('aggregate', 'Mage_Tag_Model_Tag'),
    $this->_addRule('applyDesign', 'Mage_Catalog_Model_Design'),
    $this->_addRule('authAdmin'),
    $this->_addRule('authFailed', null, 'Mage_Core_Helper_Http::failHttpAuthentication()'),
    $this->_addRule('authFrontend'),
    $this->_addRule('authValidate', null, 'Mage_Core_Helper_Http::getHttpAuthCredentials()'),
    $this->_addRule('bundlesAction', 'Mage_Adminhtml_Catalog_ProductController'),
    $this->_addRule('calcTaxAmount', 'Mage_Sales_Model_Quote_Item_Abstract'),
    $this->_addRule('canPrint', 'Mage_Checkout_Block_Onepage_Success'),
    $this->_addRule('catalogCategoryChangeProducts', 'Mage_Catalog_Model_Product_Flat_Observer'),
    $this->_addRule('catalogEventProductCollectionAfterLoad', 'Mage_GiftMessage_Model_Observer'),
    $this->_addRule('catalogProductLoadAfter', 'Mage_Bundle_Model_Observer'),
    $this->_addRule('chechAllowedExtension'),
    $this->_addRule('checkConfigurableProducts', 'Mage_Eav_Model_Resource_Entity_Attribute_Collection'),
    $this->_addRule('checkDatabase', 'Mage_Install_Model_Installer_Db'),
    $this->_addRule('checkDateTime', 'Mage_Core_Model_Date'),
    $this->_addRule('cleanDbRow', 'Mage_Core_Model_Resource'),
    $this->_addRule('cloneIndexTable', 'Mage_Index_Model_Resource_Abstract'),
    $this->_addRule('convertOldTaxData', 'Mage_Tax_Model_Resource_Setup'),
    $this->_addRule('convertOldTreeToNew', 'Mage_Catalog_Model_Resource_Setup'),
    $this->_addRule('countChildren', 'Mage_Core_Block_Abstract'),
    $this->_addRule('crear'),
    $this->_addRule('createOrderItem', 'Mage_CatalogInventory_Model_Observer'),
    $this->_addRule('debugRequest', 'Mage_Paypal_Model_Api_Standard'),
    $this->_addRule('deleteProductPrices', 'Mage_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice'),
    $this->_addRule('display', 'Varien_Image_Adapter_Abstract', 'getImage()'),
    $this->_addRule('displayFullSummary', 'Mage_Tax_Model_Config'),
    $this->_addRule('displayTaxColumn', 'Mage_Tax_Model_Config'),
    $this->_addRule('displayZeroTax', 'Mage_Tax_Model_Config'),
    $this->_addRule('drawItem', 'Mage_Catalog_Block_Navigation'),
    $this->_addRule('dropKey', 'Varien_Db_Adapter_Pdo_Mysql'),
    $this->_addRule('editAction', 'Mage_Tag_CustomerController'),
    $this->_addRule('exportOrderedCsvAction'),
    $this->_addRule('exportOrderedExcelAction'),
    $this->_addRule('fetchItemsCount', 'Mage_Wishlist_Model_Resource_Wishlist'),
    $this->_addRule('fetchRuleRatesForCustomerTaxClass'),
    $this->_addRule('forsedSave'),
    $this->_addRule('generateBlocks', null, 'generateElements()'),
    $this->_addRule('getAccount', 'Mage_GoogleAnalytics_Block_Ga'),
    $this->_addRule('getAclAssert', 'Mage_Admin_Model_Config'),
    $this->_addRule('getAclPrivilegeSet', 'Mage_Admin_Model_Config'),
    $this->_addRule('getAclResourceList', 'Mage_Admin_Model_Config'),
    $this->_addRule('getAclResourceTree', 'Mage_Admin_Model_Config'),
    $this->_addRule('getAddNewButtonHtml', 'Mage_Adminhtml_Block_Catalog_Product'),
    $this->_addRule('getAddToCartItemUrl', 'Mage_Wishlist_Block_Customer_Sidebar'),
    $this->_addRule('getAddToCartUrlBase64', null, '_getAddToCartUrl'),
    $this->_addRule('getAllEntityIds', 'Mage_Rss_Model_Resource_Order'),
    $this->_addRule('getAllEntityTypeCommentIds', 'Mage_Rss_Model_Resource_Order'),
    $this->_addRule('getAllOrderEntityIds', 'Mage_Rss_Model_Resource_Order'),
    $this->_addRule('getAllOrderEntityTypeIds', 'Mage_Rss_Model_Resource_Order'),
    $this->_addRule('getAnonSuffix'),
    $this->_addRule('getBaseTaxAmount', 'Mage_Sales_Model_Quote_Item_Abstract'),
    $this->_addRule('getCheckoutMehod', 'Mage_Checkout_Model_Type_Onepage'),
    $this->_addRule('getChild', null, 'Mage_Core_Block_Abstract::getChildBlock()', 'app'),
    $this->_addRule('getChildGroup', null, 'Mage_Core_Block_Abstract::getGroupChildNames()'),
    $this->_addRule('getConfig', 'Mage_Eav_Model_Entity_Attribute_Abstract'),
    $this->_addRule('getCustomerData', 'Mage_Adminhtml_Block_Sales_Order_Create_Form_Account'),
    $this->_addRule('getDataForSave', 'Mage_Wishlist_Model_Item'),
    $this->_addRule('getDebug', 'Mage_Ogone_Model_Api'),
    $this->_addRule('getDebug', 'Mage_Paypal_Model_Api_Abstract'),
    $this->_addRule('getDirectOutput', 'Mage_Core_Model_Layout'),
    $this->_addRule('getEntityIdsToIncrementIds', 'Mage_Rss_Model_Resource_Order'),
    $this->_addRule('getEntityTypeIdsToTypes', 'Mage_Rss_Model_Resource_Order'),
    $this->_addRule('getFacets'),
    $this->_addRule('getFallbackTheme'),
    $this->_addRule('getFormated', null, 'getFormated(true) -> format(\'html\'), getFormated() -> format(\'text\')'),
    $this->_addRule('getFormObject', 'Mage_Adminhtml_Block_Widget_Form'),
    $this->_addRule('getGiftmessageHtml', 'Mage_Adminhtml_Block_Sales_Order_View_Tab_Info'),
    $this->_addRule('getHtmlFormat', 'Mage_Customer_Model_Address_Abstract'),
    $this->_addRule('getIsActiveAanalytics', null, 'getOnsubmitJs'),
    $this->_addRule('getIsAjaxRequest', 'Mage_Core_Model_Translate_Inline'),
    $this->_addRule('getIsAnonymous'),
    $this->_addRule('getIsEngineAvailable'),
    $this->_addRule('getIsGlobal', 'Mage_Eav_Model_Entity_Attribute_Abstract'),
    $this->_addRule('getIsInStock', 'Mage_Checkout_Block_Cart_Item_Renderer'),
    $this->_addRule('getItemRender', 'Mage_Checkout_Block_Cart_Abstract'),
    $this->_addRule('getJoinFlag', 'Mage_Tag_Model_Resource_Customer_Collection'),
    $this->_addRule('getJoinFlag', 'Mage_Tag_Model_Resource_Product_Collection'),
    $this->_addRule('getJoinFlag', 'Mage_Tag_Model_Resource_Tag_Collection'),
    $this->_addRule('getKeyList', 'Varien_Db_Adapter_Pdo_Mysql'),
    $this->_addRule('getLanguages', 'Mage_Install_Block_Begin'),
    $this->_addRule('getLayoutFilename', null, 'getFilename'),
    $this->_addRule('getLifeTime', 'Mage_Core_Model_Resource_Session'),
    $this->_addRule('getLocaleBaseDir', 'Mage_Core_Model_Design_Package'),
    $this->_addRule('getMail', 'Mage_Newsletter_Model_Template'),
    $this->_addRule('getMaxQueryLenght'),
    $this->_addRule('getMenuItemLabel', 'Mage_Admin_Model_Config'),
    $this->_addRule('getMergedCssUrl'),
    $this->_addRule('getMergedJsUrl'),
    $this->_addRule('getMinQueryLenght'),
    $this->_addRule('getOneBalanceTotal'),
    $this->_addRule('getOrderHtml', 'Mage_GoogleAnalytics_Block_Ga'),
    $this->_addRule('getOrderId', 'Mage_Checkout_Block_Onepage_Success'),
    $this->_addRule('getOrderId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_addRule('getOriginalHeigh', null, 'getOriginalHeight'),
    $this->_addRule('getParentProductIds', 'Mage_Catalog_Model_Resource_Product'),
    $this->_addRule('getPriceFormatted', 'Mage_Adminhtml_Block_Customer_Edit_Tab_View_Sales'),
    $this->_addRule('getPrices', 'Mage_Bundle_Model_Product_Price'),
    $this->_addRule('getPricesDependingOnTax', 'Mage_Bundle_Model_Product_Price'),
    $this->_addRule('getPrintUrl', 'Mage_Checkout_Block_Onepage_Success'),
    $this->_addRule('getPrintUrl', 'Mage_Sales_Block_Order_Info'),
    $this->_addRule('getProductCollection', 'Mage_Wishlist_Helper_Data'),
    $this->_addRule('getProductCollection', 'Mage_Wishlist_Model_Wishlist'),
    $this->_addRule('getProductsNotInStoreIds'),
    $this->_addRule('getProfile', 'Varien_Convert_Container_Abstract'),
    $this->_addRule('getQuoteItem', 'Mage_Catalog_Model_Product_Option_Type_Default'),
    $this->_addRule('getQuoteItemOption', 'Mage_Catalog_Model_Product_Option_Type_Default'),
    $this->_addRule('getQuoteOrdersHtml', 'Mage_GoogleAnalytics_Block_Ga'),
    $this->_addRule('getRemoveItemUrl', 'Mage_Wishlist_Block_Customer_Sidebar'),
    $this->_addRule('getReorderUrl', 'Mage_Sales_Block_Order_Info'),
    $this->_addRule('getRowId', 'Mage_Adminhtml_Block_Sales_Order_Create_Customer_Grid'),
    $this->_addRule('getRowId', 'Mage_Adminhtml_Block_Widget_Grid'),
    $this->_addRule('getSaveTemplateFlag', 'Mage_Newsletter_Model_Queue'),
    $this->_addRule('getSelectionFinalPrice', 'Mage_Bundle_Model_Product_Price'),
    $this->_addRule('getShipId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_addRule('getSortedChildren', null, 'getChildNames'),
    $this->_addRule('getSortedChildBlocks', null, 'getChildNames() + $this->getLayout()->getBlock($name)'),
    $this->_addRule('getStatrupPageUrl'),
    $this->_addRule('getStoreCurrency', 'Mage_Sales_Model_Order'),
    $this->_addRule('getSuggestedZeroDate'),
    $this->_addRule('getSuggestionsByQuery'),
    $this->_addRule('getTaxAmount', 'Mage_Sales_Model_Quote_Item_Abstract'),
    $this->_addRule('getTaxRatesByProductClass', null, '_getAllRatesByProductClass'),
    $this->_addRule('getTemplateFilename', null, 'getFilename'),
    $this->_addRule('getTotalModels', 'Mage_Sales_Model_Quote_Address'),
    $this->_addRule('getTrackId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_addRule('getTrackingInfoByOrder', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_addRule('getTrackingInfoByShip', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_addRule('getTrackingInfoByTrackId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_addRule('getTrackingPopUpUrlByOrderId', null, 'getTrackingPopupUrlBySalesModel'),
    $this->_addRule('getTrackingPopUpUrlByShipId', null, 'getTrackingPopupUrlBySalesModel'),
    $this->_addRule('getTrackingPopUpUrlByTrackId', null, 'getTrackingPopupUrlBySalesModel'),
    $this->_addRule('getUseCacheFilename', 'Mage_Core_Model_App'),
    $this->_addRule('getValidator', 'Mage_SalesRule_Model_Observer'),
    $this->_addRule('getValidatorData', 'Mage_Core_Model_Session_Abstract', 'use _getSessionEnvironment method'),
    $this->_addRule('getValueTable'),
    $this->_addRule('getViewOrderUrl', 'Mage_Checkout_Block_Onepage_Success'),
    $this->_addRule('getWidgetSupportedBlocks', 'Mage_Widget_Model_Widget_Instance'),
    $this->_addRule('getWidgetSupportedTemplatesByBlock', 'Mage_Widget_Model_Widget_Instance'),
    $this->_addRule('hasItems', 'Mage_Wishlist_Helper_Data'),
    $this->_addRule('htmlEscape', null, 'escapeHtml'),
    $this->_addRule('imageAction', 'Mage_Catalog_ProductController'),
    $this->_addRule('importFromTextArray'),
    $this->_addRule('initLabels', 'Mage_Catalog_Model_Resource_Eav_Attribute'),
    $this->_addRule('insertProductPrice', 'Mage_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice'),
    $this->_addRule('isAllowedGuestCheckout', 'Mage_Sales_Model_Quote'),
    $this->_addRule('isAutomaticCleaningAvailable', 'Varien_Cache_Backend_Eaccelerator'),
    $this->_addRule('isCheckoutAvailable', 'Mage_Checkout_Model_Type_Multishipping'),
    $this->_addRule('isFulAmountCovered'),
    $this->_addRule('isLeyeredNavigationAllowed'),
    $this->_addRule('isReadablePopupObject'),
    $this->_addRule('isTemplateAllowedForApplication'),
    $this->_addRule('loadLabel', 'Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute'),
    $this->_addRule('loadParentProductIds', 'Mage_Catalog_Model_Product'),
    $this->_addRule('loadPrices', 'Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute'),
    $this->_addRule('loadProductPrices', 'Mage_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice'),
    $this->_addRule('lockOrderInventoryData', 'Mage_CatalogInventory_Model_Observer'),
    $this->_addRule('logEncryptionKeySave'),
    $this->_addRule('logInvitationSave'),
    $this->_addRule('mergeFiles', 'Mage_Core_Helper_Data'),
    $this->_addRule('order_success_page_view', 'Mage_GoogleAnalytics_Model_Observer'),
    $this->_addRule('orderedAction', 'Mage_Adminhtml_Report_ProductController'),
    $this->_addRule('parse', 'Mage_Catalog_Model_Convert_Parser_Product'),
    $this->_addRule('parse', 'Mage_Customer_Model_Convert_Parser_Customer'),
    $this->_addRule('parseDateTime', 'Mage_Core_Model_Date'),
    $this->_addRule('postDispatchMyAccountSave'),
    $this->_addRule('prepareCacheId', 'Mage_Core_Model_App'),
    $this->_addRule('prepareGoogleOptimizerScripts'),
    $this->_addRule('preprocess', 'Mage_Newsletter_Model_Template'),
    $this->_addRule('processBeacon'),
    $this->_addRule('processBeforeVoid', 'Mage_Payment_Model_Method_Abstract'),
    $this->_addRule('processSubst', 'Mage_Core_Model_Store'),
    $this->_addRule('productEventAggregate'),
    $this->_addRule('push', 'Mage_Catalog_Model_Product_Image'),
    $this->_addRule('rebuildCategoryLevels', 'Mage_Catalog_Model_Resource_Setup'),
    $this->_addRule('regenerateSessionId', 'Mage_Core_Model_Session_Abstract'),
    $this->_addRule('refundOrderItem', 'Mage_CatalogInventory_Model_Observer'),
    $this->_addRule('removeCustomerFromSegments'),
    $this->_addRule('revalidateCookie', 'Mage_Core_Model_Session_Abstract_Varien'),
    $this->_addRule('sales_order_afterPlace'),
    $this->_addRule('sales_quote_address_discount_item'),
    $this->_addRule('salesOrderPaymentPlaceEnd'),
    $this->_addRule('saveRow__OLD'),
    $this->_addRule('saveAction', 'Mage_Tag_CustomerController'),
    $this->_addRule('saveSegmentCustomersFromSelect'),
    $this->_addRule('send', 'Mage_Newsletter_Model_Template'),
    $this->_addRule('sendNewPasswordEmail'),
    $this->_addRule('setAnonSuffix'),
    $this->_addRule('setAttributeSetExcludeFilter', 'Mage_Eav_Model_Resource_Entity_Attribute_Collection'),
    $this->_addRule('setBlockAlias'),
    $this->_addRule('setCustomerId', 'Mage_Customer_Model_Resource_Address'),
    $this->_addRule('setIsAjaxRequest', 'Mage_Core_Model_Translate_Inline'),
    $this->_addRule('setIsAnonymous'),
    $this->_addRule('setJoinFlag', 'Mage_Tag_Model_Resource_Customer_Collection'),
    $this->_addRule('setJoinFlag', 'Mage_Tag_Model_Resource_Product_Collection'),
    $this->_addRule('setJoinFlag', 'Mage_Tag_Model_Resource_Tag_Collection'),
    $this->_addRule('setOrderId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_addRule('setWatermarkHeigth', null, 'setWatermarkHeight'),
    $this->_addRule('getWatermarkHeigth', null, 'getWatermarkHeight'),
    $this->_addRule('setParentBlock'),
    $this->_addRule('setProfile', 'Varien_Convert_Container_Abstract'),
    $this->_addRule('setSaveTemplateFlag', 'Mage_Newsletter_Model_Queue'),
    $this->_addRule('setShipId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_addRule('setTaxGroupFilter'),
    $this->_addRule('setTrackId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_addRule('shaCrypt', null, 'Mage_Ogone_Model_Api::getHash'),
    $this->_addRule('shaCryptValidation', null, 'Mage_Ogone_Model_Api::getHash'),
    $this->_addRule('shouldCustomerHaveOneBalance'),
    $this->_addRule('shouldShowOneBalance'),
    $this->_addRule('sortChildren'),
    $this->_addRule('toOptionArray', 'Mage_Cms_Model_Resource_Page_Collection'),
    $this->_addRule('toOptionArray', 'Mage_Sendfriend_Model_Sendfriend'),
    $this->_addRule('truncate', 'Varien_Db_Adapter_Pdo_Mysql'),
    $this->_addRule('unsetBlock'),
    $this->_addRule('unsetJoinFlag', 'Mage_Tag_Model_Resource_Customer_Collection'),
    $this->_addRule('unsetJoinFlag', 'Mage_Tag_Model_Resource_Product_Collection'),
    $this->_addRule('unsetJoinFlag', 'Mage_Tag_Model_Resource_Tag_Collection'),
    $this->_addRule('useValidateRemoteAddr', 'Mage_Core_Model_Session_Abstract'),
    $this->_addRule('useValidateHttpVia', 'Mage_Core_Model_Session_Abstract'),
    $this->_addRule('useValidateHttpXForwardedFor', 'Mage_Core_Model_Session_Abstract'),
    $this->_addRule('useValidateHttpUserAgent', 'Mage_Core_Model_Session_Abstract'),
    $this->_addRule('updateCofigurableProductOptions', 'Mage_Weee_Model_Observer', 'updateConfigurableProductOptions'),
    $this->_addRule('updateTable', 'Mage_Core_Model_Resource_Setup'),
    $this->_addRule('urlEscape', null, 'escapeUrl'),
    $this->_addRule('validateDataArray', 'Varien_Convert_Container_Abstract'),
    $this->_addRule('validateFile', 'Mage_Core_Model_Design_Package'),
    $this->_addRule('validateOrder', 'Mage_Checkout_Model_Type_Onepage'),
    $this->_addRule('prepareAttributesForSave', 'Mage_ImportExport_Model_Import_Entity_Product'),
    $this->_addRule('fetchUpdatesByHandle', 'Mage_Core_Model_Resource_Layout',
        'Mage_Core_Model_Resource_Layout_Update'),
    $this->_addRule('getElementClass', 'Mage_Core_Model_Layout_Update'),
    $this->_addRule('addUpdate', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('asArray', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('asString', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('addHandle', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('removeHandle', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('getHandles', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('addPageHandles', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('getPageHandleParents', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('pageHandleExists', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('getPageHandles', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('getPageHandlesHierarchy', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('getPageHandleLabel', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('getPageHandleType', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('load', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('asSimplexml', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('getFileLayoutUpdatesXml', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_addRule('getContainers', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge')
);
