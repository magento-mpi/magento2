<?php
/**
 * Menu configuration files handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Menu_Config_Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param Magento_Backend_Model_Menu_Config_Converter $converter
     * @param Magento_Backend_Model_Menu_Config_SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        Magento_Backend_Model_Menu_Config_Converter $converter,
        Magento_Backend_Model_Menu_Config_SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'menu.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Backend_Model_Menu_Config_Menu_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
