<?php
/**
 * Obsolete class attributes
 *
 * Format: array(<attribute_name>[, <class_scope>[, <replacement>[, <directory>]]])
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    array('_alias', 'Mage_Core_Block_Abstract'),
    array('_anonSuffix'),
    array('_config', 'Mage_Core_Model_Cache', '_app'),
    array('_isAnonymous'),
    array('_children', 'Mage_Core_Block_Abstract'),
    array('_childrenHtmlCache', 'Mage_Core_Block_Abstract'),
    array('_childGroups', 'Mage_Core_Block_Abstract'),
    array('_canUseLocalModules'),
    array('_config', 'Mage_Core_Model_Design_Package'),
    array('_config', 'Mage_Core_Model_Logger', '_dirs'),
    array('_configuration', 'Mage_Index_Model_Lock_Storage', '_dirs'),
    array('_combineHistory'),
    array('_currencyNameTable'),
    array('decoratedIsFirst', null, 'getDecoratedIsFirst'),
    array('decoratedIsEven', null, 'getDecoratedIsEven'),
    array('decoratedIsOdd', null, 'getDecoratedIsOdd'),
    array('decoratedIsLast', null, 'getDecoratedIsLast'),
    array('_distroServerVars'),
    array('_searchTextFields'),
    array('_skipFieldsByModel'),
    array('_parent', 'Mage_Core_Block_Abstract'),
    array('_parentBlock', 'Mage_Core_Block_Abstract'),
    array('_setAttributes', 'Mage_Catalog_Model_Product_Type_Abstract'),
    array('_storeFilter', 'Mage_Catalog_Model_Product_Type_Abstract'),
    array('_addMinimalPrice', 'Mage_Catalog_Model_Resource_Product_Collection'),
    array('_checkedProductsQty', 'Mage_CatalogInventory_Model_Observer'),
    array('_baseDirCache', 'Mage_Core_Model_Config'),
    array('_customEtcDir', 'Mage_Core_Model_Config'),
    array('static', 'Mage_Core_Model_Email_Template_Filter'),
    array('_loadDefault', 'Mage_Core_Model_Resource_Store_Collection'),
    array('_loadDefault', 'Mage_Core_Model_Resource_Store_Group_Collection'),
    array('_loadDefault', 'Mage_Core_Model_Resource_Website_Collection'),
    array('_addresses', 'Mage_Customer_Model_Customer'),
    array('_currency', 'Mage_GoogleCheckout_Model_Api_Xml_Checkout'),
    array('_saveTemplateFlag', 'Mage_Newsletter_Model_Queue'),
    array('_ratingOptionTable', 'Mage_Rating_Model_Resource_Rating_Option_Collection'),
    array('_entityTypeIdsToTypes'),
    array('_entityIdsToIncrementIds'),
    array('_isFirstTimeProcessRun', 'Mage_SalesRule_Model_Validator'),
    array('_shipTable', 'Mage_Shipping_Model_Resource_Carrier_Tablerate_Collection'),
    array('_designProductSettingsApplied'),
    array('_option', 'Mage_Captcha_Helper_Data', '_dirs'),
    array('_options', 'Mage_Core_Model_Config', 'Mage_Core_Model_Dir'),
    array('_optionsMapping', null, 'Mage::getBaseDir($nodeKey)'),
    array('_order', 'Mage_Checkout_Block_Onepage_Success'),
    array('_order_id'),
    array('_ship_id'),
    array('_sortedChildren'),
    array('_sortInstructions'),
    array('_substServerVars'),
    array('_track_id'),
    array('_varSubFolders', null, 'Mage_Core_Model_Dir'),
    array('_viewDir', 'Mage_Core_Block_Template', '_dirs'),
);
