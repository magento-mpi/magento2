<?php
/**
 * Obsolete classes
 *
 * Format: array(<class_name>[, <replacement>])
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    array('Mage_Admin_Helper_Data', 'Mage_Backend_Helper_Data'),
    array('Mage_Admin_Model_Acl', 'Magento_Acl'),
    array('Mage_Admin_Model_Acl_Role'),
    array('Mage_Admin_Model_Acl_Resource', 'Magento_Acl_Resource'),
    array('Mage_Admin_Model_Acl_Role_Registry', 'Magento_Acl_Role_Registry'),
    array('Mage_Admin_Model_Acl_Role_Generic', 'Mage_User_Model_Acl_Role_Generic'),
    array('Mage_Admin_Model_Acl_Role_Group', 'Mage_User_Model_Acl_Role_Group'),
    array('Mage_Admin_Model_Acl_Role_User', 'Mage_User_Model_Acl_Role_User'),
    array('Mage_Admin_Model_Resource_Acl', 'Mage_User_Model_Resource_Acl'),
    array('Mage_Admin_Model_Observer'),
    array('Mage_Admin_Model_Session', 'Mage_Backend_Model_Auth_Session'),
    array('Mage_Admin_Model_Resource_Acl_Role'),
    array('Mage_Admin_Model_Resource_Acl_Role_Collection'),
    array('Mage_Admin_Model_User', 'Mage_User_Model_User'),
    array('Mage_Admin_Model_Config'),
    array('Mage_Admin_Model_Resource_User', 'Mage_User_Model_Resource_User'),
    array('Mage_Admin_Model_Resource_User_Collection', 'Mage_User_Model_Resource_User_Collection'),
    array('Mage_Admin_Model_Role', 'Mage_User_Model_Role'),
    array('Mage_Admin_Model_Roles', 'Mage_User_Model_Roles'),
    array('Mage_Admin_Model_Rules', 'Mage_User_Model_Rules'),
    array('Mage_Admin_Model_Resource_Role', 'Mage_User_Model_Resource_Role'),
    array('Mage_Admin_Model_Resource_Roles', 'Mage_User_Model_Resource_Roles'),
    array('Mage_Admin_Model_Resource_Rules', 'Mage_User_Model_Resource_Rules'),
    array('Mage_Admin_Model_Resource_Role_Collection', 'Mage_User_Model_Resource_Role_Collection'),
    array('Mage_Admin_Model_Resource_Roles_Collection', 'Mage_User_Model_Resource_Roles_Collection'),
    array('Mage_Admin_Model_Resource_Roles_User_Collection',
        'Mage_User_Model_Resource_Roles_User_Collection'),
    array('Mage_Admin_Model_Resource_Rules_Collection', 'Mage_User_Model_Resource_Rules_Collection'),
    array('Mage_Admin_Model_Resource_Permissions_Collection',
        'Mage_User_Model_Resource_Permissions_Collection'),
    array('Mage_Adminhtml_Block_Abstract', 'Mage_Backend_Block_Abstract'),
    array('Mage_Adminhtml_Block_Api_Edituser'),
    array('Mage_Adminhtml_Block_Api_Tab_Userroles'),
    array('Mage_Adminhtml_Block_Backup_Grid'),
    array('Mage_Adminhtml_Block_Catalog'),
    array('Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Grid'),
    array('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid'),
    array('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group_Grid'),
    array('Mage_Adminhtml_Block_Catalog_Search_Grid'),
    array('Mage_Adminhtml_Block_Newsletter_Problem_Grid'),
    array('Mage_Adminhtml_Block_Newsletter_Queue'),
    array('Mage_Adminhtml_Block_Newsletter_Queue_Grid'),
    array('Mage_Adminhtml_Block_Page_Menu', 'Mage_Backend_Block_Menu'),
    array('Mage_Adminhtml_Block_Permissions_User'),
    array('Mage_Adminhtml_Block_Permissions_User_Grid'),
    array('Mage_Adminhtml_Block_Permissions_User_Edit'),
    array('Mage_Adminhtml_Block_Permissions_User_Edit_Tabs'),
    array('Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main'),
    array('Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Roles'),
    array('Mage_Adminhtml_Block_Permissions_User_Edit_Form'),
    array('Mage_Adminhtml_Block_Permissions_Role'),
    array('Mage_Adminhtml_Block_Permissions_Buttons'),
    array('Mage_Adminhtml_Block_Permissions_Role_Grid_User'),
    array('Mage_Adminhtml_Block_Permissions_Grid_Role'),
    array('Mage_Adminhtml_Block_Permissions_Grid_User'),
    array('Mage_Adminhtml_Block_Permissions_Tab_Roleinfo'),
    array('Mage_Adminhtml_Block_Permissions_Tab_Rolesedit'),
    array('Mage_Adminhtml_Block_Permissions_Tab_Rolesusers'),
    array('Mage_Adminhtml_Block_Permissions_Tab_Useredit'),
    array('Mage_Adminhtml_Block_Permissions_Editroles'),
    array('Mage_Adminhtml_Block_Permissions_Roles'),
    array('Mage_Adminhtml_Block_Permissions_Users'),
    array('Mage_Adminhtml_Block_Permissions_Edituser'),
    array('Mage_Adminhtml_Block_Permissions_Tab_Userroles'),
    array('Mage_Adminhtml_Block_Permissions_Usernroles'),
    array('Mage_Adminhtml_Block_Rating_Grid'),
    array('Mage_Adminhtml_Block_System_Store_Grid'),
    array('Mage_Adminhtml_Permissions_UserController'),
    array('Mage_Adminhtml_Permissions_RoleController'),
    array('Mage_Adminhtml_Block_Report_Grid', 'Mage_Reports_Block_Adminhtml_Grid'),
    array('Mage_Adminhtml_Block_Report_Customer_Accounts',
        'Mage_Reports_Block_Adminhtml_Customer_Accounts'),
    array('Mage_Adminhtml_Block_Report_Customer_Accounts_Grid'),
    array('Mage_Adminhtml_Block_Report_Customer_Totals', 'Mage_Reports_Block_Adminhtml_Customer_Totals'),
    array('Mage_Adminhtml_Block_Report_Customer_Totals_Grid'),
    array('Mage_Adminhtml_Block_Report_Product_Sold', 'Mage_Reports_Block_Adminhtml_Product_Sold'),
    array('Mage_Adminhtml_Block_Report_Product_Sold_Grid'),
    array('Mage_Adminhtml_Block_Report_Review_Customer_Grid'),
    array('Mage_Adminhtml_Block_Report_Customer_Orders', 'Mage_Reports_Block_Adminhtml_Customer_Orders'),
    array('Mage_Adminhtml_Block_Report_Customer_Orders_Grid'),
    array('Mage_Adminhtml_Block_Report_Product_Ordered'),
    array('Mage_Adminhtml_Block_Report_Product_Ordered_Grid'),
    array('Mage_Adminhtml_Block_Report_Review_Product_Grid'),
    array('Mage_Adminhtml_Block_Report_Refresh_Statistics', 'Mage_Reports_Block_Adminhtml_Refresh_Statistics'),
    array('Mage_Adminhtml_Block_Report_Refresh_Statistics_Grid'),
    array('Mage_Adminhtml_Block_Report_Search_Grid'),
    array('Mage_Adminhtml_Block_Sales'),
    array('Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Giftmessage'),
    array('Mage_Adminhtml_Block_Sitemap_Grid'),
    array('Mage_Adminhtml_Block_System_Config_Edit', 'Mage_Backend_Block_System_Config_Edit'),
    array('Mage_Adminhtml_Block_System_Config_Form', 'Mage_Backend_Block_System_Config_Form'),
    array('Mage_Adminhtml_Block_System_Config_Tabs', 'Mage_Backend_Block_System_Config_Tabs'),
    array('Mage_Adminhtml_Block_System_Config_System_Storage_Media_Synchronize',
        'Mage_Backend_Block_System_Config_System_Storage_Media_Synchronize'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Fieldset_Modules_DisableOutput',
        'Mage_Backend_Block_System_Config_Form_Fieldset_Modules_DisableOutput'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field_Regexceptions',
        'Mage_Backend_Block_System_Config_Form_Field_Regexceptions'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field_Notification',
        'Mage_Backend_Block_System_Config_Form_Field_Notification'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field_Heading',
        'Mage_Backend_Block_System_Config_Form_Field_Heading'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field_Datetime',
        'Mage_Backend_Block_System_Config_Form_Field_Datetime'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract',
        'Mage_Backend_Block_System_Config_Form_Field_Array_Abstract'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Fieldset',
        'Mage_Backend_Block_System_Config_Form_Fieldset'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field',
        'Mage_Backend_Block_System_Config_Form_Field'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field_Import',
        'Mage_Backend_Block_System_Config_Form_Field_Import'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field_Image',
        'Mage_Backend_Block_System_Config_Form_Field_Image'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field_Export',
        'Mage_Backend_Block_System_Config_Form_Field_Export'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field_Select_Allowspecific',
        'Mage_Backend_Block_System_Config_Form_Field_Select_Allowspecific'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field_File',
        'Mage_Backend_Block_System_Config_Form_Field_File'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field_Select_Flatproduct',
        'Mage_Catalog_Block_Adminhtml_System_Config_Form_Field_Select_Flatproduct'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Field_Select_Flatcatalog',
        'Mage_Catalog_Block_Adminhtml_System_Config_Form_Field_Select_Flatcatalog'
    ),
    array('Mage_Adminhtml_Block_System_Config_Form_Fieldset_Order_Statuses',
        'Mage_Sales_Block_Adminhtml_System_Config_Form_Fieldset_Order_Statuses'
    ),
    array('Mage_Adminhtml_Block_System_Config_Dwstree', 'Mage_Backend_Block_System_Config_Dwstree'),
    array('Mage_Adminhtml_Block_System_Config_Switcher', 'Mage_Backend_Block_System_Config_Switcher'),
    array('Mage_Adminhtml_Block_System_Design_Grid'),
    array('Mage_Adminhtml_Block_System_Email_Template_Grid'),
    array('Mage_Adminhtml_Block_System_Variable_Grid'),
    array('Mage_Adminhtml_Block_Store_Switcher', 'Mage_Backend_Block_Store_Switcher'),
    array('Mage_Adminhtml_Block_Store_Switcher_Form_Renderer_Fieldset',
        'Mage_Backend_Block_Store_Switcher_Form_Renderer_Fieldset'
    ),
    array('Mage_Adminhtml_Block_Store_Switcher_Form_Renderer_Fieldset_Element',
        'Mage_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element'
    ),
    array('Mage_Adminhtml_Block_Tag_Tag_Edit'),
    array('Mage_Adminhtml_Block_Tag_Tag_Edit_Form'),
    array('Mage_Adminhtml_Block_Tax_Rate_Grid'),
    array('Mage_Adminhtml_Block_Tree'),
    array('Mage_Adminhtml_Block_Urlrewrite_Grid'),
    array('Mage_Adminhtml_Helper_Rss'),
    array('Mage_Adminhtml_Model_Config', 'Mage_Backend_Model_Config_Structure'),
    array('Mage_Adminhtml_Model_Config_Data', 'Mage_Backend_Model_Config'),
    array('Mage_Adminhtml_Model_Extension'),
    array('Mage_Adminhtml_Model_System_Config_Source_Shipping_Allowedmethods'),
    array('Mage_Adminhtml_Model_System_Config_Backend_Admin_Password_Link_Expirationperiod',
        'Mage_Backend_Model_Config_Backend_Admin_Password_Link_Expirationperiod'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom',
        'Mage_Backend_Model_Config_Backend_Admin_Custom'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Admin_Custompath',
        'Mage_Backend_Model_Config_Backend_Admin_Custompath'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Admin_Observer',
        'Mage_Backend_Model_Config_Backend_Admin_Observer'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Admin_Robots',
        'Mage_Backend_Model_Config_Backend_Admin_Robots'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Admin_Usecustom',
        'Mage_Backend_Model_Config_Backend_Admin_Usecustom'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Admin_Usecustompath',
        'Mage_Backend_Model_Config_Backend_Admin_Usecustompath'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Admin_Usesecretkey',
        'Mage_Backend_Model_Config_Backend_Admin_Usesecretkey'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Catalog_Inventory_Managestock',
        'Mage_CatalogInventory_Model_Config_Backend_Managestock'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Catalog_Search_Type',
        'Mage_CatalogSearch_Model_Config_Backend_Search_Type'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Currency_Abstract',
        'Mage_Backend_Model_Config_Backend_Currency_Abstract'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Currency_Allow',
        'Mage_Backend_Model_Config_Backend_Currency_Allow'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Currency_Base',
        'Mage_Backend_Model_Config_Backend_Currency_Base'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Currency_Cron',
        'Mage_Backend_Model_Config_Backend_Currency_Cron'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Currency_Default',
        'Mage_Backend_Model_Config_Backend_Currency_Default'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Customer_Address_Street',
        'Mage_Customer_Model_Config_Backend_Address_Street'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Customer_Password_Link_Expirationperiod',
        'Mage_Customer_Model_Config_Backend_Password_Link_Expirationperiod'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Customer_Show_Address',
        'Mage_Customer_Model_Config_Backend_Show_Address'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Customer_Show_Customer',
        'Mage_Customer_Model_Config_Backend_Show_Customer'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Design_Exception',
        'Mage_Backend_Model_Config_Backend_Design_Exception'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Email_Address',
        'Mage_Backend_Model_Config_Backend_Email_Address'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Email_Logo',
        'Mage_Backend_Model_Config_Backend_Email_Logo'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Email_Sender',
        'Mage_Backend_Model_Config_Backend_Email_Sender'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Image_Adapter',
        'Mage_Backend_Model_Config_Backend_Image_Adapter'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Image_Favicon',
        'Mage_Backend_Model_Config_Backend_Image_Favicon'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Image_Pdf',
        'Mage_Backend_Model_Config_Backend_Image_Pdf'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Locale_Timezone',
        'Mage_Backend_Model_Config_Backend_Locale_Timezone'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Log_Cron',
        'Mage_Backend_Model_Config_Backend_Log_Cron'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Price_Scope'),
    array('Mage_Adminhtml_Model_System_Config_Backend_Product_Alert_Cron',
        'Mage_Cron_Model_Config_Backend_Product_Alert'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Seo_Product',
        'Mage_Catalog_Model_Config_Backend_Seo_Product'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array',
        'Mage_Backend_Model_Config_Backend_Serialized_Array'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Shipping_Tablerate',
        'Mage_Shipping_Model_Config_Backend_Tablerate'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Sitemap_Cron',
        'Mage_Cron_Model_Config_Backend_Sitemap'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Storage_Media_Database',
        'Mage_Backend_Model_Config_Backend_Storage_Media_Database'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Baseurl',
        'Mage_Backend_Model_Config_Backend_Baseurl'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Cache',
        'Mage_Backend_Model_Config_Backend_Cache'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Category',
        'Mage_Catalog_Model_Config_Backend_Category'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Cookie',
        'Mage_Backend_Model_Config_Backend_Cookie'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Datashare',
        'Mage_Backend_Model_Config_Backend_Datashare'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Encrypted',
        'Mage_Backend_Model_Config_Backend_Encrypted'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_File',
        'Mage_Backend_Model_Config_Backend_File'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Filename',
        'Mage_Backend_Model_Config_Backend_Filename'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Image',
        'Mage_Backend_Model_Config_Backend_Image'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Locale',
        'Mage_Backend_Model_Config_Backend_Locale'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Secure',
        'Mage_Backend_Model_Config_Backend_Secure'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Serialized',
        'Mage_Backend_Model_Config_Backend_Serialized'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Sitemap',
        'Mage_Sitemap_Model_Config_Backend_Priority'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Store',
        'Mage_Backend_Model_Config_Backend_Store'
    ),
    array('Mage_Adminhtml_Model_System_Config_Backend_Translate',
        'Mage_Backend_Model_Config_Backend_Translate'
    ),
    array('Mage_Adminhtml_Model_System_Config_Clone_Media_Image',
        'Mage_Catalog_Model_Config_Clone_Media_Image'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Admin_Page',
        'Mage_Backend_Model_Config_Source_Admin_Page'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Catalog_Search_Type',
        'Mage_CatalogSearch_Model_Config_Source_Search_Type'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Catalog_GridPerPage',
        'Mage_Catalog_Model_Config_Source_GridPerPage'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Catalog_ListMode',
        'Mage_Catalog_Model_Config_Source_ListMode'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Catalog_ListPerPage',
        'Mage_Catalog_Model_Config_Source_ListPerPage'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Catalog_ListSort',
        'Mage_Catalog_Model_Config_Source_ListSort'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Catalog_TimeFormat',
        'Mage_Catalog_Model_Config_Source_TimeFormat'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Cms_Wysiwyg_Enabled',
        'Mage_Cms_Model_Config_Source_Wysiwyg_Enabled'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Cms_Page',
        'Mage_Cms_Model_Config_Source_Page'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Country_Full',
        'Mage_Directory_Model_Config_Source_Country_Full'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency',
        'Mage_Cron_Model_Config_Source_Frequency'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Currency_Service',
        'Mage_Backend_Model_Config_Source_Currency'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Customer_Address_Type',
        'Mage_Customer_Model_Config_Source_Address_Type'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Customer_Group_Multiselect',
        'Mage_Customer_Model_Config_Source_Group_Multiselect'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Customer_Group',
        'Mage_Customer_Model_Config_Source_Group'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Date_Short',
        'Mage_Backend_Model_Config_Source_Date_Short'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Design_Package'),
    array('Mage_Adminhtml_Model_System_Config_Source_Design_Robots',
        'Mage_Backend_Model_Config_Source_Design_Robots'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Dev_Dbautoup',
        'Mage_Backend_Model_Config_Source_Dev_Dbautoup'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Email_Identity',
        'Mage_Backend_Model_Config_Source_Email_Identity'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Email_Method',
        'Mage_Backend_Model_Config_Source_Email_Method'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Email_Smtpauth',
        'Mage_Backend_Model_Config_Source_Email_Smtpauth'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Email_Template',
        'Mage_Backend_Model_Config_Source_Email_Template'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Image_Adapter',
        'Mage_Backend_Model_Config_Source_Image_Adapter'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Locale_Country',
        'Mage_Backend_Model_Config_Source_Locale_Country'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Locale_Currency_All',
        'Mage_Backend_Model_Config_Source_Locale_Currency_All'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Locale_Currency',
        'Mage_Backend_Model_Config_Source_Locale_Currency'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Locale_Timezone',
        'Mage_Backend_Model_Config_Source_Locale_Timezone'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Locale_Weekdays',
        'Mage_Backend_Model_Config_Source_Locale_Weekdays'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Notification_Frequency',
        'Mage_AdminNotification_Model_Config_Source_Frequency'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Order_Status_New',
        'Mage_Sales_Model_Config_Source_Order_Status_New'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Order_Status_Newprocessing',
        'Mage_Sales_Model_Config_Source_Order_Status_Newprocessing'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Order_Status_Processing',
        'Mage_Sales_Model_Config_Source_Order_Status_Processing'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Order_Status',
        'Mage_Sales_Model_Config_Source_Order_Status'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Payment_Allmethods',
        'Mage_Payment_Model_Config_Source_Allmethods'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Payment_Allowedmethods',
        'Mage_Payment_Model_Config_Source_Allowedmethods'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Payment_Allspecificcountries',
        'Mage_Payment_Model_Config_Source_Allspecificcountries'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Payment_Cctype',
        'Mage_Payment_Model_Config_Source_Cctype'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Price_Scope',
        'Mage_Catalog_Model_Config_Source_Price_Scope'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Price_Step',
        'Mage_Catalog_Model_Config_Source_Price_Step'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Product_Options_Price',
        'Mage_Catalog_Model_Config_Source_Product_Options_Price'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Product_Options_Type',
        'Mage_Catalog_Model_Config_Source_Product_Options_Type'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Product_Thumbnail',
        'Mage_Catalog_Model_Config_Source_Product_Thumbnail'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Reports_Scope',
        'Mage_Backend_Model_Config_Source_Reports_Scope'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Shipping_Allmethods',
        'Mage_Shipping_Model_Config_Source_Allmethods'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Shipping_Allspecificcountries',
        'Mage_Shipping_Model_Config_Source_Allspecificcountries'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Shipping_Flatrate',
        'Mage_Shipping_Model_Config_Source_Flatrate'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Shipping_Tablerate',
        'Mage_Shipping_Model_Config_Source_Tablerate'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Shipping_Taxclass',
        'Mage_Tax_Model_Config_Source_Class_Product'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Storage_Media_Database',
        'Mage_Backend_Model_Config_Source_Storage_Media_Database'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Storage_Media_Storage',
        'Mage_Backend_Model_Config_Source_Storage_Media_Storage'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Tax_Apply_On',
        'Mage_Tax_Model_Config_Source_Apply_On'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Tax_Basedon',
        'Mage_Tax_Model_Config_Source_Basedon'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Tax_Catalog',
        'Mage_Tax_Model_Config_Source_Catalog'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Watermark_Position',
        'Mage_Catalog_Model_Config_Source_Watermark_Position'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Web_Protocol',
        'Mage_Backend_Model_Config_Source_Web_Protocol'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Web_Redirect',
        'Mage_Backend_Model_Config_Source_Web_Redirect'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Allregion',
        'Mage_Directory_Model_Config_Source_Allregion'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Category',
        'Mage_Catalog_Model_Config_Source_Category'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Checktype',
        'Mage_Backend_Model_Config_Source_Checktype'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Country',
        'Mage_Directory_Model_Config_Source_Country'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Currency',
        'Mage_Backend_Model_Config_Source_Currency'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Enabledisable',
        'Mage_Backend_Model_Config_Source_Enabledisable'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Frequency',
        'Mage_Sitemap_Model_Config_Source_Frequency'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Locale',
        'Mage_Backend_Model_Config_Source_Locale'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Nooptreq',
        'Mage_Backend_Model_Config_Source_Nooptreq'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Store',
        'Mage_Backend_Model_Config_Source_Store'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Website',
        'Mage_Backend_Model_Config_Source_Website'
    ),
    array('Mage_Adminhtml_Model_System_Config_Source_Yesno', 'Mage_Backend_Model_Config_Source_Yesno'),
    array('Mage_Adminhtml_Model_System_Config_Source_Yesnocustom',
        'Mage_Backend_Model_Config_Source_Yesnocustom'
    ),
    array('Mage_Adminhtml_Model_System_Store', 'Mage_Core_Model_System_Store'),
    array('Mage_Adminhtml_Model_Url', 'Mage_Backend_Model_Url'),
    array('Mage_Adminhtml_Rss_CatalogController'),
    array('Mage_Adminhtml_Rss_OrderController'),
    array('Mage_Adminhtml_SystemController', 'Mage_Backend_Adminhtml_SystemController'),
    array('Mage_Adminhtml_System_ConfigController', 'Mage_Backend_Adminhtml_System_ConfigController'),
    array('Mage_Bundle_Product_EditController', 'Mage_Bundle_Adminhtml_Bundle_SelectionController'),
    array('Mage_Bundle_SelectionController', 'Mage_Bundle_Adminhtml_Bundle_SelectionController'),
    array('Mage_Catalog_Model_Convert'),
    array('Mage_Catalog_Model_Convert_Adapter_Catalog'),
    array('Mage_Catalog_Model_Convert_Adapter_Product'),
    array('Mage_Catalog_Model_Convert_Parser_Product'),
    array('Mage_Catalog_Model_Entity_Product_Attribute_Frontend_Image'),
    array('Mage_Catalog_Model_Resource_Product_Attribute_Frontend_Image'),
    array('Mage_Catalog_Model_Resource_Product_Attribute_Frontend_Tierprice'),
    array('Mage_Core_Block_Flush'),
    array('Mage_Core_Block_Template_Facade'),
    array('Mage_Core_Block_Template_Smarty'),
    array('Mage_Core_Block_Template_Zend'),
    array('Mage_Core_Controller_Varien_Router_Admin', 'Mage_Backend_Controller_Router_Default'),
    array('Mage_Core_Model_Convert'),
    array('Mage_Core_Model_Config_Options', 'Mage_Core_Model_Dir'),
    array('Mage_Core_Model_Config_Module'),
    array('Mage_Core_Model_Config_System'),
    array('Mage_Core_Model_Design_Source_Apply'),
    array('Mage_Core_Model_Language'),
    array('Mage_Core_Model_Resource_Language'),
    array('Mage_Core_Model_Resource_Language_Collection'),
    array('Mage_Core_Model_Resource_Setup_Query_Modifier'),
    array('Mage_Core_Model_Session_Abstract_Varien'),
    array('Mage_Core_Model_Session_Abstract_Zend'),
    array('Mage_Core_Model_Layout_Data', 'Mage_Core_Model_Layout_Update'),
    array('Mage_Core_Model_Theme_Customization_Link'),
    array('Mage_Customer_Block_Account'),
    array('Mage_Customer_Model_Convert_Adapter_Customer'),
    array('Mage_Customer_Model_Convert_Parser_Customer'),
    array('Mage_DesignEditor_Block_Page_Html_Head_Vde'),
    array('Mage_DesignEditor_Block_Page_Html_Head'),
    array('Mage_Directory_Model_Resource_Currency_Collection'),
    array('Mage_Downloadable_FileController', 'Mage_Downloadable_Adminhtml_Downloadable_FileController'),
    array('Mage_Downloadable_Product_EditController', 'Mage_Adminhtml_Catalog_ProductController'),
    array('Mage_Eav_Model_Convert_Adapter_Entity'),
    array('Mage_Eav_Model_Convert_Adapter_Grid'),
    array('Mage_Eav_Model_Convert_Parser_Abstract'),
    array('Mage_GiftMessage_Block_Message_Form'),
    array('Mage_GiftMessage_Block_Message_Helper'),
    array('Mage_GiftMessage_IndexController'),
    array('Mage_GiftMessage_Model_Entity_Attribute_Backend_Boolean_Config'),
    array('Mage_GiftMessage_Model_Entity_Attribute_Source_Boolean_Config'),
    array('Mage_GoogleOptimizer_IndexController', 'Mage_GoogleOptimizer_Adminhtml_Googleoptimizer_IndexController'),
    array('Mage_GoogleShopping_Block_Adminhtml_Types_Grid'),
    array('Mage_ImportExport_Model_Import_Adapter_Abstract', 'Mage_ImportExport_Model_Import_SourceAbstract'),
    array('Mage_ImportExport_Model_Import_Adapter_Csv', 'Mage_ImportExport_Model_Import_Source_Csv'),
    array('Mage_Install_Model_Installer_Env'),
    array('Mage_Ogone_Model_Api_Debug'),
    array('Mage_Ogone_Model_Resource_Api_Debug'),
    array('Mage_Page_Block_Html_Toplinks'),
    array('Mage_Page_Block_Html_Wrapper'),
    array('Mage_Poll_Block_Poll'),
    array('Mage_ProductAlert_Block_Price'),
    array('Mage_ProductAlert_Block_Stock'),
    array('Mage_Reports_Model_Resource_Coupons_Collection'),
    array('Mage_Reports_Model_Resource_Invoiced_Collection'),
    array('Mage_Reports_Model_Resource_Product_Ordered_Collection'),
    array('Mage_Reports_Model_Resource_Product_Viewed_Collection',
        'Mage_Reports_Model_Resource_Report_Product_Viewed_Collection'),
    array('Mage_Reports_Model_Resource_Refunded_Collection'),
    array('Mage_Reports_Model_Resource_Shipping_Collection'),
    array('Mage_Rss_Model_Observer'),
    array('Mage_Rss_Model_Session', 'Mage_Backend_Model_Auth and Mage_Backend_Model_Auth_Session'),
    array('Mage_Sales_Block_Order_Details'),
    array('Mage_Sales_Block_Order_Tax'),
    array('Mage_Sales_Model_Entity_Order'),
    array('Mage_Sales_Model_Entity_Order_Address'),
    array('Mage_Sales_Model_Entity_Order_Address_Collection'),
    array('Mage_Sales_Model_Entity_Order_Attribute_Backend_Billing'),
    array('Mage_Sales_Model_Entity_Order_Attribute_Backend_Child'),
    array('Mage_Sales_Model_Entity_Order_Attribute_Backend_Parent'),
    array('Mage_Sales_Model_Entity_Order_Attribute_Backend_Shipping'),
    array('Mage_Sales_Model_Entity_Order_Collection'),
    array('Mage_Sales_Model_Entity_Order_Creditmemo'),
    array('Mage_Sales_Model_Entity_Order_Creditmemo_Attribute_Backend_Child'),
    array('Mage_Sales_Model_Entity_Order_Creditmemo_Attribute_Backend_Parent'),
    array('Mage_Sales_Model_Entity_Order_Creditmemo_Collection'),
    array('Mage_Sales_Model_Entity_Order_Creditmemo_Comment'),
    array('Mage_Sales_Model_Entity_Order_Creditmemo_Comment_Collection'),
    array('Mage_Sales_Model_Entity_Order_Creditmemo_Item'),
    array('Mage_Sales_Model_Entity_Order_Creditmemo_Item_Collection'),
    array('Mage_Sales_Model_Entity_Order_Invoice'),
    array('Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Child'),
    array('Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Item'),
    array('Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Order'),
    array('Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Parent'),
    array('Mage_Sales_Model_Entity_Order_Invoice_Collection'),
    array('Mage_Sales_Model_Entity_Order_Invoice_Comment'),
    array('Mage_Sales_Model_Entity_Order_Invoice_Comment_Collection'),
    array('Mage_Sales_Model_Entity_Order_Invoice_Item'),
    array('Mage_Sales_Model_Entity_Order_Invoice_Item_Collection'),
    array('Mage_Sales_Model_Entity_Order_Item'),
    array('Mage_Sales_Model_Entity_Order_Item_Collection'),
    array('Mage_Sales_Model_Entity_Order_Payment'),
    array('Mage_Sales_Model_Entity_Order_Payment_Collection'),
    array('Mage_Sales_Model_Entity_Order_Shipment'),
    array('Mage_Sales_Model_Entity_Order_Shipment_Attribute_Backend_Child'),
    array('Mage_Sales_Model_Entity_Order_Shipment_Attribute_Backend_Parent'),
    array('Mage_Sales_Model_Entity_Order_Shipment_Collection'),
    array('Mage_Sales_Model_Entity_Order_Shipment_Comment'),
    array('Mage_Sales_Model_Entity_Order_Shipment_Comment_Collection'),
    array('Mage_Sales_Model_Entity_Order_Shipment_Item'),
    array('Mage_Sales_Model_Entity_Order_Shipment_Item_Collection'),
    array('Mage_Sales_Model_Entity_Order_Shipment_Track'),
    array('Mage_Sales_Model_Entity_Order_Shipment_Track_Collection'),
    array('Mage_Sales_Model_Entity_Order_Status_History'),
    array('Mage_Sales_Model_Entity_Order_Status_History_Collection'),
    array('Mage_Sales_Model_Entity_Quote'),
    array('Mage_Sales_Model_Entity_Quote_Address'),
    array('Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend'),
    array('Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Child'),
    array('Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Parent'),
    array('Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Region'),
    array('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend'),
    array('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Custbalance'),
    array('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Discount'),
    array('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Grand'),
    array('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Shipping'),
    array('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Subtotal'),
    array('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Tax'),
    array('Mage_Sales_Model_Entity_Quote_Address_Collection'),
    array('Mage_Sales_Model_Entity_Quote_Address_Item'),
    array('Mage_Sales_Model_Entity_Quote_Address_Item_Collection'),
    array('Mage_Sales_Model_Entity_Quote_Address_Rate'),
    array('Mage_Sales_Model_Entity_Quote_Address_Rate_Collection'),
    array('Mage_Sales_Model_Entity_Quote_Collection'),
    array('Mage_Sales_Model_Entity_Quote_Item'),
    array('Mage_Sales_Model_Entity_Quote_Item_Collection'),
    array('Mage_Sales_Model_Entity_Quote_Payment'),
    array('Mage_Sales_Model_Entity_Quote_Payment_Collection'),
    array('Mage_Sales_Model_Entity_Sale_Collection'),
    array('Mage_Sales_Model_Entity_Setup'),
    array('Mage_Shipping_ShippingController'),
    array('Mage_Tag_Block_Adminhtml_Report_Customer_Detail_Grid'),
    array('Mage_Tag_Block_Adminhtml_Report_Customer_Grid'),
    array('Mage_Tag_Block_Adminhtml_Report_Popular_Detail_Grid'),
    array('Mage_Tag_Block_Adminhtml_Report_Product_Detail_Grid'),
    array('Mage_Tag_Block_Adminhtml_Report_Product_Grid'),
    array('Mage_Tag_Block_Customer_Edit'),
    array('Mage_Theme_Block_Adminhtml_System_Design_Theme_Grid'),
    array('Mage_User_Block_Role_Grid'),
    array('Mage_User_Block_User_Grid'),
    array('Mage_User_Model_Roles'),
    array('Mage_User_Model_Resource_Roles'),
    array('Mage_User_Model_Resource_Roles_Collection'),
    array('Mage_User_Model_Resource_Roles_User_Collection'),
    array('Mage_Wishlist_Model_Resource_Product_Collection'),
    array('Varien_Convert_Action'),
    array('Varien_Convert_Action_Abstract'),
    array('Varien_Convert_Action_Interface'),
    array('Varien_Convert_Adapter_Abstract'),
    array('Varien_Convert_Adapter_Db_Table'),
    array('Varien_Convert_Adapter_Http'),
    array('Varien_Convert_Adapter_Http_Curl'),
    array('Varien_Convert_Adapter_Interface'),
    array('Varien_Convert_Adapter_Io'),
    array('Varien_Convert_Adapter_Soap'),
    array('Varien_Convert_Adapter_Std'),
    array('Varien_Convert_Adapter_Zend_Cache'),
    array('Varien_Convert_Adapter_Zend_Db'),
    array('Varien_Convert_Container_Collection'),
    array('Varien_Convert_Container_Generic'),
    array('Varien_Convert_Container_Interface'),
    array('Varien_Convert_Mapper_Abstract'),
    array('Varien_Convert_Parser_Abstract'),
    array('Varien_Convert_Parser_Csv'),
    array('Varien_Convert_Parser_Interface'),
    array('Varien_Convert_Parser_Serialize'),
    array('Varien_Convert_Parser_Xml_Excel'),
    array('Varien_Convert_Profile'),
    array('Varien_Convert_Profile_Abstract'),
    array('Varien_Convert_Profile_Collection'),
    array('Varien_Convert_Validator_Abstract'),
    array('Varien_Convert_Validator_Column'),
    array('Varien_Convert_Validator_Dryrun'),
    array('Varien_Convert_Validator_Interface'),
    array('Varien_File_Uploader_Image'),
    array('Varien_Profiler', 'Magento_Profiler'),
    array('Mage_Adminhtml_Block_Notification_Window', 'Mage_AdminNotification_Block_Window'),
    array('Mage_Adminhtml_Block_Notification_Toolbar'),
    array('Mage_Adminhtml_Block_Notification_Survey'),
    array('Mage_Adminhtml_Block_Notification_Security'),
    array('Mage_Adminhtml_Block_Notification_Inbox'),
    array('Mage_Adminhtml_Block_Notification_Grid', 'Mage_AdminNotification_Block_Notification_Grid'),
    array('Mage_Adminhtml_Block_Notification_Baseurl'),
    array('Mage_Adminhtml_Block_Notification_Grid_Renderer_Severity',
        'Mage_AdminNotification_Block_Grid_Renderer_Severity'),
    array('Mage_Adminhtml_Block_Notification_Grid_Renderer_Notice',
        'Mage_AdminNotification_Block_Grid_Renderer_Notice'),
    array('Mage_Adminhtml_Block_Notification_Grid_Renderer_Actions',
        'Mage_AdminNotification_Block_Grid_Renderer_Actions'),
    array('Mage_Adminhtml_Block_Cache_Notifications'),
);
