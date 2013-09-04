<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Parser\Adapter;

use Magento\Tools\I18n\Code\Context;
use Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\PhraseCollector;

/**
 * Php parser adapter
 */
class Php extends AbstractAdapter
{
    /**
     * Phrase collector
     *
     * @var \Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\PhraseCollector
     */
    protected $_phraseCollector;

    /**
     * Adapter construct
     *
     * @param \Magento\Tools\I18n\Code\Context $context
     * @param \Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\PhraseCollector $phraseCollector
     */
    public function __construct(Context $context, PhraseCollector $phraseCollector)
    {
        parent::__construct($context);

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
