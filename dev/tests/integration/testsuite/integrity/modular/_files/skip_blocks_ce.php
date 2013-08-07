<?php
/**
 * List of blocks to be skipped from instantiation test
 *
 * Format: array('Block_Class_Name', ...)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    // Blocks with abstract constructor arguments
    'Mage_Adminhtml_Block_System_Email_Template',
    'Mage_Adminhtml_Block_System_Email_Template_Edit',
    'Mage_Backend_Block_System_Config_Edit',
    'Mage_Backend_Block_System_Config_Form',
    'Mage_Backend_Block_System_Config_Tabs',
    // Fails because of bug in Mage_Webapi_Model_Acl_Loader_Resource_ConfigReader constructor
    'Mage_Adminhtml_Block_Cms_Page',
    'Mage_Adminhtml_Block_Cms_Page_Edit',
    'Mage_Adminhtml_Block_Sales_Order',
    'Mage_Oauth_Block_Adminhtml_Oauth_Consumer',
    'Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Grid',
    'Mage_Paypal_Block_Adminhtml_Settlement_Report',
    'Mage_Sales_Block_Adminhtml_Billing_Agreement_View',
    'Mage_User_Block_Role_Tab_Edit',
    'Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource',
);
