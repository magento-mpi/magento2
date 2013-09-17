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
class Magento_Core_Model_Theme_Customization_File_Css extends Magento_Core_Model_Theme_Customization_FileAbstract
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
