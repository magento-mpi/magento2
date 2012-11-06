<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Inspection_SanityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Inspection_Sanity
     */
    protected $_sanityChecker;

    protected function setUp()
    {
        $this->_sanityChecker = new Inspection_Sanity(__DIR__ . '/_files/config.xml', __DIR__ . '/_files/sanity');
    }

    protected function tearDown()
    {
        $this->_sanityChecker = null;
    }

    /**
     * @param string $configFile
     * @param string $baseDir
     * @expectedException Inspection_Exception
     * @dataProvider constructorExceptionDataProvider
     */
    public function testConstructorException($configFile, $baseDir)
    {
        new Inspection_Sanity($configFile, $baseDir);
    }

    public function constructorExceptionDataProvider()
    {
        $fixturePath = __DIR__ . '/_files/';
        return array(
            'non-existing config file' => array(
                $fixturePath . 'non-existing.xml',
                $fixturePath
            ),
            'non-existing base dir' => array(
                $fixturePath . 'config.xml',
                $fixturePath . 'non-existing-dir'
            ),
            'broken config' => array(
                $fixturePath . 'broken_config.xml',
                $fixturePath
            ),
            'empty config' => array(
                $fixturePath . 'empty_config.xml',
                $fixturePath
            ),
            'empty whitelisted path' => array(
                $fixturePath . 'empty_whitelisted_path.xml',
                $fixturePath
            ),
        );
    }

    public function testGetWords()
    {
        $expected = array('demon', 'vampire');
        $actual = $this->_sanityChecker->getWords();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param string $file
     * @param bool $checkContents
     * @param array $expected
     * @dataProvider findWordsDataProvider
     */
    public function testFindWords($file, $checkContents, $expected)
    {
        $actual = $this->_sanityChecker->findWords($file, $checkContents);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function findWordsDataProvider()
    {
        $basePath = __DIR__ . '/_files/sanity/';
        return array(
            'usual file' => array(
                $basePath . 'buffy.php',
                true,
                array('demon', 'vampire'),
            ),
            'usual file no contents checked' => array(
                $basePath . 'buffy.php',
                false,
                array(),
            ),
            'whitelisted file' => array(
                $basePath . 'twilight/eclipse.php',
                true,
                array(),
            ),
            'partially whitelisted file' => array(
                $basePath . 'twilight/newmoon.php',
                true,
                array('demon')
            ),
            'filename with bad word' => array(
                $basePath . 'interview_with_the_vampire.php',
                true,
                array('vampire')
            ),
        );
    }
}
