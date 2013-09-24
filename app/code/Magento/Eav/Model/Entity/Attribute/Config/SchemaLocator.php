<?php
/**
 * Entity attribute configuration schema locator
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Eav_Model_Entity_Attribute_Config_SchemaLocator implements Magento_Config_SchemaLocatorInterface
{
    /**
     * Schema file
     *
     * @var string
     */
    protected $_schema;

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     */
    public function __construct(Magento_Core_Model_Config_Modules_Reader $moduleReader)
    {
        $this->_schema = $moduleReader->getModuleDir('etc', 'Magento_Eav') . DIRECTORY_SEPARATOR . 'eav_attributes.xsd';
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
        return null;
    }
}
