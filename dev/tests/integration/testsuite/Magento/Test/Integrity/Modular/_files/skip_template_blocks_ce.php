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
    'Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\Customers',
);
