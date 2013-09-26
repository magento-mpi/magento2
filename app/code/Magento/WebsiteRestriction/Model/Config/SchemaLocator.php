<?php
/**
 * Website restrictions configuration schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Model\Config;

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
        $this->_schema = $moduleReader->getModuleDir('etc', 'Magento_WebsiteRestriction')
            . DIRECTORY_SEPARATOR . 'webrestrictions.xsd';
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
