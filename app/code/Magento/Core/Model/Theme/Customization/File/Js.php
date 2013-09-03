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
class Magento_Core_Model_Theme_Customization_File_Js extends Magento_Core_Model_Theme_Customization_FileAbstract
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
