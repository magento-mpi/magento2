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
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Core_Model_Email_Template_Config_Converter $converter
     * @param Magento_Core_Model_Email_Template_Config_SchemaLocator $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Core_Model_Email_Template_Config_Converter $converter,
        Magento_Core_Model_Email_Template_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState
    ) {
        $fileName = 'email_templates.xml';
        $idAttributes = array(
            '/config/template' => 'id',
        );
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes);
    }

    /**
     * Add information on context of a module, config file belongs to
     *
     * {@inheritdoc}
     */
    protected function _readFileContents($filename)
    {
        $result = parent::_readFileContents($filename);
        $result = str_replace('<template ', '<template module="' . $this->_getModuleName($filename) . '" ', $result);
        return $result;
    }

    /**
     * Determine fully-qualified module name config file belongs to
     *
     * @param string $filename Supported format: <modules_dir>/<Namespace>/<Module>/etc/*.xml
     * @return string
     */
    protected function _getModuleName($filename)
    {
        $namespace = basename(dirname(dirname(dirname($filename))));
        $module = basename(dirname(dirname($filename)));
        $moduleFull = $namespace . '_' . $module;
        return $moduleFull;
    }
}
