<?php
/**
 * Loads email template configuration from multiple XML files by merging them together
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Email_Template_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * @var Magento_Core_Model_Module_Dir_ReverseResolver
     */
    private $_moduleDirResolver;

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Core_Model_Email_Template_Config_Converter $converter
     * @param Magento_Core_Model_Email_Template_Config_SchemaLocator $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param Magento_Core_Model_Module_Dir_ReverseResolver $moduleDirResolver
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Core_Model_Email_Template_Config_Converter $converter,
        Magento_Core_Model_Email_Template_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        Magento_Core_Model_Module_Dir_ReverseResolver $moduleDirResolver
    ) {
        $fileName = 'email_templates.xml';
        $idAttributes = array(
            '/config/template' => 'id',
        );
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes);
        $this->_moduleDirResolver = $moduleDirResolver;
    }

    /**
     * Add information on context of a module, config file belongs to
     *
     * {@inheritdoc}
     * @throws UnexpectedValueException
     */
    protected function _readFileContents($filename)
    {
        $result = parent::_readFileContents($filename);
        $moduleName = $this->_moduleDirResolver->getModuleName($filename);
        if (!$moduleName) {
            throw new UnexpectedValueException("Unable to determine a module, file '$filename' belongs to.");
        }
        $result = str_replace('<template ', '<template module="' . $moduleName . '" ', $result);
        return $result;
    }
}
