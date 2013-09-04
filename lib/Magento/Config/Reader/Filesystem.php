<?php
/**
 * Filesystem configuration loader. Loads configuration from XML files, split by scopes
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Config\Reader;

class Filesystem implements \Magento\Config\ReaderInterface
{
    /**
     * File locator
     *
     * @var \Magento\Config\FileResolverInterface
     */
    protected $_fileResolver;

    /**
     * Config converter
     *
     * @var \Magento\Config\Converter\Dom
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
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Config\ConverterInterface $converter
     * @param string $fileName
     * @param array $idAttributes
     * @param string $schema
     * @param string $perFileSchema
     * @param bool $isValidated
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Config\ConverterInterface $converter,
        $fileName,
        $idAttributes,
        $schema = null,
        $perFileSchema = null,
        $isValidated = false,
        $domDocumentClass = '\Magento\Config\Dom'
    ) {
        $this->_fileResolver = $fileResolver;
        $this->_converter = $converter;
        $this->_fileName = $fileName;
        $this->_idAttributes = array_replace($this->_idAttributes, $idAttributes);
        $this->_schemaFile = $schema;
        $this->_perFileSchema = $perFileSchema && $isValidated ? $perFileSchema : null;
        $this->_isValidated = $isValidated;
        $this->_domDocumentClass = $domDocumentClass;
    }

    /**
     * Load configuration scope
     *
     * @param string $scope
     * @return array
     * @throws \Magento\Exception
     */
    public function read($scope)
    {
        $fileList = $this->_fileResolver->get($this->_fileName, $scope);
        if (!count($fileList)) {
            return array();
        }
        /** @var \Magento\Config\Dom $domDocument */
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
            } catch (\Magento\Config\Dom\ValidationException $e) {
                throw new \Magento\Exception("Invalid XML in file " . $file . ":\n" . $e->getMessage());
            }
        }
        if ($this->_isValidated) {
            $errors = array();
            if ($domDocument && !$domDocument->validate($this->_schemaFile, $errors)) {
                $message = "Invalid Document \n";
                throw new \Magento\Exception($message . implode("\n", $errors));
            }
        }

        $output = array();
        if ($domDocument) {
            $output = $this->_converter->convert($domDocument->getDom());
        }
        return $output;
    }
}
