<?php
/**
 * Default configuration data reader. Reads configuration data from storage
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Framework\App\Config\Initial;

class Reader
{
    /**
     * File locator
     *
     * @var \Magento\Framework\Config\FileResolverInterface
     */
    protected $_fileResolver;

    /**
     * Config converter
     *
     * @var  \Magento\Framework\Config\ConverterInterface
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
     * Path to corresponding XSD file with validation rules for config
     *
     * @var string
     */
    protected $_schemaFile;

    /**
     * @param \Magento\Framework\Config\FileResolverInterface $fileResolver
     * @param \Magento\Framework\Config\ConverterInterface $converter
     * @param SchemaLocator $schemaLocator
     * @param \Magento\Framework\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \Magento\Framework\Config\ConverterInterface $converter,
        SchemaLocator $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        $fileName = 'config.xml',
        $domDocumentClass = 'Magento\Framework\Config\Dom'
    ) {
        $this->_schemaFile = $validationState->isValidated() ? $schemaLocator->getSchema() : null;
        $this->_fileResolver = $fileResolver;
        $this->_converter = $converter;
        $this->_domDocumentClass = $domDocumentClass;
        $this->_fileName = $fileName;
    }

    /**
     * Read configuration scope
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception
     */
    public function read()
    {
        $fileList = array();
        foreach ($this->_scopePriorityScheme as $scope) {
            $directories = $this->_fileResolver->get($this->_fileName, $scope);
            foreach ($directories as $key => $directory) {
                $fileList[$key] = $directory;
            }
        }

        if (!count($fileList)) {
            return array();
        }

        /** @var \Magento\Framework\Config\Dom $domDocument */
        $domDocument = null;
        foreach ($fileList as $file) {
            try {
                if (is_null($domDocument)) {
                    $class = $this->_domDocumentClass;
                    $domDocument = new $class($file, array(), null, $this->_schemaFile);
                } else {
                    $domDocument->merge($file);
                }
            } catch (\Magento\Framework\Config\Dom\ValidationException $e) {
                throw new \Magento\Framework\Exception("Invalid XML in file " . $file . ":\n" . $e->getMessage());
            }
        }

        $output = array();
        if ($domDocument) {
            $output = $this->_converter->convert($domDocument->getDom());
        }
        return $output;
    }
}
