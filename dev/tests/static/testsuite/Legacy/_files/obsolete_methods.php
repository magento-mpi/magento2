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

/** @var $this Legacy_ObsoleteCodeTest */
return array(
    $this->_getRule('__get', 'Varien_Object'),
    $this->_getRule('__set', 'Varien_Object'),
    $this->_getRule('_addMinimalPrice', 'Mage_Catalog_Model_Resource_Product_Collection'),
    $this->_getRule('_addTaxPercents', 'Mage_Catalog_Model_Resource_Product_Collection'),
    $this->_getRule('_afterSaveCommit', 'Mage_Core_Model_Abstract'),
    $this->_getRule('_afterSetConfig', 'Mage_Eav_Model_Entity_Abstract'),
    $this->_getRule('_aggregateByOrderCreatedAt', 'Mage_SalesRule_Model_Resource_Report_Rule'),
    $this->_getRule('_amountByCookies', 'Mage_Sendfriend_Model_Sendfriend'),
    $this->_getRule('_amountByIp', 'Mage_Sendfriend_Model_Sendfriend'),
    $this->_getRule('_applyCustomDesignSettings'),
    $this->_getRule('_applyDesign', 'Mage_Catalog_Model_Design'),
    $this->_getRule('_applyDesignRecursively', 'Mage_Catalog_Model_Design'),
    $this->_getRule('_avoidDoubleTransactionProcessing'),
    $this->_getRule('_beforeChildToHtml'),
    $this->_getRule('_calculatePrice', 'Mage_Sales_Model_Quote_Item_Abstract'),
    $this->_getRule('_canShowField', 'Mage_Backend_Block_System_Config_Form'),
    $this->_getRule('_canUseLocalModules'),
    $this->_getRule('_checkUrlSettings', 'Mage_Adminhtml_Controller_Action'),
    $this->_getRule('_collectOrigData', 'Mage_Catalog_Model_Resource_Abstract'),
    $this->_getRule('_decodeInput', 'Mage_Adminhtml_Catalog_ProductController'),
    $this->_getRule('_emailOrderConfirmation', 'Mage_Checkout_Model_Type_Abstract'),
    $this->_getRule('_escapeValue', 'Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract'),
    $this->_getRule('_getAddressTaxRequest', 'Mage_Tax_Model_Sales_Total_Quote_Shipping'),
    $this->_getRule('_getAggregationPerStoreView'),
    $this->_getRule('_getAttributeFilterBlockName', 'Mage_Catalog_Block_Layer_View'),
    $this->_getRule('_getAttributeFilterBlockName', 'Mage_CatalogSearch_Block_Layer'),
    $this->_getRule('_getAttributeFilterBlockName'),
    $this->_getRule('_getAvailable', 'Mage_GiftMessage_Model_Observer'),
    $this->_getRule('_getCacheId', 'Mage_Core_Model_App'),
    $this->_getRule('_getCacheKey', 'Mage_Catalog_Model_Layer_Filter_Price'),
    $this->_getRule('_getCacheTags', 'Mage_Core_Model_App'),
    $this->_getRule('_getChildHtml'),
    $this->_getRule('_getCollapseState', 'Mage_Backend_Block_System_Config_Form_Fieldset', '_isCollapseState'),
    $this->_getRule('_getCollectionNames', 'Mage_Adminhtml_Report_SalesController'),
    $this->_getRule('_getConnenctionType', 'Mage_Install_Model_Installer_Db'),
    $this->_getRule('_getDateFromToHtml', 'Mage_ImportExport_Block_Adminhtml_Export_Filter'),
    $this->_getRule('_getExistingBasePopularity'),
    $this->_getRule('_getFieldTableAlias', 'Mage_Newsletter_Model_Resource_Subscriber_Collection'),
    $this->_getRule('_getForeignKeyName', 'Varien_Db_Adapter_Pdo_Mysql'),
    $this->_getRule('_getGiftmessageSaveModel', 'Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid'),
    $this->_getRule('_getGlobalAggregation'),
    $this->_getRule('_getGroupByDateFormat', 'Mage_Log_Model_Resource_Visitor_Collection'),
    $this->_getRule('_getInputHtml', 'Mage_ImportExport_Block_Adminhtml_Export_Filter'),
    $this->_getRule('_getLabelForStore', 'Mage_Catalog_Model_Resource_Eav_Attribute'),
    $this->_getRule('_getMultiSelectHtml', 'Mage_ImportExport_Block_Adminhtml_Export_Filter'),
    $this->_getRule('_getNumberFromToHtml', 'Mage_ImportExport_Block_Adminhtml_Export_Filter'),
    $this->_getRule('_getPriceFilter', 'Mage_Catalog_Block_Layer_View'),
    $this->_getRule('_getProductQtyForCheck', 'Mage_CatalogInventory_Model_Observer'),
    $this->_getRule('_getRangeByType', 'Mage_Log_Model_Resource_Visitor_Collection'),
    $this->_getRule('_getRecentProductsCollection'),
    $this->_getRule('_getSelectHtml', 'Mage_ImportExport_Block_Adminhtml_Export_Filter'),
    $this->_getRule('_getSetData', 'Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main'),
    $this->_getRule('_getSHAInSet', null, 'Mage_Ogone_Model_Api::getHash'),
    $this->_getRule('_getStoreTaxRequest', 'Mage_Tax_Model_Sales_Total_Quote_Shipping'),
    $this->_getRule('_importAddress', 'Mage_Paypal_Model_Api_Nvp'),
    $this->_getRule('_inheritDesign', 'Mage_Catalog_Model_Design'),
    $this->_getRule('_initOrder', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_getRule('_initShipment', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_getRule('_inludeControllerClass', null, '_includeControllerClass'),
    $this->_getRule('_isApplyDesign', 'Mage_Catalog_Model_Design'),
    $this->_getRule('_isApplyFor', 'Mage_Catalog_Model_Design'),
    $this->_getRule('_isPositiveDecimalNumber', 'Mage_Shipping_Model_Resource_Carrier_Tablerate'),
    $this->_getRule('_loadOldRates', 'Mage_Tax_Model_Resource_Setup'),
    $this->_getRule('_needSubtractShippingTax'),
    $this->_getRule('_needSubtractTax'),
    $this->_getRule('_needToAddDummy'),
    $this->_getRule('_needToAddDummyForShipment'),
    $this->_getRule('_parseDescription', 'Mage_Sales_Model_Order_Pdf_Items_Abstract'),
    $this->_getRule('_parseXmlTrackingResponse', 'Mage_Usa_Model_Shipping_Carrier_Fedex'),
    $this->_getRule('_prepareCondition', 'Mage_CatalogSearch_Model_Advanced'),
    $this->_getRule('_prepareConfigurableProductData', 'Mage_ImportExport_Model_Export_Entity_Product'),
    $this->_getRule('_prepareConfigurableProductPrice', 'Mage_ImportExport_Model_Export_Entity_Product'),
    $this->_getRule('_prepareOptionsForCart', 'Mage_Catalog_Model_Product_Type_Abstract'),
    $this->_getRule('_preparePackageTheme', 'Mage_Widget_Model_Widget_Instance'),
    $this->_getRule('_processItem', 'Mage_Weee_Model_Total_Quote_Weee'),
    $this->_getRule('_processShippingAmount'),
    $this->_getRule('_processValidateCustomer', 'Mage_Checkout_Model_Type_Onepage'),
    $this->_getRule('_putCustomerIntoQuote', 'Mage_Adminhtml_Model_Sales_Order_Create'),
    $this->_getRule('_quoteRow', 'Mage_Backup_Model_Resource_Db'),
    $this->_getRule('_recollectItem', 'Mage_Tax_Model_Sales_Total_Quote_Subtotal'),
    $this->_getRule('_resetItemPriceInclTax'),
    $this->_getRule('_saveCustomerAfterOrder', 'Mage_Adminhtml_Model_Sales_Order_Create'),
    $this->_getRule('_saveCustomers', 'Mage_Adminhtml_Model_Sales_Order_Create'),
    $this->_getRule('_sendUploadResponse', 'Mage_Adminhtml_CustomerController'),
    $this->_getRule('_sendUploadResponse', 'Mage_Adminhtml_Newsletter_SubscriberController'),
    $this->_getRule('_setAttribteValue'),
    $this->_getRule('_sort', 'Mage_Backend_Model_Config_Structure_Converter'),
    $this->_getRule('_usePriceIncludeTax'),
    $this->_getRule('addBackupedFilter'),
    $this->_getRule('addConfigField', 'Mage_Core_Model_Resource_Setup'),
    $this->_getRule('addConstraint', 'Varien_Db_Adapter_Pdo_Mysql'),
    $this->_getRule('addCustomersToAlertQueueAction'),
    $this->_getRule('addCustomerToSegments'),
    $this->_getRule('addGroupByTag', 'Mage_Tag_Model_Resource_Reports_Collection'),
    $this->_getRule('addKey', 'Varien_Db_Adapter_Pdo_Mysql'),
    $this->_getRule('addSaleableFilterToCollection'),
    $this->_getRule('addSearchQfFilter'),
    $this->_getRule('addStoresFilter', 'Mage_Poll_Model_Resource_Poll_Collection'),
    $this->_getRule('addSummary', 'Mage_Tag_Model_Resource_Tag'),
    $this->_getRule('addSummary', 'Mage_Tag_Model_Tag'),
    $this->_getRule('addTemplateData', 'Mage_Newsletter_Model_Queue'),
    $this->_getRule('addToAlersAction'),
    $this->_getRule('addToChildGroup'),
    $this->_getRule('addVisibleFilterToCollection', 'Mage_Catalog_Model_Product_Status'),
    $this->_getRule('addVisibleInCatalogFilterToCollection', null,
        '$collection->setVisibility(Mage_Catalog_Model_Product_Visibility->getVisibleInCatalogIds());'),
    $this->_getRule('addVisibleInSearchFilterToCollection', null,
        '$collection->setVisibility(Mage_Catalog_Model_Product_Visibility->getVisibleInSearchIds());'),
    $this->_getRule('addVisibleInSiteFilterToCollection', null,
        '$collection->setVisibility(Mage_Catalog_Model_Product_Visibility->getVisibleInSiteIds());'),
    $this->_getRule('addWishlistLink', 'Mage_Wishlist_Block_Links'),
    $this->_getRule('addWishListSortOrder', 'Mage_Wishlist_Model_Resource_Item_Collection'),
    $this->_getRule('aggregate', 'Mage_Tag_Model_Resource_Tag'),
    $this->_getRule('aggregate', 'Mage_Tag_Model_Tag'),
    $this->_getRule('applyDesign', 'Mage_Catalog_Model_Design'),
    $this->_getRule('authAdmin'),
    $this->_getRule('authFailed', null, 'Mage_Core_Helper_Http::failHttpAuthentication()'),
    $this->_getRule('authFrontend'),
    $this->_getRule('authValidate', null, 'Mage_Core_Helper_Http::getHttpAuthCredentials()'),
    $this->_getRule('bundlesAction', 'Mage_Adminhtml_Catalog_ProductController'),
    $this->_getRule('calcTaxAmount', 'Mage_Sales_Model_Quote_Item_Abstract'),
    $this->_getRule('canPrint', 'Mage_Checkout_Block_Onepage_Success'),
    $this->_getRule('catalogCategoryChangeProducts', 'Mage_Catalog_Model_Product_Flat_Observer'),
    $this->_getRule('catalogEventProductCollectionAfterLoad', 'Mage_GiftMessage_Model_Observer'),
    $this->_getRule('catalogProductLoadAfter', 'Mage_Bundle_Model_Observer'),
    $this->_getRule('chechAllowedExtension'),
    $this->_getRule('checkConfigurableProducts', 'Mage_Eav_Model_Resource_Entity_Attribute_Collection'),
    $this->_getRule('checkDatabase', 'Mage_Install_Model_Installer_Db'),
    $this->_getRule('checkDateTime', 'Mage_Core_Model_Date'),
    $this->_getRule('cleanDbRow', 'Mage_Core_Model_Resource'),
    $this->_getRule('cloneIndexTable', 'Mage_Index_Model_Resource_Abstract'),
    $this->_getRule('convertOldTaxData', 'Mage_Tax_Model_Resource_Setup'),
    $this->_getRule('convertOldTreeToNew', 'Mage_Catalog_Model_Resource_Setup'),
    $this->_getRule('countChildren', 'Mage_Core_Block_Abstract'),
    $this->_getRule('crear'),
    $this->_getRule('createOrderItem', 'Mage_CatalogInventory_Model_Observer'),
    $this->_getRule('debugRequest', 'Mage_Paypal_Model_Api_Standard'),
    $this->_getRule('deleteProductPrices', 'Mage_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice'),
    $this->_getRule('display', 'Varien_Image_Adapter_Abstract', 'getImage()'),
    $this->_getRule('displayFullSummary', 'Mage_Tax_Model_Config'),
    $this->_getRule('displayTaxColumn', 'Mage_Tax_Model_Config'),
    $this->_getRule('displayZeroTax', 'Mage_Tax_Model_Config'),
    $this->_getRule('drawItem', 'Mage_Catalog_Block_Navigation'),
    $this->_getRule('dropKey', 'Varien_Db_Adapter_Pdo_Mysql'),
    $this->_getRule('editAction', 'Mage_Tag_CustomerController'),
    $this->_getRule('escapeJs', 'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config'),
    $this->_getRule('exportOrderedCsvAction'),
    $this->_getRule('exportOrderedExcelAction'),
    $this->_getRule('fetchItemsCount', 'Mage_Wishlist_Model_Resource_Wishlist'),
    $this->_getRule('fetchRuleRatesForCustomerTaxClass'),
    $this->_getRule('forsedSave'),
    $this->_getRule('generateBlocks', null, 'generateElements()'),
    $this->_getRule('getAccount', 'Mage_GoogleAnalytics_Block_Ga'),
    $this->_getRule('getAclAssert', 'Mage_Admin_Model_Config'),
    $this->_getRule('getAclPrivilegeSet', 'Mage_Admin_Model_Config'),
    $this->_getRule('getAclResourceList', 'Mage_Admin_Model_Config'),
    $this->_getRule('getAclResourceTree', 'Mage_Admin_Model_Config'),
    $this->_getRule('getAddNewButtonHtml', 'Mage_Adminhtml_Block_Catalog_Product'),
    $this->_getRule('getAddToCartItemUrl', 'Mage_Wishlist_Block_Customer_Sidebar'),
    $this->_getRule('getAddToCartUrlBase64', null, '_getAddToCartUrl'),
    $this->_getRule('getAllEntityIds', 'Mage_Rss_Model_Resource_Order'),
    $this->_getRule('getAllEntityTypeCommentIds', 'Mage_Rss_Model_Resource_Order'),
    $this->_getRule('getAllOrderEntityIds', 'Mage_Rss_Model_Resource_Order'),
    $this->_getRule('getAllOrderEntityTypeIds', 'Mage_Rss_Model_Resource_Order'),
    $this->_getRule('getAnonSuffix'),
    $this->_getRule('getAttributesJson', 'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config', 'getAttributes'),
    $this->_getRule('getBaseTaxAmount', 'Mage_Sales_Model_Quote_Item_Abstract'),
    $this->_getRule('getCheckoutMehod', 'Mage_Checkout_Model_Type_Onepage'),
    $this->_getRule('getChild', null, 'Mage_Core_Block_Abstract::getChildBlock()', 'app'),
    $this->_getRule('getChildGroup', null, 'Mage_Core_Block_Abstract::getGroupChildNames()'),
    $this->_getRule('getConfig', 'Mage_Eav_Model_Entity_Attribute_Abstract'),
    $this->_getRule('getCustomerData', 'Mage_Adminhtml_Block_Sales_Order_Create_Form_Account'),
    $this->_getRule('getDataForSave', 'Mage_Wishlist_Model_Item'),
    $this->_getRule('getDebug', 'Mage_Ogone_Model_Api'),
    $this->_getRule('getDebug', 'Mage_Paypal_Model_Api_Abstract'),
    $this->_getRule('getDirectOutput', 'Mage_Core_Model_Layout'),
    $this->_getRule('getEntityIdsToIncrementIds', 'Mage_Rss_Model_Resource_Order'),
    $this->_getRule('getEntityTypeIdsToTypes', 'Mage_Rss_Model_Resource_Order'),
    $this->_getRule('getFacets'),
    $this->_getRule('getFallbackTheme'),
    $this->_getRule('getFormated', null, 'getFormated(true) -> format(\'html\'), getFormated() -> format(\'text\')'),
    $this->_getRule('getFormObject', 'Mage_Adminhtml_Block_Widget_Form'),
    $this->_getRule('getGiftmessageHtml', 'Mage_Adminhtml_Block_Sales_Order_View_Tab_Info'),
    $this->_getRule('getHtmlFormat', 'Mage_Customer_Model_Address_Abstract'),
    $this->_getRule('getIsActiveAanalytics', null, 'getOnsubmitJs'),
    $this->_getRule('getIsAjaxRequest', 'Mage_Core_Model_Translate_Inline'),
    $this->_getRule('getIsEngineAvailable'),
    $this->_getRule('getIsGlobal', 'Mage_Eav_Model_Entity_Attribute_Abstract'),
    $this->_getRule('getIsInStock', 'Mage_Checkout_Block_Cart_Item_Renderer'),
    $this->_getRule('getItemRender', 'Mage_Checkout_Block_Cart_Abstract'),
    $this->_getRule('getJoinFlag', 'Mage_Tag_Model_Resource_Customer_Collection'),
    $this->_getRule('getJoinFlag', 'Mage_Tag_Model_Resource_Product_Collection'),
    $this->_getRule('getJoinFlag', 'Mage_Tag_Model_Resource_Tag_Collection'),
    $this->_getRule('getKeyList', 'Varien_Db_Adapter_Pdo_Mysql'),
    $this->_getRule('getLanguages', 'Mage_Install_Block_Begin'),
    $this->_getRule('getLayoutFilename', null, 'getFilename'),
    $this->_getRule('getLifeTime', 'Mage_Core_Model_Resource_Session'),
    $this->_getRule('getLocaleBaseDir', 'Mage_Core_Model_Design_Package'),
    $this->_getRule('getMail', 'Mage_Newsletter_Model_Template'),
    $this->_getRule('getMaxQueryLenght'),
    $this->_getRule('getMenuItemLabel', 'Mage_Admin_Model_Config'),
    $this->_getRule('getMergedCssUrl'),
    $this->_getRule('getMergedJsUrl'),
    $this->_getRule('getMinQueryLenght'),
    $this->_getRule('getNeedUsePriceExcludeTax', null, 'Mage_Tax_Model_Config::priceIncludesTax()'),
    $this->_getRule('getOneBalanceTotal'),
    $this->_getRule('getOrderHtml', 'Mage_GoogleAnalytics_Block_Ga'),
    $this->_getRule('getOrderId', 'Mage_Checkout_Block_Onepage_Success'),
    $this->_getRule('getOrderId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_getRule('getOriginalHeigh', null, 'getOriginalHeight'),
    $this->_getRule('getParentProductIds', 'Mage_Catalog_Model_Resource_Product'),
    $this->_getRule('getPriceFormatted', 'Mage_Adminhtml_Block_Customer_Edit_Tab_View_Sales'),
    $this->_getRule('getPrices', 'Mage_Bundle_Model_Product_Price'),
    $this->_getRule('getPricesDependingOnTax', 'Mage_Bundle_Model_Product_Price'),
    $this->_getRule('getPrintUrl', 'Mage_Checkout_Block_Onepage_Success'),
    $this->_getRule('getPrintUrl', 'Mage_Sales_Block_Order_Info'),
    $this->_getRule('getProductCollection', 'Mage_Wishlist_Helper_Data'),
    $this->_getRule('getProductCollection', 'Mage_Wishlist_Model_Wishlist'),
    $this->_getRule('getProductsNotInStoreIds'),
    $this->_getRule('getProfile', 'Varien_Convert_Container_Abstract'),
    $this->_getRule('getQuoteItem', 'Mage_Catalog_Model_Product_Option_Type_Default'),
    $this->_getRule('getQuoteItemOption', 'Mage_Catalog_Model_Product_Option_Type_Default'),
    $this->_getRule('getQuoteOrdersHtml', 'Mage_GoogleAnalytics_Block_Ga'),
    $this->_getRule('getRemoveItemUrl', 'Mage_Wishlist_Block_Customer_Sidebar'),
    $this->_getRule('getReorderUrl', 'Mage_Sales_Block_Order_Info'),
    $this->_getRule('getRowId', 'Mage_Adminhtml_Block_Sales_Order_Create_Customer_Grid'),
    $this->_getRule('getRowId', 'Mage_Adminhtml_Block_Widget_Grid'),
    $this->_getRule('getSaveTemplateFlag', 'Mage_Newsletter_Model_Queue'),
    $this->_getRule('getSelectionFinalPrice', 'Mage_Bundle_Model_Product_Price'),
    $this->_getRule('getSecure', 'Mage_Core_Model_Url', 'isSecure'),
    $this->_getRule('getSecure', 'Mage_Backend_Model_Url', 'isSecure'),
    $this->_getRule('getShipId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_getRule('getSortedChildren', null, 'getChildNames'),
    $this->_getRule('getSortedChildBlocks', null, 'getChildNames() + $this->getLayout()->getBlock($name)'),
    $this->_getRule('getStatrupPageUrl'),
    $this->_getRule('getStoreButtonsHtml', 'Mage_Backend_Block_System_Config_Tabs'),
    $this->_getRule('getStoreCurrency', 'Mage_Sales_Model_Order'),
    $this->_getRule('getStoreSelectOptions', 'Mage_Backend_Block_System_Config_Tabs'),
    $this->_getRule('getSuggestedZeroDate'),
    $this->_getRule('getSuggestionsByQuery'),
    $this->_getRule('getSysTmpDir'),
    $this->_getRule('getTaxAmount', 'Mage_Sales_Model_Quote_Item_Abstract'),
    $this->_getRule('getTaxRatesByProductClass', null, '_getAllRatesByProductClass'),
    $this->_getRule('getTemplateFilename', null, 'getFilename'),
    $this->_getRule('getTotalModels', 'Mage_Sales_Model_Quote_Address'),
    $this->_getRule('getTrackId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_getRule('getTrackingInfoByOrder', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_getRule('getTrackingInfoByShip', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_getRule('getTrackingInfoByTrackId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_getRule('getTrackingPopUpUrlByOrderId', null, 'getTrackingPopupUrlBySalesModel'),
    $this->_getRule('getTrackingPopUpUrlByShipId', null, 'getTrackingPopupUrlBySalesModel'),
    $this->_getRule('getTrackingPopUpUrlByTrackId', null, 'getTrackingPopupUrlBySalesModel'),
    $this->_getRule('getUseCacheFilename', 'Mage_Core_Model_App'),
    $this->_getRule('getValidator', 'Mage_SalesRule_Model_Observer'),
    $this->_getRule('getValidatorData', 'Mage_Core_Model_Session_Abstract', 'use _getSessionEnvironment method'),
    $this->_getRule('getValueTable'),
    $this->_getRule('getViewOrderUrl', 'Mage_Checkout_Block_Onepage_Success'),
    $this->_getRule('getWidgetSupportedBlocks', 'Mage_Widget_Model_Widget_Instance'),
    $this->_getRule('getWidgetSupportedTemplatesByBlock', 'Mage_Widget_Model_Widget_Instance'),
    $this->_getRule('hasItems', 'Mage_Wishlist_Helper_Data'),
    $this->_getRule('htmlEscape', null, 'escapeHtml'),
    $this->_getRule('imageAction', 'Mage_Catalog_ProductController'),
    $this->_getRule('importFromTextArray'),
    $this->_getRule('initLabels', 'Mage_Catalog_Model_Resource_Eav_Attribute'),
    $this->_getRule('insertProductPrice', 'Mage_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice'),
    $this->_getRule('isAllowedGuestCheckout', 'Mage_Sales_Model_Quote'),
    $this->_getRule('isAutomaticCleaningAvailable', 'Varien_Cache_Backend_Eaccelerator'),
    $this->_getRule('isCheckoutAvailable', 'Mage_Checkout_Model_Type_Multishipping'),
    $this->_getRule('isFulAmountCovered'),
    $this->_getRule('isLeyeredNavigationAllowed'),
    $this->_getRule('isReadablePopupObject'),
    $this->_getRule('isTemplateAllowedForApplication'),
    $this->_getRule('loadLabel', 'Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute'),
    $this->_getRule('loadParentProductIds', 'Mage_Catalog_Model_Product'),
    $this->_getRule('loadPrices', 'Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute'),
    $this->_getRule('loadProductPrices', 'Mage_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice'),
    $this->_getRule('lockOrderInventoryData', 'Mage_CatalogInventory_Model_Observer'),
    $this->_getRule('logEncryptionKeySave'),
    $this->_getRule('logInvitationSave'),
    $this->_getRule('mergeFiles', 'Mage_Core_Helper_Data'),
    $this->_getRule('order_success_page_view', 'Mage_GoogleAnalytics_Model_Observer'),
    $this->_getRule('orderedAction', 'Mage_Adminhtml_Report_ProductController'),
    $this->_getRule('parseDateTime', 'Mage_Core_Model_Date'),
    $this->_getRule('postDispatchMyAccountSave'),
    $this->_getRule('postDispatchSystemImportExportRun'),
    $this->_getRule('prepareCacheId', 'Mage_Core_Model_App'),
    $this->_getRule('prepareGoogleOptimizerScripts'),
    $this->_getRule('preprocess', 'Mage_Newsletter_Model_Template'),
    $this->_getRule('processBeacon'),
    $this->_getRule('processBeforeVoid', 'Mage_Payment_Model_Method_Abstract'),
    $this->_getRule('processSubst', 'Mage_Core_Model_Store'),
    $this->_getRule('productEventAggregate'),
    $this->_getRule('push', 'Mage_Catalog_Model_Product_Image'),
    $this->_getRule('rebuildCategoryLevels', 'Mage_Catalog_Model_Resource_Setup'),
    $this->_getRule('regenerateSessionId', 'Mage_Core_Model_Session_Abstract'),
    $this->_getRule('refundOrderItem', 'Mage_CatalogInventory_Model_Observer'),
    $this->_getRule('removeCustomerFromSegments'),
    $this->_getRule('revalidateCookie', 'Mage_Core_Model_Session_Abstract_Varien'),
    $this->_getRule('sales_order_afterPlace'),
    $this->_getRule('sales_quote_address_discount_item'),
    $this->_getRule('salesOrderPaymentPlaceEnd'),
    $this->_getRule('saveRow__OLD'),
    $this->_getRule('saveAction', 'Mage_Tag_CustomerController'),
    $this->_getRule('saveSegmentCustomersFromSelect'),
    $this->_getRule('send', 'Mage_Newsletter_Model_Template'),
    $this->_getRule('sendNewPasswordEmail'),
    $this->_getRule('setAnonSuffix'),
    $this->_getRule('setAttributeSetExcludeFilter', 'Mage_Eav_Model_Resource_Entity_Attribute_Collection'),
    $this->_getRule('setBlockAlias'),
    $this->_getRule('setCustomerId', 'Mage_Customer_Model_Resource_Address'),
    $this->_getRule('setIsAjaxRequest', 'Mage_Core_Model_Translate_Inline'),
    $this->_getRule('setJoinFlag', 'Mage_Tag_Model_Resource_Customer_Collection'),
    $this->_getRule('setJoinFlag', 'Mage_Tag_Model_Resource_Product_Collection'),
    $this->_getRule('setJoinFlag', 'Mage_Tag_Model_Resource_Tag_Collection'),
    $this->_getRule('setOrderId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_getRule('setNeedUsePriceExcludeTax', null, 'Mage_Tax_Model_Config::setPriceIncludesTax()'),
    $this->_getRule('setWatermarkHeigth', null, 'setWatermarkHeight'),
    $this->_getRule('getWatermarkHeigth', null, 'getWatermarkHeight'),
    $this->_getRule('setParentBlock'),
    $this->_getRule('setProfile', 'Varien_Convert_Container_Abstract'),
    $this->_getRule('setSaveTemplateFlag', 'Mage_Newsletter_Model_Queue'),
    $this->_getRule('setShipId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_getRule('setTaxGroupFilter'),
    $this->_getRule('setTrackId', 'Mage_Shipping_Block_Tracking_Popup'),
    $this->_getRule('shaCrypt', null, 'Mage_Ogone_Model_Api::getHash'),
    $this->_getRule('shaCryptValidation', null, 'Mage_Ogone_Model_Api::getHash'),
    $this->_getRule('shouldCustomerHaveOneBalance'),
    $this->_getRule('shouldShowOneBalance'),
    $this->_getRule('sortChildren'),
    $this->_getRule('toOptionArray', 'Mage_Cms_Model_Resource_Page_Collection'),
    $this->_getRule('toOptionArray', 'Mage_Sendfriend_Model_Sendfriend'),
    $this->_getRule('truncate', 'Varien_Db_Adapter_Pdo_Mysql'),
    $this->_getRule('unsetBlock'),
    $this->_getRule('unsetJoinFlag', 'Mage_Tag_Model_Resource_Customer_Collection'),
    $this->_getRule('unsetJoinFlag', 'Mage_Tag_Model_Resource_Product_Collection'),
    $this->_getRule('unsetJoinFlag', 'Mage_Tag_Model_Resource_Tag_Collection'),
    $this->_getRule('useValidateRemoteAddr', 'Mage_Core_Model_Session_Abstract'),
    $this->_getRule('useValidateHttpVia', 'Mage_Core_Model_Session_Abstract'),
    $this->_getRule('useValidateHttpXForwardedFor', 'Mage_Core_Model_Session_Abstract'),
    $this->_getRule('useValidateHttpUserAgent', 'Mage_Core_Model_Session_Abstract'),
    $this->_getRule('updateCofigurableProductOptions', 'Mage_Weee_Model_Observer', 'updateConfigurableProductOptions'),
    $this->_getRule('updateTable', 'Mage_Core_Model_Resource_Setup'),
    $this->_getRule('urlEscape', null, 'escapeUrl'),
    $this->_getRule('validateDataArray', 'Varien_Convert_Container_Abstract'),
    $this->_getRule('validateFile', 'Mage_Core_Model_Design_Package'),
    $this->_getRule('validateOrder', 'Mage_Checkout_Model_Type_Onepage'),
    $this->_getRule('prepareAttributesForSave', 'Mage_ImportExport_Model_Import_Entity_Product'),
    $this->_getRule('fetchUpdatesByHandle', 'Mage_Core_Model_Resource_Layout',
        'Mage_Core_Model_Resource_Layout_Update'),
    $this->_getRule('getElementClass', 'Mage_Core_Model_Layout_Update'),
    $this->_getRule('addUpdate', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('asArray', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('asString', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('addHandle', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('removeHandle', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('getHandles', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('addPageHandles', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('getPageHandleParents', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('pageHandleExists', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('getPageHandles', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('getPageHandlesHierarchy', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('getPageHandleLabel', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('getPageHandleType', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('load', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('asSimplexml', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('getFileLayoutUpdatesXml', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('getContainers', 'Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Layout_Merge'),
    $this->_getRule('getPostMaxSize', 'Mage_Adminhtml_Block_Media_Uploader',
        'Magento_File_Size::getPostMaxSize()'),
    $this->_getRule('getUploadMaxSize', 'Mage_Adminhtml_Block_Media_Uploader',
        'Magento_File_Size::getUploadMaxSize()'),
    $this->_getRule('getDataMaxSize'),
    $this->_getRule('getDataMaxSizeInBytes', 'Mage_Adminhtml_Block_Media_Uploader',
        'Magento_File_Size::getMaxFileSize()'),
    $this->_getRule('_getBytesIniValue', 'Mage_Catalog_Model_Product_Option_Type_File'),
    $this->_getRule('_getUploadMaxFilesize', 'Mage_Catalog_Model_Product_Option_Type_File'),
    $this->_getRule('_bytesToMbytes', 'Mage_Catalog_Model_Product_Option_Type_File'),
    $this->_getRule('getMaxUploadSize', 'Mage_ImportExport_Helper_Data', 'getMaxUploadSizeMessage'),
    $this->_getRule('prepareRedirect', 'Mage_Core_Controller_Varien_Exception'),
    $this->_getRule('getPostMaxSize', 'Mage_Adminhtml_Block_Media_Uploader',
        'Magento_File_Size::getPostMaxSize()'),
    $this->_getRule('getUploadMaxSize', 'Mage_Adminhtml_Block_Media_Uploader',
        'Magento_File_Size::getUploadMaxSize()'),
    $this->_getRule('getDataMaxSize'),
    $this->_getRule('getDataMaxSizeInBytes', 'Mage_Adminhtml_Block_Media_Uploader',
        'Magento_File_Size::getMaxFileSize()'),
    $this->_getRule('_getBytesIniValue', 'Mage_Catalog_Model_Product_Option_Type_File'),
    $this->_getRule('_getUploadMaxFilesize', 'Mage_Catalog_Model_Product_Option_Type_File'),
    $this->_getRule('_bytesToMbytes', 'Mage_Catalog_Model_Product_Option_Type_File'),
    $this->_getRule('getMaxUploadSize', 'Mage_ImportExport_Helper_Data', 'getMaxUploadSizeMessage'),
    $this->_getRule('getOptions', 'Mage_Core_Model_Design_Source_Design',
        'Mage_Core_Model_Theme::getThemeCollectionOptionArray'),
    $this->_getRule('getThemeOptions', 'Mage_Core_Model_Design_Source_Design',
        'Mage_Core_Model_Theme::getThemeCollectionOptionArray'),
    $this->_getRule('isThemeCompatible', 'Mage_Core_Model_Design_Package', 'Mage_Core_Model_Theme::isThemeCompatible'),
    $this->_getRule('setPackageTheme', 'Mage_Widget_Model_Widget_Instance', 'setThemeId'),
    $this->_getRule('getPackageTheme', 'Mage_Widget_Model_Widget_Instance', 'getThemeId'),
    $this->_getRule('getPackage', 'Mage_Widget_Model_Widget_Instance'),
    $this->_getRule('getTheme', 'Mage_Widget_Model_Widget_Instance'),
    $this->_getRule('_parsePackageTheme', 'Mage_Widget_Model_Widget_Instance'),
    $this->_getRule('getHeaderText', 'Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search'),
    $this->_getRule('getButtonsHtml', 'Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search'),
    $this->_getRule('getHeaderCssClass', 'Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search'),
);
