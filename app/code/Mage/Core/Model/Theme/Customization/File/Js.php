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
 * Theme js file service class
 */
class Mage_Core_Model_Theme_Customization_File_Js extends Mage_Core_Model_Theme_Customization_FileAbstract
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
