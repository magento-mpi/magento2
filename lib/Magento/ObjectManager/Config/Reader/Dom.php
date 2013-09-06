<?php
/**
 * ObjectManager DOM configuration reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ObjectManager_Config_Reader_Dom extends Magento_Config_Reader_Filesystem
{
    /**
     * List of paths to identifiable nodes
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/preference'         => 'for',
        '/config/type'               => 'name',
        '/config/type/param'         => 'name',
        '/config/type/plugin'        => 'name',
        '/config/virtualType'        => 'name',
        '/config/virtualType/param'  => 'name',
        '/config/virtualType/plugin' => 'name',
    );

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_ObjectManager_Config_Mapper_Dom $converter
     * @param Magento_ObjectManager_Config_SchemaLocator $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param array $idAttributes
     * @param string $filename
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_ObjectManager_Config_Mapper_Dom $converter,
        Magento_ObjectManager_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $idAttributes = array(),
        $filename = 'di.xml',
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $filename, $idAttributes, $domDocumentClass
        );
    }
}
