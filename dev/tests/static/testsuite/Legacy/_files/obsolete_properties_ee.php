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
    $this->_getRule('_eventData', 'Enterprise_Logging_Block_Adminhtml_Container'),
    $this->_getRule('_customerSegments', 'Enterprise_CustomerSegment_Model_Customer'),
    $this->_getRule('_limit', 'Enterprise_Search_Model_Resource_Index'),
    $this->_getRule('_amountCache', 'Enterprise_GiftCard_Block_Catalog_Product_Price'),
    $this->_getRule('_minMaxCache', 'Enterprise_GiftCard_Block_Catalog_Product_Price'),
    $this->_getRule('_skipFields', 'Enterprise_Logging_Model_Processor'),
    $this->_getRule('_layoutUpdate', 'Enterprise_WebsiteRestriction_IndexController'),
);
