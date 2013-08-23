<?php
/**
 * Scan source code for detects invocations of __() function, analyzes placeholders with arguments
 * and see if they not equal
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Phrase_ArgumentsTest extends Magento_Test_Integrity_Phrase_TestAbstract
{
    protected function setUp()
    {
        $this->_phraseCollector = new Magento_Tokenizer_PhraseCollector();
    }

    public function testCase()
    {
        foreach ($this->_getFiles() as $file) {
            $this->_phraseCollector->parse($file);
            foreach ($this->_phraseCollector->getPhrases() as $phrase) {
                if (preg_match_all('/%(\d+)/', $phrase['phrase'], $matches) || $phrase['arguments'] > 0) {
                    $placeholdersInPhrase = array_unique($matches[1]);
                    if (count($placeholdersInPhrase) != $phrase['arguments']) {
                        $this->addError($phrase);
                    }
                }
            }
        }
        $message = $this->_prepareErrorMessage('%d usages of inconsistency the number of arguments and placeholders '
            . 'were discovered: %s', $this->_errors);
        $this->assertEmpty($this->_errors, $message);

    }
}
