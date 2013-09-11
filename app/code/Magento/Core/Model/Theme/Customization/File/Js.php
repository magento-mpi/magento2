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
namespace Magento\Core\Model\Theme\Customization\File;

class Js extends \Magento\Core\Model\Theme\Customization\FileAbstract
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
