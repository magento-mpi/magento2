<?php
/**
 * Cms menu hierarchy configuration schema locator
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Model\Hierarchy\Config;

class SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
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
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     */
    public function __construct(\Magento\Framework\Module\Dir\Reader $moduleReader)
    {
        $etcDir = $moduleReader->getModuleDir('etc', 'Magento_VersionsCms');
        $this->_schema = $etcDir . '/menu_hierarchy_merged.xsd';
        $this->_perFileSchema = $etcDir . '/menu_hierarchy.xsd';
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
