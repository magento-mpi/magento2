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
    $this->_getRule('_anonSuffix'),
    $this->_getRule('_isAnonymous'),
    $this->_getRule('decoratedIsFirst', null, 'getDecoratedIsFirst'),
    $this->_getRule('decoratedIsEven', null, 'getDecoratedIsEven'),
    $this->_getRule('decoratedIsOdd', null, 'getDecoratedIsOdd'),
    $this->_getRule('decoratedIsLast', null, 'getDecoratedIsLast'),
    $this->_getRule('_alias', 'Mage_Core_Block_Abstract'),
    $this->_getRule('_children', 'Mage_Core_Block_Abstract'),
    $this->_getRule('_childrenHtmlCache', 'Mage_Core_Block_Abstract'),
    $this->_getRule('_childGroups', 'Mage_Core_Block_Abstract'),
    $this->_getRule('_currencyNameTable'),
    $this->_getRule('_combineHistory'),
    $this->_getRule('_searchTextFields'),
    $this->_getRule('_skipFieldsByModel'),
    $this->_getRule('_imageFields', 'Mage_Catalog_Model_Convert_Adapter_Product'),
    $this->_getRule('_parent', 'Mage_Core_Block_Abstract'),
    $this->_getRule('_parentBlock', 'Mage_Core_Block_Abstract'),
    $this->_getRule('_setAttributes', 'Mage_Catalog_Model_Product_Type_Abstract'),
    $this->_getRule('_storeFilter', 'Mage_Catalog_Model_Product_Type_Abstract'),
    $this->_getRule('_addMinimalPrice', 'Mage_Catalog_Model_Resource_Product_Collection'),
    $this->_getRule('_checkedProductsQty', 'Mage_CatalogInventory_Model_Observer'),
    $this->_getRule('_baseDirCache', 'Mage_Core_Model_Config'),
    $this->_getRule('_customEtcDir', 'Mage_Core_Model_Config'),
    $this->_getRule('static', 'Mage_Core_Model_Email_Template_Filter'),
    $this->_getRule('_loadDefault', 'Mage_Core_Model_Resource_Store_Collection'),
    $this->_getRule('_loadDefault', 'Mage_Core_Model_Resource_Store_Group_Collection'),
    $this->_getRule('_loadDefault', 'Mage_Core_Model_Resource_Website_Collection'),
    $this->_getRule('_addresses', 'Mage_Customer_Model_Customer'),
    $this->_getRule('_currency', 'Mage_GoogleCheckout_Model_Api_Xml_Checkout'),
    $this->_getRule('_saveTemplateFlag', 'Mage_Newsletter_Model_Queue'),
    $this->_getRule('_ratingOptionTable', 'Mage_Rating_Model_Resource_Rating_Option_Collection'),
    $this->_getRule('_entityTypeIdsToTypes'),
    $this->_getRule('_entityIdsToIncrementIds'),
    $this->_getRule('_isFirstTimeProcessRun', 'Mage_SalesRule_Model_Validator'),
    $this->_getRule('_shipTable', 'Mage_Shipping_Model_Resource_Carrier_Tablerate_Collection'),
    $this->_getRule('_designProductSettingsApplied'),
    $this->_getRule('_order', 'Mage_Checkout_Block_Onepage_Success'),
    $this->_getRule('_track_id'),
    $this->_getRule('_order_id'),
    $this->_getRule('_ship_id'),
    $this->_getRule('_sortedChildren'),
    $this->_getRule('_sortInstructions'),
);
