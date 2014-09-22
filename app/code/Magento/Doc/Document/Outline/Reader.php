<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document\Outline;

use Magento\Framework\Config\ValidationStateInterface;
use Magento\Doc\Document\Filter;

/**
 * Class Reader
 * @package Magento\Doc\Document\Outline
 */
class Reader
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $idAttributes = [
        '/outline/content/item' => 'name'
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
     * @var Filter
     */
    protected $filter;

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
     * Dom Interface Factory
     *
     * @var DomFactory
     */
    protected $domFactory;

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
     * @param ValidationState $validationState
     * @param Filter $filter
     * @param string DomFactory $domFactory
     * @param array $idAttributes
     * @param string $defaultScope
     */
    public function __construct(
        FileResolver $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationState $validationState,
        Filter $filter,
        DomFactory $domFactory,
        $idAttributes = [],
        $defaultScope = 'doc'
    ) {
        $this->fileResolver = $fileResolver;
        $this->converter = $converter;
        $this->filter = $filter;
        $this->schemaFile = $schemaLocator->getSchema();
        $this->perFileSchema = $schemaLocator->getPerFileSchema() &&
        $this->isValidated ? $schemaLocator->getPerFileSchema() : null;
        $this->isValidated = $validationState->isValidated();
        $this->domFactory = $domFactory;
        $this->idAttributes = array_replace($this->idAttributes, $idAttributes);
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
        $this->applyTemplate($output);
        return $output;
    }

    /**
     * Apply template to the element
     *
     * @param array $parent
     * @return null
     */
    protected function applyTemplate(array & $parent)
    {
        if (isset($parent['content'])) {
            foreach ($parent['content'] as & $child) {
                if (isset($child['template'])) {
                    $fileName = $child['template'] . '.xml';
                    $fileList = $this->fileResolver->get($fileName);
                    if (count($fileList)) {
                        $template = $this->_readFiles($fileList, $child);
                        if ($template) {
                            $child = array_replace_recursive($template, $child);
                            unset($child['template']);
                        }
                    }
                } else {
                    $this->applyTemplate($child);
                }
            }
        }
        return null;
    }

    /**
     * Read configuration files
     *
     * @param array $fileList
     * @param array $templateVars
     * @return array
     * @throws \Magento\Framework\Exception
     */
    protected function _readFiles($fileList, array $templateVars = [])
    {
        /** @var \Magento\Framework\Config\Dom $configMerger */
        $configMerger = null;
        foreach ($fileList as $key => $file) {
            /** @var \Magento\Framework\View\File $file */
            $content = file_get_contents($file->getFilename());
            if ($templateVars) {
                $this->filter->setVariables($templateVars);
                $content = $this->filter->preProcess($content);
            }
            try {
                if (!$configMerger) {
                    $configMerger = $this->domFactory->create(
                        [
                            'xml' => $content,
                            'idAttributes' => $this->idAttributes,
                            'schemaFile' => $this->perFileSchema
                        ]
                    );
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

        $output = [];
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
