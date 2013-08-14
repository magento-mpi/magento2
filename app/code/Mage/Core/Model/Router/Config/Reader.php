<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Router_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of paths to identifiable nodes
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/routers'               => 'id',
        '/config/routers/route'         => 'id',
        '/config/routers/route/module'  => 'name'
    );

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Mage_Core_Model_Router_Config_Converter $converter
     * @param Mage_Core_Model_Router_Config_SchemaLocator $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param string $fileName
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Mage_Core_Model_Router_Config_Converter $converter,
        Mage_Core_Model_Router_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'router.xml'
    ) {
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName,
            $this->_idAttributes);
    }
}