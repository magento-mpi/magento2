<?php
/**
 * List of blocks to be skipped from template files test
 *
 * Format: array('Block_Class_Name', ...)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    // Fails because of dependence on registry
    'Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Customers',
);
