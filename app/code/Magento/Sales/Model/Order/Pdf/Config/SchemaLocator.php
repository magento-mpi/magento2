<?php
/**
 * Attributes config schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Model_Order_Pdf_Config_SchemaLocator implements Magento_Config_SchemaLocatorInterface
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
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     */
    public function __construct(Magento_Core_Model_Config_Modules_Reader $moduleReader)
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
