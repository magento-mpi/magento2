<?php
/**
 * Scan source code for detects invocations of outdated __() method
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Phrase_Legacy_SignatureTest extends PHPUnit_Framework_TestCase
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
        $path = Utility_Files::init()->getPathToSource() . '/app/';
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach (new RegexIterator($files, '/\.(php|phtml)$/') as $file) {
            $this->_phraseCollector->parse($file);
            foreach ($this->_phraseCollector->getPhrases() as $phrase) {
                $errors[] = "\nPhrase: " . $phrase['phrase'] .
                    "\nFile: " . $phrase['file'] .
                    "\nLine: " . $phrase['line'];
            }
        }
        $message = sprintf('%d usages of the old translation method call were discovered: %s',
            count($errors), "\n" );
        $this->assertEmpty($errors, $message);
    }
}
