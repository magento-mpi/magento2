<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

use Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\Translate\MethodCollector;
use Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer;

/**
 * Scan source code for detects invocations of outdated __() method
 */
class Magento_Test_Integrity_Phrase_Legacy_SignatureTest extends Magento_Test_Integrity_Phrase_AbstractTestCase
{
    /**
     * @var \Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\Translate\MethodCollector
     */
    protected $_phraseCollector;

    protected function setUp()
    {
        $this->_phraseCollector = new MethodCollector(new Tokenizer());
    }

    public function testSignature()
    {
        return;
        $errors = array();
        foreach ($this->_getFiles() as $file) {
            $this->_phraseCollector->parse($file);
            foreach ($this->_phraseCollector->getPhrases() as $phrase) {
                $errors[] = $this->_createPhraseError($phrase);
            }
        }
        $this->assertEmpty(
            $errors,
            sprintf('%d usages of the old translation method call were discovered: %s', count($errors),
                implode("\n\n", $errors))
        );
    }
}
