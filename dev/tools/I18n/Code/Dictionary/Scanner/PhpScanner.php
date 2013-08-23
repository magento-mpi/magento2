<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary\Scanner;

/**
 * Generate dictionary from phrases
 */
class PhpScanner extends FileScanner
{
    /**
     * {@inheritdoc}
     */
    const FILE_MASK = '/\.(php|phtml)$/';

    /**
     * {@inheritdoc}
     */
    protected $_defaultPathes = array(
        '/app/code/',
        '/app/design/',
    );

    /**
     * Collect phrases from php
     */
    protected function _collectPhrases()
    {
        $phraseCollector = new \Magento_Tokenizer_PhraseCollector();
        foreach ($this->_getFiles() as $file) {
            $phraseCollector->parse($file);
            foreach ($phraseCollector->getPhrases() as $phrase) {
                $this->_addPhrase($phrase['phrase'], $phrase['file'], $phrase['line']);
            }
        }
    }
}
