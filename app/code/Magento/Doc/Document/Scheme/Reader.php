<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document\Scheme;

use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\ValidationStateInterface;

class Reader
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $idAttributes = [
        '/scheme/content/item' => 'name'
    ];

    /**
     * File locator
     *
     * @var \Magento\Framework\Config\FileResolverInterface
     */
    protected $fileResolver;

    /**
     * Config converter
     *
     * @var \Magento\Framework\Config\ConverterInterface
     */
    protected $converter;

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
    protected $perFileSchema;

    /**
     * Class of dom configuration document used for merge
     *
     * @var string
     */
    protected $domDocumentClass;

    /**
     * Should configuration be validated
     *
     * @var bool
     */
    protected $isValidated;

    /**
     * @param FileResolver $fileResolver
     * @param Converter $converter
     * @param SchemaLocator $schemaLocator
     * @param ValidationStateInterface $validationState
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        FileResolver $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationStateInterface $validationState,
        $idAttributes = [],
        $domDocumentClass = 'Magento\Framework\Config\Dom',
        $defaultScope = 'doc'
    ) {
        $this->fileResolver = $fileResolver;
        $this->converter = $converter;
        $this->idAttributes = array_replace($this->idAttributes, $idAttributes);
        $this->schemaFile = $schemaLocator->getSchema();
        $this->isValidated = $validationState->isValidated();
        $this->perFileSchema = $schemaLocator->getPerFileSchema() &&
        $this->isValidated ? $schemaLocator->getPerFileSchema() : null;
        $this->domDocumentClass = $domDocumentClass;
        $this->defaultScope = $defaultScope;
    }

    /**
     * Load file
     *
     * @param string $fileName
     * @param null $scope
     * @return array
     */
    public function read($fileName, $scope = null)
    {
        $scope = $scope ?: $this->defaultScope;
        $fileList = $this->fileResolver->get($fileName, $scope);
        if (!count($fileList)) {
            return [];
        }
        $output = $this->_readFiles($fileList);

        return $output;
    }

    /**
     * Read configuration files
     *
     * @param array $fileList
     * @return array
     * @throws \Magento\Framework\Exception
     */
    protected function _readFiles($fileList)
    {
        /** @var \Magento\Framework\Config\Dom $configMerger */
        $configMerger = null;
        foreach ($fileList as $key => $content) {
            try {
                if (!$configMerger) {
                    $configMerger = $this->_createConfigMerger($this->domDocumentClass, $content);
                } else {
                    $configMerger->merge($content);
                }
            } catch (\Magento\Framework\Config\Dom\ValidationException $e) {
                throw new \Magento\Framework\Exception("Invalid XML in file " . $key . ":\n" . $e->getMessage());
            }
        }
        if ($this->isValidated) {
            $errors = array();
            if ($configMerger && !$configMerger->validate($this->schemaFile, $errors)) {
                $message = "Invalid Document \n";
                throw new \Magento\Framework\Exception($message . implode("\n", $errors));
            }
        }

        $output = array();
        if ($configMerger) {
            $output = $this->converter->convert($configMerger->getDom());
        }
        return $output;
    }

    /**
     * Return newly created instance of a config merger
     *
     * @param string $mergerClass
     * @param string $initialContents
     * @return \Magento\Framework\Config\Dom
     * @throws \UnexpectedValueException
     */
    protected function _createConfigMerger($mergerClass, $initialContents)
    {
        $result = new $mergerClass($initialContents, $this->idAttributes, null, $this->perFileSchema);
        if (!$result instanceof \Magento\Framework\Config\Dom) {
            throw new \UnexpectedValueException(
                "Instance of the DOM config merger is expected, got {$mergerClass} instead."
            );
        }
        return $result;
    }
}
