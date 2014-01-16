<?php
/**
 * Logging schema locator
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\App\Config\Initial;

class SchemaLocator implements \Magento\Config\SchemaLocatorInterface
{
    /**
     * Path to corresponding XSD file with validation rules for config
     *
     * @var string
     */
    protected $_schema = null;

    /**
     * @param \Magento\Module\Dir\Reader $moduleReader
     * @param strng $moduleName
     */
    public function __construct(\Magento\Module\Dir\Reader $moduleReader, $moduleName)
    {
        $this->_schema = $moduleReader->getModuleDir('etc', $moduleName) . '/config.xsd';
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
        return $this->_schema;
    }
}
