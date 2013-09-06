<?php
/**
 * Routes configuration reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Route_Config_Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of paths to identifiable nodes
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/router'               => 'id',
        '/config/router/route'         => 'id',
        '/config/router/route/module'  => 'name'
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param Magento_Core_Model_Route_Config_Converter $converter
     * @param Magento_Core_Model_Route_Config_SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        Magento_Core_Model_Route_Config_Converter $converter,
        Magento_Core_Model_Route_Config_SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'routes.xml'
    ) {
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName,
            $this->_idAttributes);
    }
}