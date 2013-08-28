<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Parser;

use Magento\Tools\I18n\Code\Dictionary\ContextDetector;

/**
 * Php data parser
 */
class Php extends AbstractParser
{
    /**
     * @var \Magento_Tokenizer_PhraseCollector
     */
    protected $_phraseCollector;

    /**
     * Parser construct
     *
     * @param array $files
     * @param ContextDetector $contextDetector
     * @param \Magento_Tokenizer_PhraseCollector $phraseCollector
     */
    public function __construct(
        array $files,
        ContextDetector $contextDetector,
        \Magento_Tokenizer_PhraseCollector $phraseCollector
    ) {
        parent::__construct($files, $contextDetector);

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
