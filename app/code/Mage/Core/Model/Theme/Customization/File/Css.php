<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme css file service class
 */
class Mage_Core_Model_Theme_Customization_File_Css extends Mage_Core_Model_Theme_Customization_FileAbstract
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
