<?php
/**
 * {license_notice}
 *
 * @package Magento/Downloadable
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
