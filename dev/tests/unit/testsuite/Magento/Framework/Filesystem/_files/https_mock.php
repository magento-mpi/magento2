<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\Driver;

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
    return HttpsTest::$fSockOpen;
}

/**
 * Override standard function (make a placeholder - we don't need it in our tests)
 */
function fwrite()
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
