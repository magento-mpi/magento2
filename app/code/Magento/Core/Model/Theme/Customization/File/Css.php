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
 * Theme css file service class
 */
namespace Magento\Core\Model\Theme\Customization\File;

class Css extends \Magento\Core\Model\Theme\Customization\FileAbstract
{
    /**#@+
     * CSS file customization types
     */
    const TYPE = 'css';
    const CONTENT_TYPE = 'css';
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
