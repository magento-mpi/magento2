<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

/**
 * Abstract class for phrase testing
 */
namespace Magento\Test\Integrity\Phrase;

use Magento\Tools\I18n\FilesCollector;

class AbstractTestCase extends \PHPUnit_Framework_TestCase
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
     * @return \RegexIterator
     */
    protected function _getFiles()
    {
        $filesCollector = new \Magento\Tools\I18n\FilesCollector();

        return $filesCollector->getFiles(
            array(\Magento\TestFramework\Utility\Files::init()->getPathToSource()),
            '/\.(php|phtml)$/'
        );
    }
}
