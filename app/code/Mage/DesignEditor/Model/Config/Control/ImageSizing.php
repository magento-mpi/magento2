<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Image Sizing configuration
 */
class Mage_DesignEditor_Model_Config_Control_ImageSizing extends Mage_DesignEditor_Model_Config_Control_Abstract
{
    /**
     * Keys of layout params attributes
     *
     * @var array
     */
    protected $_controlAttributes = array('title');

    /**
     * Path to quick_styles.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/../../../etc/image_sizing.xsd';
    }
}
