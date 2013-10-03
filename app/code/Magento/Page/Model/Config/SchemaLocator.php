<?php
/**
 * Locator for page layouts XSD schemas.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Page\Model\Config;

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
     * @param \Magento\Core\Model\Config\Modules\Reader $moduleReader
     */
    public function __construct(\Magento\Core\Model\Config\Modules\Reader $moduleReader)
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
