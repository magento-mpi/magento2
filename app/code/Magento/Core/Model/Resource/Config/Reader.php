<?php
/**
 * Resources configuration filesystem loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Resource_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/resource' => 'name'
    );

    /**
     * @var Magento_Core_Model_Config_Local
     */
    protected $_configLocal;

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Core_Model_Resource_Config_Converter $converter
     * @param Magento_Core_Model_Resource_Config_SchemaLocator $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param Magento_Core_Model_Config_Local $configLocal
     * @param string $fileName
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Core_Model_Resource_Config_Converter $converter,
        Magento_Core_Model_Resource_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        Magento_Core_Model_Config_Local $configLocal,
        $fileName = 'resources.xml'
    ) {
        $this->_configLocal = $configLocal;
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName);
    }

    /**
     * Load configuration scope
     *
     * @param string|null $scope
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function read($scope = null)
    {
        $data = parent::read();
        $data = array_replace($data, $this->_configLocal->getResources());

        return $data;
    }
}
