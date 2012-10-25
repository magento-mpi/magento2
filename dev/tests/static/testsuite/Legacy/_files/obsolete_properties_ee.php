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
    $this->_addRule('_eventData', 'Enterprise_Logging_Block_Adminhtml_Container'),
    $this->_addRule('_customerSegments', 'Enterprise_CustomerSegment_Model_Customer'),
    $this->_addRule('_limit', 'Enterprise_Search_Model_Resource_Index'),
    $this->_addRule('_amountCache', 'Enterprise_GiftCard_Block_Catalog_Product_Price'),
    $this->_addRule('_minMaxCache', 'Enterprise_GiftCard_Block_Catalog_Product_Price'),
    $this->_addRule('_skipFields', 'Enterprise_Logging_Model_Processor'),
    $this->_addRule('_layoutUpdate', 'Enterprise_WebsiteRestriction_IndexController'),
);
