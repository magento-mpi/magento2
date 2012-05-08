<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_PackageMergingTest extends PHPUnit_Framework_TestCase
{
    /**
     * Path to the public directory for skin files
     *
     * @var string
     */
    protected static $_skinPublicDir;

    /**
     * Path to the public directory for merged skin files
     *
     * @var string
     */
    protected static $_skinPublicMergedDir;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_model = null;

    public static function setUpBeforeClass()
    {
        self::$_skinPublicDir = Mage::app()->getConfig()->getOptions()->getMediaDir() . '/skin';
        self::$_skinPublicMergedDir = self::$_skinPublicDir . '/_merged';
    }

    protected function setUp()
    {
        Mage::app()->getConfig()->getOptions()->setDesignDir(dirname(__DIR__) . '/_files/design');

        $this->_model = new Mage_Core_Model_Design_Package();
        $this->_model->setDesignTheme('package/default/theme', 'frontend');
    }

    protected function tearDown()
    {
        Varien_Io_File::rmdirRecursive(self::$_skinPublicDir);
    }

    /**
     * @magentoConfigFixture current_store dev/css/merge_css_files 1
     * @expectedException Magento_Exception
     */
    public function testMergeFilesException()
    {
        $this->_model->getOptimalCssUrls(array(
            'css/exception.css',
            'css/file.css',
        ));
    }

    /**
     * @param string $contentType
     * @param array $files
     * @param string $expectedFilename
     * @param array $related
     * @dataProvider mergeFilesDataProvider
     * @magentoConfigFixture current_store dev/css/merge_css_files 1
     * @magentoConfigFixture current_store dev/js/merge_files 1
     * @magentoAppIsolation enabled
     */
    public function testMergeFiles($contentType, $files, $expectedFilename, $related = array())
    {
        if ($contentType == Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS) {
            $result = $this->_model->getOptimalCssUrls($files);
        } else {
            $result = $this->_model->getOptimalJsUrls($files);
        }
        $this->assertArrayHasKey(0, $result);
        $this->assertEquals(1, count($result), 'Result must contain exactly one file.');
        $this->assertEquals($expectedFilename, basename($result[0]));
        foreach ($related as $file) {
            $this->assertFileExists(
                self::$_skinPublicDir . '/frontend/package/default/theme/en_US/' . $file
            );
        }
    }

    /**
     * @return array
     */
    public function mergeFilesDataProvider()
    {
        return array(
            array(
                Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS,
                array(
                    'calendar/calendar-blue.css',
                    'css/file.css',
                ),
                'ba1ea83ef061c58d4ceef66018beb4f2.css',
                array(
                    'css/file.css',
                    'recursive.css',
                    'recursive.gif',
                    'css/deep/recursive.css',
                    'recursive2.gif',
                    'css/body.gif',
                    'css/1.gif',
                    'h1.gif',
                    'images/h2.gif',
                    'Namespace_Module/absolute_valid_module.gif',
                    'Mage_Page/favicon.ico', // non-fixture file from real module
                ),
            ),
            array(
                Mage_Core_Model_Design_Package::CONTENT_TYPE_JS,
                array(
                    'calendar/calendar.js',
                    'scripts.js',
                ),
                '916b1b8161a8f61422b432009f47f267.js',
            ),
        );
    }

    /**
     * @magentoConfigFixture current_store dev/js/merge_files 1
     * @magentoAppIsolation enabled
     */
    public function testMergeFilesModification()
    {
        $files = array(
            'calendar/calendar.js',
            'scripts.js',
        );

        $resultingFile = self::$_skinPublicMergedDir . '/916b1b8161a8f61422b432009f47f267.js';
        $this->assertFileNotExists($resultingFile);

        // merge first time
        $this->_model->getOptimalJsUrls($files);
        $this->assertFileExists($resultingFile);

    }

    /**
     * @magentoConfigFixture current_store dev/js/merge_files 1
     * @magentoAppIsolation enabled
     */
    public function testCleanMergedJsCss()
    {
        $this->assertFileNotExists(self::$_skinPublicMergedDir);

        $this->_model->getOptimalJsUrls(array(
            'calendar/calendar.js',
            'scripts.js',
        ));
        $this->assertFileExists(self::$_skinPublicMergedDir);
        $filesFound = false;
        foreach (new RecursiveDirectoryIterator(self::$_skinPublicMergedDir) as $fileInfo) {
            if ($fileInfo->isFile()) {
                $filesFound = true;
                break;
            }
        }
        $this->assertTrue($filesFound, 'No files found in the merged directory.');

        $this->_model->cleanMergedJsCss();
        $this->assertFileNotExists(self::$_skinPublicMergedDir);
    }
}
