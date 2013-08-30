<?php
/**
 * Abstract class for phrase testing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Integrity_Phrase_AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $phrase
     * @return string
     */
    protected function _createPhraseErrorMessage($phrase)
    {
        return "\nPhrase: {$phrase['phrase']} \nFile: {$phrase['file']} \nLine: {$phrase['line']}";
    }

    /**
     * @return RegexIterator
     */
    protected function _getFiles()
    {
        $path = Utility_Files::init()->getPathToSource() . '/app/';
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        return new RegexIterator($files, '/\.(php|phtml)$/');
    }
}
