<?php
/**
 * Event observers configuration filesystem loader. Loads event observers configuration from XML files, split by scopes
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Event_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/event' => 'name',
        '/config/event/observer' => 'name',
    );

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Mage_Core_Model_Event_Config_Converter $converter
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param Mage_Core_Model_Event_Config_SchemaLocator $schemaLocator
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Mage_Core_Model_Event_Config_Converter $converter,
        Mage_Core_Model_Event_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'events.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }

}
