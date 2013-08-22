<?php
/**
 * Absctract class for phrase testing
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Magento_Test_Integrity_Phrase_TestAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * File mask
     */
    const FILES_MASK = '/\.(php|phtml)$/';

    /**
     * @var Magento_Tokenizer_PhraseCollector
     */
    protected $_phraseCollector;

    /**
     * @var array
     */
    protected $_errors = array();

    /**
     * Test case
     */
    abstract public function testCase();

    protected function setUp()
    {
        $this->_phraseCollector = new Magento_Tokenizer_PhraseCollector();
    }

    /**
     * Prepare error message
     *
     * @param string $errorMessage
     * @param array $errors
     * @return string
     */
    protected function _prepareErrorMessage($errorMessage, $errors)
    {
        $errorsAmount = count($errors);
        return sprintf("\n" . $errorMessage, $errorsAmount, "\n" . implode("\n\n", $errors));
    }

    /**
     * Get files for scan
     *
     * @return array
     */
    protected function _getFiles()
    {
        $path = Utility_Files::init()->getPathToSource() . '/app/';
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        return new RegexIterator($files, self::FILES_MASK);
    }

    /**
     * Add error
     *
     * @param array $phrase
     */
    protected function addError(array $phrase)
    {
        $this->_errors[] = "\nPhrase: " . $phrase['phrase'] .
            "\nFile: " . $phrase['file'] .
            "\nLine: " . $phrase['line'];
    }
}
