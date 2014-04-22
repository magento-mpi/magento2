<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset;

/**
 * An abstraction for static view file (or resource) that may be embedded to a web page
 */
interface AssetInterface
{
    /**
     * Retrieve URL pointing to a resource
     *
     * @return string
     */
    public function getUrl();

    /**
     * Retrieve type of contents
     *
     * @return string
     */
    public function getContentType();
}
