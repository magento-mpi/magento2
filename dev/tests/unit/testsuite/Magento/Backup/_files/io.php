<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backup
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backup;

/**
 * Mock is_dir function
 *
 * @see \Magento\Backup\Filesystem
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function is_dir($path)
{
    return true;
}

/**
 * Mock is_dir function
 *
 * @see \Magento\Backup\Filesystem
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function is_writable($path)
{
    return true;
}

/**
 * Mock disk_free_space function
 *
 * @see \Magento\Backup\Filesystem
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function disk_free_space($path)
{
    return 2;
}

/**
 * Mock is_file function
 *
 * @see \Magento\Backup\Filesystem
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function is_file($path)
{
    return 2;
}

/**
 * Mock filesize function
 *
 * @see \Magento\Backup\Filesystem
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function filesize($path)
{
    return 1;
}

/**
 * Mock unlink function
 *
 * @see \Magento\Backup\Filesystem
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function unlink($path)
{
    return true;
}

/**
 * Mock rmdir function
 *
 * @see \Magento\Backup\Filesystem
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function rmdir($path)
{
    return true;
}