<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * An abstraction for static view file (or resource) that may be embedded to a web page
 */
interface AssetInterface
{
    /**#@+
     * Common content types
     */
    const CONTENT_TYPE_CSS = 'css';
    const CONTENT_TYPE_JS  = 'js';
    /**#@-*/

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
