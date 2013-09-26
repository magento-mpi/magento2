<?php
/**
 * Entity attribute configuration schema locator
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute\Config;

class SchemaLocator implements \Magento\Config\SchemaLocatorInterface
{
    /**
     * Schema file
     *
     * @var string
     */
    protected $_schema;

    /**
     * @param \Magento\Core\Model\Config\Modules\Reader $moduleReader
     */
    public function __construct(\Magento\Core\Model\Config\Modules\Reader $moduleReader)
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
