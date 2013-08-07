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
    // Fails only in SAAS, could be something wrong list of classes being deleted
    'Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit',
    'Mage_Adminhtml_Block_Sales_Order_Invoice_View',
    'Mage_AdminNotification_Block_Window',
    'Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer',
    'Saas_Launcher_Block_Adminhtml_Storelauncher_WelcomeScreen',
);
