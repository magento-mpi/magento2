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
    $this->_addRule('_anonSuffix'),
    $this->_addRule('_isAnonymous'),
    $this->_addRule('decoratedIsFirst', null, 'getDecoratedIsFirst'),
    $this->_addRule('decoratedIsEven', null, 'getDecoratedIsEven'),
    $this->_addRule('decoratedIsOdd', null, 'getDecoratedIsOdd'),
    $this->_addRule('decoratedIsLast', null, 'getDecoratedIsLast'),
    $this->_addRule('_alias', 'Mage_Core_Block_Abstract'),
    $this->_addRule('_children', 'Mage_Core_Block_Abstract'),
    $this->_addRule('_childrenHtmlCache', 'Mage_Core_Block_Abstract'),
    $this->_addRule('_childGroups', 'Mage_Core_Block_Abstract'),
    $this->_addRule('_currencyNameTable'),
    $this->_addRule('_combineHistory'),
    $this->_addRule('_searchTextFields'),
    $this->_addRule('_skipFieldsByModel'),
    $this->_addRule('_imageFields', 'Mage_Catalog_Model_Convert_Adapter_Product'),
    $this->_addRule('_parent', 'Mage_Core_Block_Abstract'),
    $this->_addRule('_parentBlock', 'Mage_Core_Block_Abstract'),
    $this->_addRule('_setAttributes', 'Mage_Catalog_Model_Product_Type_Abstract'),
    $this->_addRule('_storeFilter', 'Mage_Catalog_Model_Product_Type_Abstract'),
    $this->_addRule('_addMinimalPrice', 'Mage_Catalog_Model_Resource_Product_Collection'),
    $this->_addRule('_checkedProductsQty', 'Mage_CatalogInventory_Model_Observer'),
    $this->_addRule('_baseDirCache', 'Mage_Core_Model_Config'),
    $this->_addRule('_customEtcDir', 'Mage_Core_Model_Config'),
    $this->_addRule('static', 'Mage_Core_Model_Email_Template_Filter'),
    $this->_addRule('_loadDefault', 'Mage_Core_Model_Resource_Store_Collection'),
    $this->_addRule('_loadDefault', 'Mage_Core_Model_Resource_Store_Group_Collection'),
    $this->_addRule('_loadDefault', 'Mage_Core_Model_Resource_Website_Collection'),
    $this->_addRule('_addresses', 'Mage_Customer_Model_Customer'),
    $this->_addRule('_currency', 'Mage_GoogleCheckout_Model_Api_Xml_Checkout'),
    $this->_addRule('_saveTemplateFlag', 'Mage_Newsletter_Model_Queue'),
    $this->_addRule('_ratingOptionTable', 'Mage_Rating_Model_Resource_Rating_Option_Collection'),
    $this->_addRule('_entityTypeIdsToTypes'),
    $this->_addRule('_entityIdsToIncrementIds'),
    $this->_addRule('_isFirstTimeProcessRun', 'Mage_SalesRule_Model_Validator'),
    $this->_addRule('_shipTable', 'Mage_Shipping_Model_Resource_Carrier_Tablerate_Collection'),
    $this->_addRule('_designProductSettingsApplied'),
    $this->_addRule('_order', 'Mage_Checkout_Block_Onepage_Success'),
    $this->_addRule('_track_id'),
    $this->_addRule('_order_id'),
    $this->_addRule('_ship_id'),
    $this->_addRule('_sortedChildren'),
    $this->_addRule('_sortInstructions'),
);
