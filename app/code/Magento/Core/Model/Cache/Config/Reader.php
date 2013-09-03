<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Cache_Config_Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/type' => 'name',
    );

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param Magento_Core_Model_Cache_Config_Converter $converter
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param null $schema
     * @param null $perFileSchema
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Core_Model_Config_Modules_Reader $moduleReader,
        \Magento\Config\FileResolverInterface $fileResolver,
        Magento_Core_Model_Cache_Config_Converter $converter,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'cache.xml',
        $idAttributes = array(),
        $schema = null,
        $perFileSchema = null,
        $domDocumentClass = '\Magento\Config\Dom'
    ) {
        $schema = $schema ?: $moduleReader->getModuleDir('etc', 'Magento_Core') . DIRECTORY_SEPARATOR . 'cache.xsd';
        parent::__construct(
            $fileResolver, $converter, $fileName, $idAttributes, $schema,
            $perFileSchema, $validationState->isValidated(), $domDocumentClass
        );
    }
}
