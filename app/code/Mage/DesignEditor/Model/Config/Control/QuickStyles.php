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
 * Quick styles configuration
 */
class Mage_DesignEditor_Model_Config_Control_QuickStyles extends Mage_DesignEditor_Model_Config_Control_Abstract
{
    /**
     * Keys of layout params attributes
     *
     * @var array
     */
    protected $_controlAttributes = array('title', 'tab', 'column');

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
        return $this->_moduleReader->getModuleDir('etc', 'Mage_DesignEditor') . Magento_Filesystem::DIRECTORY_SEPARATOR
            . 'quick_styles.xsd';
    }
}
