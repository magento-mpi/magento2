<?php
/**
 * Scan source code for detects invocations of outdated __() method
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Phrase_Legacy_SignatureTest extends Magento_Test_Integrity_Phrase_AbstractTestCase
{
    /**
     * @var Magento_Tokenizer_Translate_MethodCollector
     */
    protected $_phraseCollector;

    protected function setUp()
    {
        $this->_phraseCollector = new Magento_Tokenizer_Translate_MethodCollector();
    }

    public function testCase()
    {
        $errors = array();
        foreach ($this->_getFiles() as $file) {
            $this->_phraseCollector->parse($file);
            foreach ($this->_phraseCollector->getPhrases() as $phrase) {
                $errors[] = $this->_createPhraseErrorMessage($phrase);
            }
        }
        $this->assertEmpty(
            $errors,
            sprintf('%d usages of the old translation method call were discovered: %s', count($errors),
                implode("\n\n", $errors))
        );
    }
}
