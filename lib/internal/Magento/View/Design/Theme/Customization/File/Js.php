<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme\Customization\File;

/**
 * Theme JS file service class
 */
class Js extends \Magento\View\Design\Theme\Customization\AbstractFile
{
    /**#@+
     * File type customization
     */
    const TYPE = 'js';
    const CONTENT_TYPE = 'js';
    /**#@-*/

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return self::CONTENT_TYPE;
    }
}
