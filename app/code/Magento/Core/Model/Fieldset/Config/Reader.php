<?php
/**
 * Fieldset configuration reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Fieldset_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/scope' => 'id',
        '/config/scope/fieldset' => 'id',
        '/config/scope/fieldset/field' => 'name',
        '/config/scope/fieldset/field/aspect' => 'name',
    );

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Core_Model_Fieldset_Config_Converter $converter
     * @param Magento_Config_SchemaLocatorInterface $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Core_Model_Fieldset_Config_Converter $converter,
        Magento_Config_SchemaLocatorInterface $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'fieldset.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass
        );
    }
}
