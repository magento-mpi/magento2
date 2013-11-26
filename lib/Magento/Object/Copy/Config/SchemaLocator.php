<?php
/**
 * Locator for fieldset XSD schemas.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Object\Copy\Config;

class SchemaLocator implements \Magento\Config\SchemaLocatorInterface
{
    /**
     * Path to corresponding XSD file with validation rules for merged config
     *
     * @var string
     */
    protected $_schema;

    /**
     * Path to corresponding XSD file with validation rules for separate config files
     *
     * @var string
     */
    protected $_perFileSchema;

    /**
     * @param \Magento\App\Dir $dirs
     * @param string $schema
     * @param string $perFileSchema
     */
    public function __construct(\Magento\App\Dir $dirs, $schema, $perFileSchema)
    {
        $this->_schema = $dirs->getDir() . '/' . $schema;
        $this->_perFileSchemaschema = $dirs->getDir() . '/' . $perFileSchema;
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
