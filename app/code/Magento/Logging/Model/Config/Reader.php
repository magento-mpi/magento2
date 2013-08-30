<?php
/**
 * Reader class for logging.xml
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Logging_Model_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = array(

    );

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Core_Model_Fieldset_Config_Converter $converter
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param null $schema
     * @param null $perFileSchema
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Core_Model_Config_Modules_Reader $moduleReader,
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Logging_Model_Config_Converter $converter,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'logging.xml',
        $idAttributes = array(),
        $schema = null,
        $perFileSchema = null,
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        $schema = $schema ?: $moduleReader->getModuleDir('etc', 'Enterprise_Logging') . '/logging.xsd';
        parent::__construct(
            $fileResolver, $converter, $fileName, $idAttributes, $schema,
            $perFileSchema, $validationState->isValidated(), $domDocumentClass
        );
    }
}