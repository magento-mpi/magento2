<?php
/**
 * Attribute configuration reader
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Eav_Model_Entity_Attribute_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * Xml merging attributes
     *
     * @var array
     */
    protected $_idAttributes = array(
        'config/entity' => 'type',
        'config/entity/attribute' => 'code',
        'config/entity/attribute/field' => 'code'
    );

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Eav_Model_Entity_Attribute_Config_Converter $converter
     * @param Magento_Eav_Model_Entity_Attribute_Config_SchemaLocator $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Eav_Model_Entity_Attribute_Config_Converter $converter,
        Magento_Eav_Model_Entity_Attribute_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState
    ) {
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, 'eav_attributes.xml', array());
    }
}
