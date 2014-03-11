<?php
/**
 * Locator for page_types XSD schemas.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Layout\PageType\Config;

class SchemaLocator implements \Magento\Config\SchemaLocatorInterface
{
    /**
     * Path to corresponding XSD file with validation rules for config
     *
     * @var string
     */
    protected $_schema = null;

    /**
     * Constructor
     *
     * @param \Magento\Module\Dir\Reader $moduleReader
     */
    public function __construct(\Magento\Module\Dir\Reader $moduleReader)
    {
        $this->_schema =  $moduleReader->getModuleDir('etc', 'Magento_Core') . '/page_types.xsd';
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
        return $this->_schema;
    }
}
