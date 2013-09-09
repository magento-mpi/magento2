<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Widget_Model_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/widgets/widget' => 'id',
        '/widgets/widget/parameter' => 'name',
        '/widgets/widget/parameter/option' => 'name',
        '/widgets/widget/container' => 'name',
        '/widgets/widget/container/template' => 'name',
    );

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Widget_Model_Config_Converter $converter
     * @param Magento_Config_SchemaLocatorInterface $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Widget_Model_Config_Converter $converter,
        Magento_Config_SchemaLocatorInterface $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'widget.xml',
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

    /**
     * Load configuration file
     *
     * @param string $file
     * @return array
     * @throws Magento_Exception
     */
    public function readFile($file)
    {
        return $this->_readFiles(array($file));
    }
}
