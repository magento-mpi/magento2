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

use Magento\Tools\I18n\Code\FilesCollector;

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
        $filesCollector = new \Magento\Tools\I18n\Code\FilesCollector();

        return $filesCollector->getFiles(
            array(\Magento\Framework\Test\Utility\Files::init()->getPathToSource()),
            '/\.(php|phtml)$/'
        );
    }
}
