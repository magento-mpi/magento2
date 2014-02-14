<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview\Config;

class SchemaLocator implements \Magento\Config\SchemaLocatorInterface
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
     * @param \Magento\Filesystem\DirectoryList $directoryList
     */
    public function __construct(\Magento\Filesystem\DirectoryList $directoryList)
    {
        $etcDir = $directoryList->getDir(\Magento\App\Filesystem::LIB_DIR) . '/Magento/Mview/etc';
        $this->_schema =  $etcDir . '/mview.xsd';
        $this->_perFileSchema = $etcDir . '/mview.xsd';
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
