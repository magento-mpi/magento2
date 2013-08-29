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
class Magento_Test_Integrity_Phrase_ArgumentsTest extends PHPUnit_Framework_TestCase
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
        $path = Utility_Files::init()->getPathToSource() . '/app/';
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach (new RegexIterator($files, '/\.(php|phtml)$/') as $file) {
            $this->_phraseCollector->parse($file);
            foreach ($this->_phraseCollector->getPhrases() as $phrase) {
                if (preg_match_all('/%(\d+)/', $phrase['phrase'], $matches) || $phrase['arguments'] > 0) {
                    $placeholdersInPhrase = array_unique($matches[1]);
                    if (count($placeholdersInPhrase) != $phrase['arguments']) {
                        $errors[] = "\nPhrase: " . $phrase['phrase'] .
                            "\nFile: " . $phrase['file'] .
                            "\nLine: " . $phrase['line'];
                    }
                }
            }
        }
        $message = sprintf("\n%d usages of inconsistency the number of arguments and placeholders were discovered: %s",
            count($errors), "\n" . implode("\n\n", $errors));
        $this->assertEmpty($errors, $message);
    }
}
