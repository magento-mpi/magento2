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
    'Magento_Adminhtml_Block_System_Email_Template',
    'Magento_Adminhtml_Block_System_Email_Template_Edit',
    'Magento_Backend_Block_System_Config_Edit',
    'Magento_Backend_Block_System_Config_Form',
    'Magento_Backend_Block_System_Config_Tabs',
    'Magento_Review_Block_Form',
    // Fails because of bug in Magento_Webapi_Model_Acl_Loader_Resource_ConfigReader constructor
    'Magento_Adminhtml_Block_Cms_Page',
    'Magento_Adminhtml_Block_Cms_Page_Edit',
    'Magento_Adminhtml_Block_Sales_Order',
    'Magento_Oauth_Block_Adminhtml_Oauth_Consumer',
    'Magento_Oauth_Block_Adminhtml_Oauth_Consumer_Grid',
    'Magento_Paypal_Block_Adminhtml_Settlement_Report',
    'Magento_Sales_Block_Adminhtml_Billing_Agreement_View',
    'Magento_User_Block_Role_Tab_Edit',
    'Magento_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource',
);
