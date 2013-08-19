<?php
/**
 * Default configuration data reader. Reads configuration data from storage
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Initial_Reader
{
    /**
     * File locator
     *
     * @var Magento_Config_FileResolverInterface
     */
    protected $_fileResolver;

    /**
     * Config converter
     *
     * @var Mage_Core_Model_Config_Initial_Converter
     */
    protected $_converter;

    /**
     * Config file name
     *
     * @var string
     */
    protected $_fileName;

    /**
     * Class of dom configuration document used for merge
     *
     * @var string
     */
    protected $_domDocumentClass;

    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('primary', 'global');

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Mage_Core_Model_Config_Initial_Converter $converter
     * @param string $fileName
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Mage_Core_Model_Config_Initial_Converter $converter,
        $fileName = 'config.xml',
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        $this->_fileResolver = $fileResolver;
        $this->_converter = $converter;
        $this->_domDocumentClass = $domDocumentClass;
        $this->_fileName = $fileName;
    }

    /**
     * Load configuration scope
     *
     * @return array
     *
     * @throws Magento_Exception
     */
    public function read()
    {
        $fileList = array();
        foreach ($this->_scopePriorityScheme as $scope) {
            $fileList = array_merge($fileList, $this->_fileResolver->get($this->_fileName, $scope));
        }

        if (!count($fileList)) {
            return array();
        }

        /** @var Magento_Config_Dom $domDocument */
        $domDocument = null;
        foreach ($fileList as $file) {
            try {
                if (is_null($domDocument)) {
                    $class = $this->_domDocumentClass;
                    $domDocument = new $class(file_get_contents($file));
                } else {
                    $domDocument->merge(file_get_contents($file));
                }
            } catch (Magento_Config_Dom_ValidationException $e) {
                throw new Magento_Exception("Invalid XML in file " . $file . ":\n" . $e->getMessage());
            }
        }

        $output = array();
        if ($domDocument) {
            $output = $this->_converter->convert($domDocument->getDom());
        }
        return $output;
    }

}
