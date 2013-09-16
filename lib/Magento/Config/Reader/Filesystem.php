<?php
/**
 * Filesystem configuration loader. Loads configuration from XML files, split by scopes
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Config_Reader_Filesystem implements Magento_Config_ReaderInterface
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
     * @var Magento_Config_Converter_Dom
     */
    protected $_converter;

    /**
     * The name of file that stores configuration
     *
     * @var string
     */
    protected $_fileName;

    /**
     * Path to corresponding XSD file with validation rules for merged config
     *
     * @var string
     */
    protected $_schema;

    /**
     * Path to corresponding XSD file with validation rules for separate config files
     *
     * @var string
     */
    protected $_perFileSchema;

    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array();

    /**
     * Class of dom configuration document used for merge
     *
     * @var string
     */
    protected $_domDocumentClass;

    /**
     * Should configuration be validated
     *
     * @var bool
     */
    protected $_isValidated;

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Config_ConverterInterface $converter
     * @param Magento_Config_SchemaLocatorInterface $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param $fileName
     * @param $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Config_ConverterInterface $converter,
        Magento_Config_SchemaLocatorInterface $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName,
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        $this->_fileResolver = $fileResolver;
        $this->_converter = $converter;
        $this->_fileName = $fileName;
        $this->_idAttributes = array_replace($this->_idAttributes, $idAttributes);
        $this->_schemaFile = $schemaLocator->getSchema();
        $this->_isValidated = $validationState->isValidated();
        $this->_perFileSchema = $schemaLocator->getPerFileSchema() && $this->_isValidated
            ? $schemaLocator->getPerFileSchema()
            : null;
        $this->_domDocumentClass = $domDocumentClass;
    }

    /**
     * Load configuration scope
     *
     * @param string $scope
     * @return array
     * @throws Magento_Exception
     */
    public function read($scope)
    {
        $fileList = $this->_fileResolver->get($this->_fileName, $scope);
        if (!count($fileList)) {
            return array();
        }
        $output = $this->_readFiles($fileList);

        return $output;
    }

    /**
     * @param array $fileList
     * @return array
     * @throws Magento_Exception
     */
    protected function _readFiles(array $fileList)
    {
        /** @var Magento_Config_Dom $domDocument */
        $domDocument = null;
        foreach ($fileList as $file) {
            try {
                if (is_null($domDocument)) {
                    $class = $this->_domDocumentClass;
                    $domDocument = new $class(
                        file_get_contents($file),
                        $this->_idAttributes,
                        $this->_perFileSchema
                    );
                } else {
                    $domDocument->merge(file_get_contents($file));
                }
            } catch (Magento_Config_Dom_ValidationException $e) {
                throw new Magento_Exception("Invalid XML in file " . $file . ":\n" . $e->getMessage());
            }
        }
        if ($this->_isValidated) {
            $errors = array();
            if ($domDocument && !$domDocument->validate($this->_schemaFile, $errors)) {
                $message = "Invalid Document \n";
                throw new Magento_Exception($message . implode("\n", $errors));
            }
        }

        $output = array();
        if ($domDocument) {
            $output = $this->_converter->convert($domDocument->getDom());
        }
        return $output;
    }
}
