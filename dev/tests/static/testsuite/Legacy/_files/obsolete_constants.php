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
    $this->_getRule('GALLERY_IMAGE_TABLE', 'Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media'),
    $this->_getRule('DEFAULT_VALUE_TABLE_PREFIX'),
    $this->_getRule('CATEGORY_APPLY_CATEGORY_AND_PRODUCT_RECURSIVE'),
    $this->_getRule('CATEGORY_APPLY_CATEGORY_ONLY'),
    $this->_getRule('CATEGORY_APPLY_CATEGORY_AND_PRODUCT_ONLY'),
    $this->_getRule('CATEGORY_APPLY_CATEGORY_RECURSIVE'),
    $this->_getRule('BACKORDERS_BELOW'),
    $this->_getRule('BACKORDERS_YES'),
    $this->_getRule('XML_PATH_DEFAULT_COUNTRY', 'Mage_Core_Model_Locale'),
    $this->_getRule('XML_PATH_SENDING_SET_RETURN_PATH', 'Mage_Newsletter_Model_Subscriber'),
    $this->_getRule('CHECKSUM_KEY_NAME'),
    $this->_getRule('XML_PATH_COUNTRY_DEFAULT', 'Mage_Paypal_Model_System_Config_Backend_MerchantCountry'),
    $this->_getRule('ENTITY_PRODUCT', 'Mage_Review_Model_Review'),
    $this->_getRule('CHECKOUT_METHOD_REGISTER'),
    $this->_getRule('CHECKOUT_METHOD_GUEST'),
    $this->_getRule('CONFIG_XML_PATH_SHOW_IN_CATALOG'),
    $this->_getRule('CONFIG_XML_PATH_DEFAULT_PRODUCT_TAX_GROUP'),
    $this->_getRule('CONFIG_XML_PATH_DISPLAY_TAX_COLUMN'),
    $this->_getRule('CONFIG_XML_PATH_DISPLAY_FULL_SUMMARY'),
    $this->_getRule('CONFIG_XML_PATH_DISPLAY_ZERO_TAX'),
    $this->_getRule('EXCEPTION_CODE_IS_GROUPED_PRODUCT'),
    $this->_getRule('Mage_Rss_Block_Catalog_NotifyStock::CACHE_TAG'),
    $this->_getRule('Mage_Rss_Block_Catalog_Review::CACHE_TAG'),
    $this->_getRule('Mage_Rss_Block_Order_New::CACHE_TAG'),
    $this->_getRule('REGISTRY_FORM_PARAMS_KEY', null, 'direct value'),
    $this->_getRule('TYPE_TINYINT', null, 'Varien_Db_Ddl_Table::TYPE_SMALLINT'),
    $this->_getRule('TYPE_CHAR', null, 'Varien_Db_Ddl_Table::TYPE_TEXT'),
    $this->_getRule('TYPE_VARCHAR', null, 'Varien_Db_Ddl_Table::TYPE_TEXT'),
    $this->_getRule('TYPE_LONGVARCHAR', null, 'Varien_Db_Ddl_Table::TYPE_TEXT'),
    $this->_getRule('TYPE_CLOB', null, 'Varien_Db_Ddl_Table::TYPE_TEXT'),
    $this->_getRule('TYPE_DOUBLE', null, 'Varien_Db_Ddl_Table::TYPE_FLOAT'),
    $this->_getRule('TYPE_REAL', null, 'Varien_Db_Ddl_Table::TYPE_FLOAT'),
    $this->_getRule('TYPE_TIME', null, 'Varien_Db_Ddl_Table::TYPE_TIMESTAMP'),
    $this->_getRule('TYPE_BINARY', null, 'Varien_Db_Ddl_Table::TYPE_BLOB'),
    $this->_getRule('TYPE_LONGVARBINARY', null, 'Varien_Db_Ddl_Table::TYPE_BLOB'),
    $this->_getRule('HASH_ALGO'),
    $this->_getRule('SEESION_MAX_COOKIE_LIFETIME'),
    $this->_getRule('URL_TYPE_SKIN'),
);
