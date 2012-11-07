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
            'empty whitelisted path' => array(
                $fixturePath . 'empty_whitelisted_path.xml',
                $fixturePath
            ),
        );
    }

    public function testGetWords()
    {
        $sanityChecker = new Inspection_Sanity(__DIR__ . '/_files/config.xml', __DIR__ . '/_files/sanity');
        $actual = $sanityChecker->getWords();
        $expected = array('demon', 'vampire');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param string|array $configFiles
     * @param string $file
     * @param bool $checkContents
     * @param array $expected
     * @dataProvider findWordsDataProvider
     */
    public function testFindWords($configFiles, $file, $checkContents, $expected)
    {
        $sanityChecker = new Inspection_Sanity($configFiles, __DIR__ . '/_files/sanity');
        $actual = $sanityChecker->findWords($file, $checkContents);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function findWordsDataProvider()
    {
        $mainConfig = __DIR__ . '/_files/config.xml';
        $additionalConfig = __DIR__ . '/_files/config_additional.xml';
        $basePath = __DIR__ . '/_files/sanity/';
        return array(
            'usual file' => array(
                $mainConfig,
                $basePath . 'buffy.php',
                true,
                array('demon', 'vampire'),
            ),
            'usual file no contents checked' => array(
                $mainConfig,
                $basePath . 'buffy.php',
                false,
                array(),
            ),
            'whitelisted file' => array(
                $mainConfig,
                $basePath . 'twilight/eclipse.php',
                true,
                array(),
            ),
            'partially whitelisted file' => array(
                $mainConfig,
                $basePath . 'twilight/newmoon.php',
                true,
                array('demon')
            ),
            'filename with bad word' => array(
                $mainConfig,
                $basePath . 'interview_with_the_vampire.php',
                true,
                array('vampire')
            ),
            'words in multiple configs' => array(
                array(
                    $mainConfig,
                    $additionalConfig,
                ),
                $basePath . 'buffy.php',
                true,
                array('demon', 'vampire', 'darkness')
            ),
            'whitelisted paths in multiple configs' => array(
                array(
                    $mainConfig,
                    $additionalConfig,
                ),
                $basePath . 'twilight/newmoon.php',
                true,
                array('demon')
            ),
        );
    }
}
