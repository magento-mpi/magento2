<?php
/**
 * Attributes config schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Pdf\Config;

class SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
{
    /**
     * Path to corresponding XSD file with validation rules for merged configs
     *
     * @var string
     */
    private $_schema;

    /**
     * Path to corresponding XSD file with validation rules for individual configs
     *
     * @var string
     */
    private $_schemaFile;

    /**
     * @param \Magento\Module\Dir\Reader $moduleReader
     */
    public function __construct(\Magento\Module\Dir\Reader $moduleReader)
    {
        $dir = $moduleReader->getModuleDir('etc', 'Magento_Sales');
        $this->_schema = $dir . '/pdf.xsd';
        $this->_schemaFile = $dir . '/pdf_file.xsd';
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return $this->_schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getPerFileSchema()
    {
        return $this->_schemaFile;
    }
}
