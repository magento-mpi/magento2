<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

use Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer\PhraseCollector;
use Magento\Tools\I18n\Code\Parser\Adapter\Php\Tokenizer;

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

    protected function setUp()
    {
        $this->_phraseCollector = new PhraseCollector(new Tokenizer());
    }

    public function testArguments()
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
