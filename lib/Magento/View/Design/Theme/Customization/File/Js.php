<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme js file service class
 */
namespace Magento\View\Design\Theme\Customization\File;

class Js extends \Magento\View\Design\Theme\Customization\AbstractFile
{
    /**#@+
     * File type customization
     */
    const TYPE = 'js';
    const CONTENT_TYPE = 'js';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return self::CONTENT_TYPE;
    }
}
