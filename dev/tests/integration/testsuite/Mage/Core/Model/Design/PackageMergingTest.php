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

/**
 * @group module:Mage_Core
 */
class Mage_Core_Model_Design_PackageMergingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_model = null;

    /**
     * @var string
     */
    protected $_skinFixture = '';

    /**
     * @var string
     */
    protected $_pubMerged = '';

    /**
     * @var string
     */
    protected $_pubLib = '';

    protected function setUp()
    {
        Mage::app()->getConfig()->getOptions()->setDesignDir(dirname(__DIR__) . '/_files/design');
        Varien_Io_File::rmdirRecursive(Mage::app()->getConfig()->getOptions()->getMediaDir() . '/skin');

        $this->_model = new Mage_Core_Model_Design_Package();
        $this->_model->setArea('frontend')
            ->setPackageName('package')
            ->setTheme('default')
            ->setSkin('theme');

        $pub = Mage::getBaseDir('media');
        $this->_pubMerged = "{$pub}/skin/_merged";
        $this->_pubLib = Mage::getBaseDir('js');;
        // emulate source skin
        $this->_skinFixture = dirname(__DIR__) . '/_files/skin';
    }

    /**
     * @magentoConfigFixture current_store dev/css/merge_css_files 1
     * @expectedException Exception
     */
    public function testMergeFilesException()
    {
        $this->_model->getOptimalCssUrls(array(
            'css/exception.css' => Mage_Core_Model_Design_Package::STATIC_TYPE_SKIN,
            'css/file.css' => Mage_Core_Model_Design_Package::STATIC_TYPE_SKIN,
        ));
    }

    /**
     * @param string $contentType
     * @param array $files
     * @param string $expectedFilename
     * @param string $expectedFixture
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
        if ($related) {
            foreach ($related as $file) {
                $this->assertFileExists(
                    Mage::getBaseDir('media') . "/skin/frontend/package/default/theme/en_US/{$file}"
                );
            }
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
                    'calendar/calendar-blue.css' => Mage_Core_Model_Design_Package::STATIC_TYPE_LIB,
                    'css/file.css' => Mage_Core_Model_Design_Package::STATIC_TYPE_SKIN,
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
                    'calendar/calendar.js' => Mage_Core_Model_Design_Package::STATIC_TYPE_LIB,
                    'scripts.js'  => Mage_Core_Model_Design_Package::STATIC_TYPE_SKIN,
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
            'calendar/calendar.js' => Mage_Core_Model_Design_Package::STATIC_TYPE_LIB,
            'scripts.js'  => Mage_Core_Model_Design_Package::STATIC_TYPE_SKIN,
        );

        $resultingFile = "{$this->_pubMerged}/916b1b8161a8f61422b432009f47f267.js";
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
        $this->assertFileNotExists($this->_pubMerged);

        $this->_model->getOptimalJsUrls(array(
            'calendar/calendar.js' => Mage_Core_Model_Design_Package::STATIC_TYPE_LIB,
            'scripts.js'  => Mage_Core_Model_Design_Package::STATIC_TYPE_SKIN,
        ));
        $this->assertFileExists($this->_pubMerged);
        $filesFound = false;
        foreach (new RecursiveDirectoryIterator($this->_pubMerged) as $fileInfo) {
            if ($fileInfo->isFile()) {
                $filesFound = true;
            }
        }
        $this->assertTrue($filesFound, 'No files found in the merged directory.');

        $this->_model->cleanMergedJsCss();
        $this->assertFileNotExists($this->_pubMerged);
    }
}
