<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code;

use Magento\Tools\I18n\Code\ParserInterface;
use Magento\Tools\I18n\Code\FilesCollector;
use Magento\Tools\I18n\Code\Parser\AdapterInterface;

/**
 * Parser
 */
class Parser implements ParserInterface
{
    /**
     * Files collector
     *
     * @var \Magento\Tools\I18n\Code\FilesCollector
     */
    protected $_filesCollector = array();

    /**
     * Adapters
     *
     * @var \Magento\Tools\I18n\Code\Parser\AdapterInterface[]
     */
    protected $_adapters = array();

    /**
     * Parsed phrases
     *
     * @var array
     */
    protected $_phrases = array();

    /**
     * Parser construct
     *
     * @param \Magento\Tools\I18n\Code\FilesCollector $filesCollector
     */
    public function __construct(FilesCollector $filesCollector)
    {
        $this->_filesCollector = $filesCollector;
    }

    /**
     * Add parser
     *
     * @param string $type
     * @param \Magento\Tools\I18n\Code\Parser\AdapterInterface $adapter
     */
    public function addAdapter($type, AdapterInterface $adapter)
    {
        $this->_adapters[$type] = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(array $parseOptions)
    {
        $this->_validateOptions($parseOptions);

        foreach ($parseOptions as $parserOptions) {
            $type = $parserOptions['type'];
            if (!isset($this->_adapters[$type])) {
                throw new \InvalidArgumentException(sprintf('Adapter is not set for type "%s".', $type));
            }

            $fileMask = isset($parserOptions['fileMask']) ? $parserOptions['fileMask']  : '';
            $files = $this->_filesCollector->getFiles($parserOptions['paths'], $fileMask);
            foreach ($files as $file) {
                $this->_adapters[$type]->parse($file);
                $this->_phrases = array_merge($this->_phrases, $this->_adapters[$type]->getPhrases());
            }
        }
    }

    /**
     * Validate options
     *
     * @param array $parseOptions
     * @throws \InvalidArgumentException
     */
    protected function _validateOptions($parseOptions)
    {
        foreach ($parseOptions as $parserOptions) {
            if (empty($parserOptions['type'])) {
                throw new \InvalidArgumentException('Missed "type" in parser options.');
            }
            if (!isset($parserOptions['paths']) || !is_array($parserOptions['paths'])) {
                throw new \InvalidArgumentException('"paths" in parser options must be array.');
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getPhrases()
    {
        return $this->_phrases;
    }
}
