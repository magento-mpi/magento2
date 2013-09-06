<?php
/**
 * Routes configuration schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Route_Config_SchemaLocator implements \Magento\Config\SchemaLocatorInterface
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
        $this->_perFileSchema = $moduleReader->getModuleDir('etc', 'Magento_Core') . DIRECTORY_SEPARATOR . 'routes.xsd';
        $this->_schema = $moduleReader->getModuleDir('etc', 'Magento_Core') . DIRECTORY_SEPARATOR . 'routes_merged.xsd';
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
     * Get path to pre file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema()
    {
        return $this->_perFileSchema;
    }
}
