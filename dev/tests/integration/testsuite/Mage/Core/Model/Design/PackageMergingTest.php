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
        self::$_skinPublicDir = Mage::app()->getConfig()->getOptions()->getMediaDir() . '/theme';
        self::$_skinPublicMergedDir = self::$_skinPublicDir . '/_merged';
    }

    protected function setUp()
    {
        Mage::app()->getConfig()->getOptions()->setDesignDir(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'design'
        );

        $this->_model = Mage::getModel('Mage_Core_Model_Design_Package');
        $this->_model->setDesignTheme('package/default', 'frontend');
    }

    protected function tearDown()
    {
        Varien_Io_File::rmdirRecursive(self::$_skinPublicDir);
        $this->_model = null;
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
     * @magentoConfigFixture current_store dev/static/sign 0
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
                self::$_skinPublicDir . '/frontend/package/default/en_US/' . $file
            );
        }
    }

    /**
     * @param string $contentType
     * @param array $files
     * @param string $expectedFilename
     * @param array $related
     * @dataProvider mergeFilesDataProvider
     * @magentoConfigFixture current_store dev/css/merge_css_files 1
     * @magentoConfigFixture current_store dev/js/merge_files 1
     * @magentoConfigFixture current_store dev/static/sign 1
     * @magentoAppIsolation enabled
     */
    public function testMergeFilesSigned($contentType, $files, $expectedFilename, $related = array())
    {
        if ($contentType == Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS) {
            $result = $this->_model->getOptimalCssUrls($files);
        } else {
            $result = $this->_model->getOptimalJsUrls($files);
        }
        $this->assertArrayHasKey(0, $result);
        $this->assertEquals(1, count($result), 'Result must contain exactly one file.');
        $mergedFileName = basename($result[0]);
        $mergedFileName = preg_replace('/\?.*$/i', '', $mergedFileName);
        $this->assertEquals($expectedFilename, $mergedFileName);
        $lastModified = array();
        preg_match('/.*\?(.*)$/i', $result[0], $lastModified);
        $this->assertArrayHasKey(1, $lastModified);
        $this->assertEquals(10, strlen($lastModified[1]));
        $this->assertLessThanOrEqual(time(), $lastModified[1]);
        $this->assertGreaterThan(1970, date('Y', $lastModified[1]));
        foreach ($related as $file) {
            $this->assertFileExists(
                self::$_skinPublicDir . '/frontend/package/default/en_US/' . $file
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
                    'mage/calendar.css',
                    'css/file.css',
                ),
                '16f3dae4a78f603c9afa37606b0f51e7.css',
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
                    'mage/calendar.js',
                    'scripts.js',
                ),
                'e97b02be13928ce72066d99a4b967d41.js',
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
            'mage/calendar.js',
            'scripts.js',
        );

        $resultingFile = self::$_skinPublicMergedDir . '/e97b02be13928ce72066d99a4b967d41.js';
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
            'mage/calendar.js',
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
