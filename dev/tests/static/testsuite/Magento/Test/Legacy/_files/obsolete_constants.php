<?php
/**
 * Obsolete constants
 *
 * Format: array(<constant_name>[, <class_scope> = ''[, <replacement>]])
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    array('BACKORDERS_BELOW'),
    array('BACKORDERS_YES'),
    array('CACHE_TAG', 'Magento_Api_Model_Config', 'Magento_Api_Model_Cache_Type::CACHE_TAG'),
    array('CACHE_TAG', 'Magento_Core_Model_AppInterface'),
    array('CACHE_TAG', 'Magento_Core_Model_Resource_Db_Collection_Abstract',
        'Magento_Core_Model_Cache_Type_Collection::CACHE_TAG'
    ),
    array('CACHE_TAG', 'Magento_Core_Model_Translate', 'Magento_Core_Model_Cache_Type_Translate::CACHE_TAG'),
    array('CACHE_TAG', 'Magento_Rss_Block_Catalog_NotifyStock'),
    array('CACHE_TAG', 'Magento_Rss_Block_Catalog_Review'),
    array('CACHE_TAG', 'Magento_Rss_Block_Order_New'),
    array('CATEGORY_APPLY_CATEGORY_AND_PRODUCT_ONLY'),
    array('CATEGORY_APPLY_CATEGORY_AND_PRODUCT_RECURSIVE'),
    array('CATEGORY_APPLY_CATEGORY_ONLY'),
    array('CATEGORY_APPLY_CATEGORY_RECURSIVE'),
    array('CHECKOUT_METHOD_GUEST'),
    array('CHECKOUT_METHOD_REGISTER'),
    array('CHECKSUM_KEY_NAME'),
    array('CONFIG_TEMPLATE_INSTALL_DATE', 'Magento_Core_Model_Config',
        'Magento_Core_Model_Config_Primary::CONFIG_TEMPLATE_INSTALL_DATE'
    ),
    array('CONFIG_XML_PATH_DEFAULT_PRODUCT_TAX_GROUP'),
    array('CONFIG_XML_PATH_DISPLAY_FULL_SUMMARY'),
    array('CONFIG_XML_PATH_DISPLAY_TAX_COLUMN'),
    array('CONFIG_XML_PATH_DISPLAY_ZERO_TAX'),
    array('CONFIG_XML_PATH_SHOW_IN_CATALOG'),
    array('DEFAULT_CURRENCY', 'Magento_Core_Model_Locale', 'Magento_Core_Model_LocaleInterface::DEFAULT_CURRENCY'),
    array('DEFAULT_ERROR_HANDLER', 'Magento_Core_Model_App', 'Mage::DEFAULT_ERROR_HANDLER'),
    array('DEFAULT_LOCALE', 'Magento_Core_Model_Locale', 'Magento_Core_Model_LocaleInterface::DEFAULT_LOCALE'),
    array('DEFAULT_THEME_NAME', 'Magento_Core_Model_Design_PackageInterface'),
    array('DEFAULT_THEME_NAME', 'Magento_Core_Model_Design_Package'),
    array('DEFAULT_TIMEZONE', 'Magento_Core_Model_Locale', 'Mage::DEFAULT_TIMEZONE'),
    array('DEFAULT_VALUE_TABLE_PREFIX'),
    array('ENTITY_PRODUCT', 'Magento_Review_Model_Review'),
    array('EXCEPTION_CODE_IS_GROUPED_PRODUCT'),
    array('FALLBACK_MAP_DIR', 'Magento_Core_Model_Design_PackageInterface'),
    array('FORMAT_TYPE_FULL', 'Magento_Core_Model_Locale', 'Magento_Core_Model_LocaleInterface::FORMAT_TYPE_FULL'),
    array('FORMAT_TYPE_LONG', 'Magento_Core_Model_Locale', 'Magento_Core_Model_LocaleInterface::FORMAT_TYPE_LONG'),
    array('FORMAT_TYPE_MEDIUM', 'Magento_Core_Model_Locale', 'Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM'),
    array('FORMAT_TYPE_SHORT', 'Magento_Core_Model_Locale', 'Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT'),
    array('GALLERY_IMAGE_TABLE', 'Magento_Catalog_Model_Resource_Product_Attribute_Backend_Media'),
    array('HASH_ALGO'),
    array('INIT_OPTION_DIRS', 'Magento_Core_Model_App', 'Mage::PARAM_APP_DIRS'),
    array('INIT_OPTION_REQUEST', 'Mage'),
    array('INIT_OPTION_REQUEST', 'Magento_Core_Model_App'),
    array('INIT_OPTION_RESPONSE', 'Mage'),
    array('INIT_OPTION_RESPONSE', 'Magento_Core_Model_App'),
    array('INIT_OPTION_SCOPE_CODE', 'Magento_Core_Model_App', 'Mage::PARAM_RUN_CODE'),
    array('INIT_OPTION_SCOPE_TYPE', 'Magento_Core_Model_App', 'Mage::PARAM_RUN_TYPE'),
    array('INIT_OPTION_URIS', 'Magento_Core_Model_App', 'Mage::PARAM_APP_URIS'),
    array('INSTALLER_HOST_RESPONSE', 'Magento_Install_Model_Installer'),
    array('LAYOUT_GENERAL_CACHE_TAG', 'Magento_Core_Model_Layout_Merge', 'Magento_Core_Model_Cache_Type_Layout::CACHE_TAG'),
    array('LOCALE_CACHE_KEY', 'Magento_Adminhtml_Block_Page_Footer'),
    array('LOCALE_CACHE_LIFETIME', 'Magento_Adminhtml_Block_Page_Footer'),
    array('LOCALE_CACHE_TAG', 'Magento_Adminhtml_Block_Page_Footer'),
    array('PATH_PREFIX_CUSTOMIZATION', 'Magento_Core_Model_Theme'),
    array('PATH_PREFIX_CUSTOMIZED', 'Magento_Core_Model_Theme_Files'),
    array('PUBLIC_BASE_THEME_DIR', 'Magento_Core_Model_Design_PackageInterface'),
    array('PUBLIC_CACHE_TAG', 'Magento_Core_Model_Design_PackageInterface'),
    array('PUBLIC_MODULE_DIR', 'Magento_Core_Model_Design_PackageInterface',
        'Magento_Core_Model_Design_Package::PUBLIC_MODULE_DIR'
    ),
    array('PUBLIC_THEME_DIR', 'Magento_Core_Model_Design_PackageInterface',
        'Magento_Core_Model_Design_Package::PUBLIC_THEME_DIR'
    ),
    array('PUBLIC_VIEW_DIR', 'Magento_Core_Model_Design_PackageInterface',
        'Magento_Core_Model_Design_Package::PUBLIC_VIEW_DIR'
    ),
    array('REGISTRY_FORM_PARAMS_KEY', null, 'direct value'),
    array('SCOPE_TYPE_GROUP', 'Magento_Core_Model_App', 'Magento_Core_Model_StoreManagerInterface::SCOPE_TYPE_GROUP'),
    array('SCOPE_TYPE_STORE', 'Magento_Core_Model_App', 'Magento_Core_Model_StoreManagerInterface::SCOPE_TYPE_STORE'),
    array('SCOPE_TYPE_WEBSITE', 'Magento_Core_Model_App', 'Magento_Core_Model_StoreManagerInterface::SCOPE_TYPE_WEBSITE'),
    array('SEESION_MAX_COOKIE_LIFETIME'),
    array('TYPE_BINARY', null, 'Magento_DB_Ddl_Table::TYPE_BLOB'),
    array('TYPE_CHAR', null, 'Magento_DB_Ddl_Table::TYPE_TEXT'),
    array('TYPE_CLOB', null, 'Magento_DB_Ddl_Table::TYPE_TEXT'),
    array('TYPE_DOUBLE', null, 'Magento_DB_Ddl_Table::TYPE_FLOAT'),
    array('TYPE_LONGVARBINARY', null, 'Magento_DB_Ddl_Table::TYPE_BLOB'),
    array('TYPE_LONGVARCHAR', null, 'Magento_DB_Ddl_Table::TYPE_TEXT'),
    array('TYPE_REAL', null, 'Magento_DB_Ddl_Table::TYPE_FLOAT'),
    array('TYPE_TIME', null, 'Magento_DB_Ddl_Table::TYPE_TIMESTAMP'),
    array('TYPE_TINYINT', null, 'Magento_DB_Ddl_Table::TYPE_SMALLINT'),
    array('TYPE_VARCHAR', null, 'Magento_DB_Ddl_Table::TYPE_TEXT'),
    array('URL_TYPE_SKIN'),
    array('XML_NODE_PAGE_TEMPLATE_FILTER', 'Magento_Cms_Helper_Data'),
    array('XML_NODE_BLOCK_TEMPLATE_FILTER', 'Magento_Cms_Helper_Data'),
    array('XML_NODE_RELATED_CACHE', 'Magento_Widget_Model_Widget_Instance'),
    array('XML_CHARSET_NODE', 'Magento_SalesRule_Helper_Coupon'),
    array('XML_CHARSET_SEPARATOR', 'Magento_SalesRule_Helper_Coupon'),
    array('XML_NODE_RELATED_CACHE', 'Magento_CatalogRule_Model_Rule'),
    array('XML_NODE_ATTRIBUTE_NODES', 'Magento_Catalog_Model_Resource_Product_Flat_Indexer',
        'XML_NODE_ATTRIBUTE_GROUPS'),
    array('XML_PATH_ALLOW_CODES', 'Magento_Core_Model_Locale', 'Magento_Core_Model_LocaleInterface::XML_PATH_ALLOW_CODES'),
    array('XML_PATH_ALLOW_CURRENCIES', 'Magento_Core_Model_Locale',
        'Magento_Core_Model_LocaleInterface::XML_PATH_ALLOW_CURRENCIES'
    ),
    array('XML_PATH_ALLOW_CURRENCIES_INSTALLED', 'Magento_Core_Model_Locale',
        'Magento_Core_Model_LocaleInterface::XML_PATH_ALLOW_CURRENCIES_INSTALLED'),
    array('XML_PATH_ALLOW_DUPLICATION', 'Magento_Core_Model_Design_PackageInterface',
        'Magento_Core_Model_Design_Package::XML_PATH_ALLOW_DUPLICATION'
    ),
    array('XML_PATH_ALLOW_MAP_UPDATE', 'Mage_Core_Model_Design_PackageInterface'),
    array('XML_PATH_BACKEND_FRONTNAME', 'Mage_Backend_Helper_Data'),
    array('XML_PATH_CACHE_BETA_TYPES'),
    array('XML_PATH_CHECK_EXTENSIONS', 'Magento_Install_Model_Config'),
    array('XML_PATH_CONTEXT_MENU_LAYOUTS', 'Magento_VersionsCms_Model_Hierarchy_Config'),
    array('XML_PATH_COUNTRY_DEFAULT', 'Magento_Paypal_Model_System_Config_Backend_MerchantCountry'),
    array('XML_PATH_DEFAULT_COUNTRY', 'Magento_Core_Model_Locale'),
    array('XML_PATH_DEFAULT_LOCALE', 'Magento_Core_Model_Locale',
        'Magento_Core_Model_LocaleInterface::XML_PATH_DEFAULT_LOCALE'
    ),
    array('XML_PATH_DEFAULT_TIMEZONE', 'Magento_Core_Model_Locale',
        'Magento_Core_Model_LocaleInterface::XML_PATH_DEFAULT_TIMEZONE'
    ),
    array('XML_PATH_INDEXER_DATA', 'Magento_Index_Model_Process'),
    array('XML_PATH_INSTALL_DATE', 'Mage_Core_Model_App', 'Mage_Core_Model_Config_Primary::XML_PATH_INSTALL_DATE'),
    array('XML_PATH_LOCALE_INHERITANCE', 'Mage_Core_Model_Translate'),
    array('XML_PATH_PRODUCT_ATTRIBUTES', 'Magento_Wishlist_Model_Config'),
    array('XML_PATH_PRODUCT_COLLECTION_ATTRIBUTES', 'Magento_Catalog_Model_Config'),
    array('XML_PATH_QUOTE_PRODUCT_ATTRIBUTES', 'Magento_Sales_Model_Quote_Config'),
    array('XML_PATH_SENDING_SET_RETURN_PATH', 'Mage_Newsletter_Model_Subscriber'),
    array('XML_PATH_SKIP_PROCESS_MODULES_UPDATES', 'Mage_Core_Model_App',
        'Mage_Core_Model_Db_UpdaterInterface::XML_PATH_SKIP_PROCESS_MODULES_UPDATES'
    ),
    array('XML_PATH_STATIC_FILE_SIGNATURE', 'Magento_Core_Helper_Data',
        'Magento_Core_Model_Design_Package::XML_PATH_STATIC_FILE_SIGNATURE'
    ),
    array('XML_PATH_THEME', 'Magento_Core_Model_Design_PackageInterface',
        'Magento_Core_Model_Design_Package::XML_PATH_THEME'
    ),
    array('XML_PATH_THEME_ID', 'Magento_Core_Model_Design_PackageInterface',
        'Magento_Core_Model_Design_Package::XML_PATH_THEME_ID'
    ),
    array('XML_STORE_ROUTERS_PATH', 'Mage_Core_Controller_Varien_Front'),
    array('XML_PATH_SESSION_MESSAGE_MODELS', 'Magento_Catalog_Helper_Product_View'),

    array('VALIDATOR_KEY', 'Magento_Core_Model_Session_Abstract',
        'Magento_Core_Model_Session_Validator::VALIDATOR_KEY'
    ),
    array('VALIDATOR_HTTP_USER_AGENT_KEY', 'Magento_Core_Model_Session_Abstract',
        'Magento_Core_Model_Session_Validator::VALIDATOR_HTTP_USER_AGENT_KEY'
    ),
    array('VALIDATOR_HTTP_X_FORVARDED_FOR_KEY', 'Magento_Core_Model_Session_Abstract',
        'Magento_Core_Model_Session_Validator::VALIDATOR_HTTP_X_FORWARDED_FOR_KEY'
    ),
    array('VALIDATOR_HTTP_VIA_KEY', 'Magento_Core_Model_Session_Abstract',
        'Magento_Core_Model_Session_Validator::VALIDATOR_HTTP_VIA_KEY'
    ),
    array('VALIDATOR_REMOTE_ADDR_KEY', 'Magento_Core_Model_Session_Abstract',
        'Magento_Core_Model_Session_Validator::VALIDATOR_REMOTE_ADDR_KEY'
    ),
    array('XML_PATH_USE_REMOTE_ADDR', 'Magento_Core_Model_Session_Abstract',
        'Magento_Core_Model_Session_Validator::XML_PATH_USE_REMOTE_ADDR'
    ),
    array('XML_PATH_USE_HTTP_VIA', 'Magento_Core_Model_Session_Abstract',
        'Magento_Core_Model_Session_Validator::XML_PATH_USE_HTTP_VIA'
    ),
    array('XML_PATH_USE_X_FORWARDED', 'Magento_Core_Model_Session_Abstract',
        'Magento_Core_Model_Session_Validator::XML_PATH_USE_X_FORWARDED'
    ),
    array('XML_PATH_USE_USER_AGENT', 'Magento_Core_Model_Session_Abstract',
        'Magento_Core_Model_Session_Validator::XML_PATH_USE_USER_AGENT'
    ),

    array('XML_NODE_DIRECT_FRONT_NAMES', 'Magento_Core_Controller_Request_Http'),

    array('XML_NODE_USET_AGENT_SKIP', 'Magento_Core_Model_Session_Abstract'),
    array('XML_PAGE_TYPE_RENDER_INHERITED', 'Magento_Core_Controller_Varien_Action'),
    array('XML_PATH_ALLOW_MAP_UPDATE', 'Magento_Core_Model_Design_FileResolution_StrategyPool'),

    array('XML_PATH_WEBAPI_REQUEST_INTERPRETERS', 'Magento_Webapi_Controller_Request_Rest_Interpreter_Factory'),
    array('XML_PATH_WEBAPI_RESPONSE_RENDERS', 'Magento_Webapi_Controller_Response_Rest_Renderer_Factor'),
    array('XML_PATH_THEME'),
    array('XML_PATH_ALLOW_DUPLICATION'),
    array('XML_NODE_BUNDLE_PRODUCT_TYPE', 'Magento_Bundle_Helper_Data'),
    array('XML_PATH_CONFIGURABLE_ALLOWED_TYPES', 'Magento_Catalog_Helper_Product_Configuration'),
    array('XML_PATH_GROUPED_ALLOWED_PRODUCT_TYPES', 'Magento_Catalog_Model_Config'),
    array('PRODUCT_OPTIONS_GROUPS_PATH', 'Magento_Catalog_Model_Config_Source_Product_Options_Type'),
    array('XML_NODE_ADD_FILTERABLE_ATTRIBUTES', 'Magento_Catalog_Helper_Product_Flat'),
    array('XML_NODE_ADD_CHILD_DATA', 'Magento_Catalog_Helper_Product_Flat'),
    array('XML_PATH_CONTENT_TEMPLATE_FILTER', 'Magento_Catalog_Helper_Data'),
    array('XML_NODE_MAX_INDEX_COUNT', 'Magento_Catalog_Model_Resource_Product_Flat_Indexer'),
    array('XML_NODE_ATTRIBUTE_GROUPS', 'Magento_Catalog_Model_Resource_Product_Flat_Indexer'),
    array('XML_PATH_UNASSIGNABLE_ATTRIBUTES', 'Magento_Catalog_Helper_Product'),
    array('XML_PATH_ATTRIBUTES_USED_IN_AUTOGENERATION', 'Magento_Catalog_Helper_Product'),
    array('XML_PATH_PRODUCT_TYPE_SWITCHER_LABEL', 'Magento_Catalog_Helper_Product'),
    array('CONFIG_KEY_ENTITIES', 'Magento_ImportExport_Model_Export'),
    array('CONFIG_KEY_FORMATS', 'Magento_ImportExport_Model_Export'),
    array('CONFIG_KEY_ENTITIES', 'Magento_ImportExport_Model_Import'),
    array('REGEX_RUN_MODEL')
);
