<?php
/**
 * {license_notice}
 *
 * @category Magento
 * @package Magento/Filesystem
 * @subpackage unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\Driver;

/**
 * Override standard function
 *
 * @return string
 */
function file_get_contents()
{
    return HttpTest::$fileGetContents;
}


/**
 * Override standard function
 *
 * @return bool
 */
function file_put_contents()
{
    return HttpTest::$filePutContents;
}

/**
 * Override standard function
 *
 * @param int    $errorNumber
 * @param string $errorMessage
 * @return bool
 */
function fsockopen(&$errorNumber, &$errorMessage)
{
    $errorNumber = 0;
    $errorMessage = '';
    return HttpTest::$fsockopen;
}

/**
 * Override standard function (make a placeholder - we don't need it in our tests)
 *
 * @param resource $handle
 * @param string   $content
 */
function fwrite($handle, $content)
{
}

/**
 * Override standard function (make a placeholder - we don't need it in our tests)
 *
 * @return bool
 */
function feof()
{
    return true;
}
