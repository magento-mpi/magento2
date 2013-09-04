<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Parser;

use Magento\Tools\I18n\Code;

/**
 *  Parser factory
 */
class Factory
{
    /**
     * Context
     *
     * @var \Magento\Tools\I18n\Code\Context
     */
    protected $_context;

    /**
     * Factory construct
     *
     * @param \Magento\Tools\I18n\Code\Context $context
     */
    public function __construct(Code\Context $context)
    {
        $this->_context = $context;
    }

    /**
     * Create parser
     *
     * @param array $options
     * @return \Magento\Tools\I18n\Code\ParserInterface
     */
    public function createParser(array $options)
    {
        $this->_validateOptions($options);

        $parser = new Composite();
        $filesCollector = new Code\FilesCollector();

        foreach ($options as $parserOptions) {
            $fileMask = isset($parserOptions['fileMask']) ? $parserOptions['fileMask']  : '';
            $files = $filesCollector->getFiles($parserOptions['paths'], $fileMask);

            $parser->add($this->_createParserInstance($parserOptions['type'], $files));
        }
        return $parser;
    }

    /**
     * Validate options
     *
     * @param array $options
     * @throws \InvalidArgumentException
     */
    protected function _validateOptions($options)
    {
        foreach ($options as $parse) {
            if (empty($parse['type'])) {
                throw new \InvalidArgumentException('Missed type in parser options.');
            }
            if (empty($parse['paths'])) {
                throw new \InvalidArgumentException('Missed paths in parser options.');
            }
            if (!is_array($parse['paths'])) {
                throw new \InvalidArgumentException('Paths in parser options must be array.');
            }
            if (empty($parse['type'])) {
                throw new \InvalidArgumentException('Missed type in parser options.');
            }
        }
    }

    /**
     * Create parser instance
     *
     * @param string $type
     * @param array $files
     * @return \Magento\Tools\I18n\Code\ParserInterface
     * @throws \InvalidArgumentException
     */
    protected function _createParserInstance($type, $files)
    {
        switch ($type) {
            case 'php':
                $phraseCollector = new Code\Parser\Php\Tokenizer\PhraseCollector(new Code\Parser\Php\Tokenizer());
                $parserInstance = new Php($files, $this->_context, $phraseCollector);
                break;
            default:
                $className = __NAMESPACE__ . '\\' . ucfirst(strtolower($type));
                if (!class_exists($className)) {
                    throw new \InvalidArgumentException(sprintf('Wrong type "%s" in parser options.', $type));
                }
                $reflectionClass = new \ReflectionClass($className);
                $parserInstance = $reflectionClass->newInstanceArgs(array($files, $this->_context));
        }
        return $parserInstance;
    }
}
