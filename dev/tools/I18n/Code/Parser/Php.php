<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Parser;

use Magento\Tools\I18n\Code\Context;
use Magento\Tools\I18n\Code\Parser\Php\Tokenizer\PhraseCollector;

/**
 * Php data parser
 */
class Php extends AbstractParser
{
    /**
     * Phrase collector
     *
     * @var \Magento\Tools\I18n\Code\Parser\Php\Tokenizer\PhraseCollector
     */
    protected $_phraseCollector;

    /**
     * Parser construct
     *
     * @param array $files
     * @param \Magento\Tools\I18n\Code\Context $context
     * @param \Magento\Tools\I18n\Code\Parser\Php\Tokenizer\PhraseCollector $phraseCollector
     */
    public function __construct(array $files, Context $context, PhraseCollector $phraseCollector)
    {
        parent::__construct($files, $context);

        $this->_phraseCollector = $phraseCollector;
    }

    /**
     * {@inheritdoc}
     */
    protected function _parse($file)
    {
        $this->_phraseCollector->parse($file);

        foreach ($this->_phraseCollector->getPhrases() as $phrase) {
            $this->_addPhrase($phrase['phrase'], $phrase['file'], $phrase['line']);
        }
    }
}
