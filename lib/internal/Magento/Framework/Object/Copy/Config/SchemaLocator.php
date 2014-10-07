<?php
/**
 * Locator for fieldset XSD schemas.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Object\Copy\Config;

use Magento\Framework\App\Filesystem\DirectoryList;

class SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
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
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param string $schema
     * @param string $perFileSchema
     */
    public function __construct(\Magento\Framework\App\Filesystem $filesystem, $schema, $perFileSchema)
    {
        $this->_schema = $filesystem->getPath(DirectoryList::ROOT_DIR) . '/' . $schema;
        $this->_perFileSchema = $filesystem->getPath(DirectoryList::ROOT_DIR) . '/' . $perFileSchema;
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
