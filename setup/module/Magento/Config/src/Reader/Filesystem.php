<?php
/**
 * Filesystem configuration loader. Loads configuration from XML files
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 *
 */

namespace Magento\Config\Reader;

use Magento\Config\FileResolverInterface;
use Magento\Framework\Config\ConverterInterface;
use Magento\Config\SchemaLocatorInterface;

class Filesystem implements ReaderInterface
{
    /**
     * File locator
     *
     * @var \Magento\Config\FileResolverInterface
     */
    protected $fileResolver;

    /**
     * Config converter
     *
     * @var ConverterInterface
     */
    protected $converter;

    /**
     * Path to corresponding XSD file with validation rules
     *
     * @var string
     */
    protected $schemaFile;

    /**
     * The name of file that stores configuration
     *
     * @var string
     */
    protected $fileName;

    /**
     * Class of dom configuration document used for merge
     *
     * @var string
     */
    protected $domDocumentClass;

    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $idAttributes = array();

    /**
     * @param FileResolverInterface $fileResolver
     * @param ConverterInterface $converter
     * @param SchemaLocatorInterface $schemaLocator
     * @param string $fileName
     * @param string $domDocumentClass
     * @param array $idAttributes
     */
    public function __construct(
        FileResolverInterface $fileResolver,
        ConverterInterface $converter,
        SchemaLocatorInterface $schemaLocator,
        $fileName,
        $domDocumentClass = '\Magento\Framework\Config\Dom',
        $idAttributes = array()
    ) {
        $this->fileResolver = $fileResolver;
        $this->converter = $converter;
        $this->fileName = $fileName;
        $this->idAttributes = array_replace($this->idAttributes, $idAttributes);
        $this->schemaFile = $schemaLocator->getSchema();
        $this->domDocumentClass = $domDocumentClass;
    }

    /**
     * Load configuration
     *
     * @return array
     */
    public function read()
    {
        $fileList = $this->fileResolver->get($this->fileName);
        if (!count($fileList)) {
            return array();
        }
        return $this->readFiles($fileList);
    }

    /**
     * Read configuration files
     *
     * @param array $fileList
     * @return array
     * @throws \Exception
     */
    protected function readFiles($fileList)
    {
        /** @var \Magento\Framework\Config\Dom $configMerger */
        $configMerger = null;
        foreach ($fileList as $key => $content) {
            try {
                if (!$configMerger) {
                    $configMerger = $this->createConfigMerger($this->domDocumentClass, $content);
                } else {
                    $configMerger->merge($content);
                }
            } catch (\Magento\Framework\Config\Dom\ValidationException $e) {
                throw new \Exception("Invalid XML in file " . $key . ":\n" . $e->getMessage());
            }
        }

        $errors = array();
        if ($configMerger && !$configMerger->validate($this->schemaFile, $errors)) {
            $message = "Invalid Document \n";
            throw new \Exception($message . implode("\n", $errors));
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
    protected function createConfigMerger($mergerClass, $initialContents)
    {
        $result = new $mergerClass($initialContents, $this->idAttributes, null, $this->schemaFile);
        if (!$result instanceof \Magento\Framework\Config\Dom) {
            throw new \UnexpectedValueException(
                "Instance of the DOM config merger is expected, got {$mergerClass} instead."
            );
        }
        return $result;
    }
}
