<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Image Sizing configuration
 */
class Magento_DesignEditor_Model_Config_Control_ImageSizing extends Magento_DesignEditor_Model_Config_Control_Abstract
{
    /**
     * Keys of layout params attributes
     *
     * @var array
     */
    protected $_controlAttributes = array('title');

    /**
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_moduleReader;

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     * @param array $configFiles
     */
    public function __construct(Magento_Core_Model_Config_Modules_Reader $moduleReader, array $configFiles)
    {
        $this->_moduleReader = $moduleReader;
        parent::__construct($configFiles);
    }

    /**
     * Path to quick_styles.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return $this->_moduleReader->getModuleDir('etc', 'Magento_DesignEditor') . DIRECTORY_SEPARATOR
            . 'image_sizing.xsd';
    }
}
