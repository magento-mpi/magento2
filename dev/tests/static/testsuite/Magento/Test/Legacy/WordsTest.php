<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests, that perform search of words, that signal of obsolete code
 */
namespace Magento\Test\Legacy;

class WordsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Inspection\WordsFinder
     */
    protected static $_wordsFinder;

    public static function setUpBeforeClass()
    {
        self::$_wordsFinder = new \Magento\TestFramework\Inspection\WordsFinder(
            glob(__DIR__ . '/_files/words_*.xml'),
            \Magento\TestFramework\Utility\Files::init()->getPathToSource()
        );
    }

    /**
     * @param string $file
     * @dataProvider wordsDataProvider
     */
    public function testWords($file)
    {
        $words = self::$_wordsFinder->findWords($file);
        if ($words) {
            $this->fail("Found words: '" . implode("', '", $words) . "' in '$file' file");
        }
    }

    /**
     * @return array
     */
    public function wordsDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getAllFiles();
    }
}
