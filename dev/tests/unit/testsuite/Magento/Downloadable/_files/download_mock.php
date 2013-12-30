<?php
/**
 * {license_notice}
 *
 * @category Magento
 * @package Magento/Downloadable
 * @subpackage unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Helper;

function function_exists()
{
    return DownloadTest::$functionExists;
}

function mime_content_type()
{
    return DownloadTest::$mimeContentType;
}