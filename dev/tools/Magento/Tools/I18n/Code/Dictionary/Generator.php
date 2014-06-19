<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\I18n\Code\Dictionary;

use Magento\Tools\I18n\Code\Factory;
use Magento\Tools\I18n\Code\ParserInterface;

/**
 * Dictionary generator
 */
class Generator
{
    /**
     * Parser
     *
     * @var \Magento\Tools\I18n\Code\ParserInterface
     */
    protected $parser;

    /**
     * Contextual parser
     *
     * @var \Magento\Tools\I18n\Code\ParserInterface
     */
    protected $contextualParser;

    /**
     * Domain abstract factory
     *
     * @var \Magento\Tools\I18n\Code\Factory
     */
    protected $factory;

    /**
     * Generator options resolver
     *
     * @var Options\ResolverFactory
     */
    protected $optionResolverFactory;

    /**
     * @var WriterInterface
     */
    protected $writer;

    /**
     * Generator construct
     *
     * @param ParserInterface $parser
     * @param ParserInterface $contextualParser
     * @param Factory $factory
     * @param Options\ResolverFactory $optionsResolver
     */
    public function __construct(
        ParserInterface $parser,
        ParserInterface $contextualParser,
        Factory $factory,
        Options\ResolverFactory $optionsResolver
    ) {
        $this->parser = $parser;
        $this->contextualParser = $contextualParser;
        $this->factory = $factory;
        $this->optionResolverFactory = $optionsResolver;
    }

    /**
     * Generate dictionary
     *
     * @param string $directory
     * @param string $outputFilename
     * @param bool $withContext
     * @return void
     */
    public function generate($directory, $outputFilename, $withContext = false)
    {
        $optionResolver = $this->optionResolverFactory->create($directory, $withContext);

        $parser = $this->getActualParser($withContext);
        $parser->parse($optionResolver->getOptions());

        $phraseList = $parser->getPhrases();
        if (!count($phraseList)) {
            throw new \UnexpectedValueException('No phrases found by given path.');
        }
        foreach ($phraseList as $phrase) {
            $this->getDictionaryWriter($outputFilename)->write($phrase);
        }
        $this->writer = null;
    }

    /**
     * @param string $outputFilename
     * @return WriterInterface
     */
    protected function getDictionaryWriter($outputFilename)
    {
        if (null === $this->writer) {
            $this->writer = $this->factory->createDictionaryWriter($outputFilename);
        }
        return $this->writer;
    }

    /**
     * Get actual parser
     *
     * @param bool $withContext
     * @return \Magento\Tools\I18n\Code\ParserInterface
     */
    protected function getActualParser($withContext)
    {
        return $withContext ? $this->contextualParser : $this->parser;
    }
}
