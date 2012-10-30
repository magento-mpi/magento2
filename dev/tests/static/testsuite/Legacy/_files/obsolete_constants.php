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
    $this->_addRule('GALLERY_IMAGE_TABLE', 'Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media'),
    $this->_addRule('DEFAULT_VALUE_TABLE_PREFIX'),
    $this->_addRule('CATEGORY_APPLY_CATEGORY_AND_PRODUCT_RECURSIVE'),
    $this->_addRule('CATEGORY_APPLY_CATEGORY_ONLY'),
    $this->_addRule('CATEGORY_APPLY_CATEGORY_AND_PRODUCT_ONLY'),
    $this->_addRule('CATEGORY_APPLY_CATEGORY_RECURSIVE'),
    $this->_addRule('BACKORDERS_BELOW'),
    $this->_addRule('BACKORDERS_YES'),
    $this->_addRule('XML_PATH_DEFAULT_COUNTRY', 'Mage_Core_Model_Locale'),
    $this->_addRule('XML_PATH_SENDING_SET_RETURN_PATH', 'Mage_Newsletter_Model_Subscriber'),
    $this->_addRule('CHECKSUM_KEY_NAME'),
    $this->_addRule('XML_PATH_COUNTRY_DEFAULT', 'Mage_Paypal_Model_System_Config_Backend_MerchantCountry'),
    $this->_addRule('ENTITY_PRODUCT', 'Mage_Review_Model_Review'),
    $this->_addRule('CHECKOUT_METHOD_REGISTER'),
    $this->_addRule('CHECKOUT_METHOD_GUEST'),
    $this->_addRule('CONFIG_XML_PATH_SHOW_IN_CATALOG'),
    $this->_addRule('CONFIG_XML_PATH_DEFAULT_PRODUCT_TAX_GROUP'),
    $this->_addRule('CONFIG_XML_PATH_DISPLAY_TAX_COLUMN'),
    $this->_addRule('CONFIG_XML_PATH_DISPLAY_FULL_SUMMARY'),
    $this->_addRule('CONFIG_XML_PATH_DISPLAY_ZERO_TAX'),
    $this->_addRule('EXCEPTION_CODE_IS_GROUPED_PRODUCT'),
    $this->_addRule('Mage_Rss_Block_Catalog_NotifyStock::CACHE_TAG'),
    $this->_addRule('Mage_Rss_Block_Catalog_Review::CACHE_TAG'),
    $this->_addRule('Mage_Rss_Block_Order_New::CACHE_TAG'),
    $this->_addRule('REGISTRY_FORM_PARAMS_KEY', null, 'direct value'),
    $this->_addRule('TYPE_TINYINT', null, 'Varien_Db_Ddl_Table::TYPE_SMALLINT'),
    $this->_addRule('TYPE_CHAR', null, 'Varien_Db_Ddl_Table::TYPE_TEXT'),
    $this->_addRule('TYPE_VARCHAR', null, 'Varien_Db_Ddl_Table::TYPE_TEXT'),
    $this->_addRule('TYPE_LONGVARCHAR', null, 'Varien_Db_Ddl_Table::TYPE_TEXT'),
    $this->_addRule('TYPE_CLOB', null, 'Varien_Db_Ddl_Table::TYPE_TEXT'),
    $this->_addRule('TYPE_DOUBLE', null, 'Varien_Db_Ddl_Table::TYPE_FLOAT'),
    $this->_addRule('TYPE_REAL', null, 'Varien_Db_Ddl_Table::TYPE_FLOAT'),
    $this->_addRule('TYPE_TIME', null, 'Varien_Db_Ddl_Table::TYPE_TIMESTAMP'),
    $this->_addRule('TYPE_BINARY', null, 'Varien_Db_Ddl_Table::TYPE_BLOB'),
    $this->_addRule('TYPE_LONGVARBINARY', null, 'Varien_Db_Ddl_Table::TYPE_BLOB'),
    $this->_addRule('HASH_ALGO'),
    $this->_addRule('SEESION_MAX_COOKIE_LIFETIME'),
);
