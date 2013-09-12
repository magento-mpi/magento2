<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

use Magento\Tools\I18n\Code\FilesCollector;

/**
 * Abstract class for phrase testing
 */
class Magento_Test_Integrity_Phrase_AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $phrase
     * @return string
     */
    protected function _createPhraseError($phrase)
    {
        return "\nPhrase: {$phrase['phrase']} \nFile: {$phrase['file']} \nLine: {$phrase['line']}";
    }

    /**
     * @return RegexIterator
     */
    protected function _getFiles()
    {
        $filesCollector = new FilesCollector();

        return $filesCollector->getFiles(array(Magento_TestFramework_Utility_Files::init()->getPathToSource()),
            '/\.(php|phtml)$/');
    }
}
