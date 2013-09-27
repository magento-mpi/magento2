<?php
/**
 * Locator for page layouts XSD schemas.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Page_Model_Config_SchemaLocator implements Magento_Config_SchemaLocatorInterface
{
    /**
     * Path to corresponding XSD file with validation rules for merged config
     *
     * @var string
     */
    protected $_schema = null;

    /**
     * Path to corresponding XSD file with validation rules for separate config files
     *
     * @var string
     */
    protected $_perFileSchema = null;

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     */
    public function __construct(Magento_Core_Model_Config_Modules_Reader $moduleReader)
    {
        $this->_schema =  $moduleReader->getModuleDir('etc', 'Magento_Page') . '/page_layouts.xsd';
        $this->_perFileSchema =  $moduleReader->getModuleDir('etc', 'Magento_Page') . '/page_layouts_file.xsd';
    }

    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema()
    {
        return $this->_schema;
    }

    /**
     * Get path to per file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema()
    {
        return $this->_perFileSchema;
    }
}
