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
class Legacy_SanityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Inspection_Sanity
     */
    protected static $_sanityChecker;

    public static function setUpBeforeClass()
    {
        self::$_sanityChecker = new Inspection_Sanity(
            __DIR__ . '/_files/sanity_ce.xml',
            Utility_Files::init()->getPathToSource()
        );
    }

    /**
     * @param string $file
     * @dataProvider sanityDataProvider
     */
    public function testSanity($file)
    {
        $isBinaryFile = preg_match('/\.(jpg|png|gif|swf|avi|mov|flv|jar)$/', $file);
        $words = self::$_sanityChecker->findWords($file, !$isBinaryFile);
        if ($words) {
            $this->fail('Found words: ' . implode(', ', $words));
        }
    }

    /**
     * @return array
     */
    public function sanityDataProvider()
    {
        return Utility_Files::init()->getAllFiles();
    }
}
