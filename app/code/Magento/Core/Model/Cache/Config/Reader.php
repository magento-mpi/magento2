<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Cache_Config_Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/type' => 'name',
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param Magento_Core_Model_Cache_Config_Converter $converter
     * @param Magento_Core_Model_Cache_Config_SchemaLocator $schemeLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        Magento_Core_Model_Cache_Config_Converter $converter,
        Magento_Core_Model_Cache_Config_SchemaLocator $schemeLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'cache.xml',
        $idAttributes = array(),
        $domDocumentClass = '\Magento\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemeLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
