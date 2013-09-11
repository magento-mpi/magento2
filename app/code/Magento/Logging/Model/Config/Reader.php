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
        '/logging/actions/action' => 'id',
        '/logging/groups/group' => 'name',
        '/logging/groups/group/events/event' => 'controller_action',
        '/logging/groups/group/events/event/expected_models/expected_model' => 'class',
        '/logging/groups/group/events/event/expected_models/expected_model/additional_fields/field' => 'name',
        '/logging/groups/group/events/event/expected_models/expected_model/skip_fields/field' => 'name',
        '/logging/groups/group/events/event/skip_on_back/controller_action' => 'name',
        '/logging/groups/group/expected_models/expected_model' => 'class',
        '/logging/groups/group/expected_models/expected_model/additional_fields/field' => 'name',
        '/logging/groups/group/expected_models/expected_model/skip_fields/field' => 'name',
    );

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Logging_Model_Config_Converter $converter
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param null $schema
     * @param null $perFileSchema
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Logging_Model_Config_Converter $converter,
        Magento_Logging_Model_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'logging.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState,
            $fileName, $idAttributes, $domDocumentClass
        );
    }
}