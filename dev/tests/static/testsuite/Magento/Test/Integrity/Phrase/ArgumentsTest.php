<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

/**
 * Scan source code for detects invocations of __() function, analyzes placeholders with arguments
 * and see if they not equal
 */
namespace Magento\Test\Integrity\Phrase;

use Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer;
use Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\PhraseCollector;

class ArgumentsTest extends \Magento\Test\Integrity\Phrase\AbstractTestCase
{
    /**
     * @var \Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\PhraseCollector
     */
    protected $_phraseCollector;

    /**
     * List of files that must be omitted
     *
     * @todo remove blacklist related logic when all files correspond to the standard
     * @var array
     */
    protected $blackList;

    protected function setUp()
    {
        $this->_phraseCollector = new \Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\PhraseCollector(
            new \Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer()
        );

        $rootDir = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        $this->blackList = array(
            // the file below is the only file where strings are translated without corresponding arguments
            $rootDir . str_replace('/', DIRECTORY_SEPARATOR, '/app/code/Magento/Core/Helper/Js.php')
        );
    }

    public function testArguments()
    {
        $errors = array();
        foreach ($this->_getFiles() as $file) {
            if (in_array($file, $this->blackList)) {
                continue;
            }
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
            sprintf(
                "\n%d usages of inconsistency the number of arguments and placeholders were discovered: \n%s",
                count($errors),
                implode("\n\n", $errors)
            )
        );
    }
}
