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
class Magento_Test_Integrity_Phrase_ArgumentsTest extends Magento_Test_Integrity_Phrase_AbstractTestCase
{
    /**
     * @var Magento_Tokenizer_PhraseCollector
     */
    protected $_phraseCollector;

    protected function setUp()
    {
        $this->_phraseCollector = new Magento_Tokenizer_PhraseCollector();
    }

    public function testCase()
    {
        $errors = array();
        foreach ($this->_getFiles() as $file) {
            $this->_phraseCollector->parse($file);
            foreach ($this->_phraseCollector->getPhrases() as $phrase) {
                if (preg_match_all('/%(\d+)/', $phrase['phrase'], $matches) || $phrase['arguments']) {
                    $placeholdersInPhrase = array_unique($matches[1]);
                    if (count($placeholdersInPhrase) != $phrase['arguments']) {
                        $errors[] = $this->_createPhraseError($phrase);
                    }
                }
            }
        }
        $this->assertEmpty(
            $errors,
            sprintf("\n%d usages of inconsistency the number of arguments and placeholders were discovered: \n%s",
                count($errors), implode("\n\n", $errors))
        );
    }
}
