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
class Mage_Core_Model_Design_PackageMaterializationTest extends PHPUnit_Framework_TestCase
{
    protected static $_cssFiles = array(
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
    );

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_model;

    protected function setUp()
    {
        Mage::app()->getConfig()->getOptions()->setDesignDir(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'design'
        );
        $this->_model = new Mage_Core_Model_Design_Package();
        $this->_model->setDesignTheme('test/default/default', 'frontend');
    }

    protected function tearDown()
    {
        Varien_Io_File::rmdirRecursive($this->_getSkinDir());
        Varien_Io_File::rmdirRecursive($this->_getMatFixtureDir());
    }

    /**
     * Return path to skin directory of Magento installation
     *
     * @return string
     */
    protected function _getSkinDir()
    {
        return Mage::app()->getConfig()->getOptions()->getMediaDir() . '/skin';
    }

    /**
     * @param string $skinFile
     * @param array $designParams
     * @param string $expectedFilename
     * @param string $locale locale code or null for default locale
     * @dataProvider getSkinFileDataProvider
     */
    public function testGetSkinFile($skinFile, array $designParams, $expectedFilename, $locale = null)
    {
        Mage::app()->getLocale()->setLocale($locale);
        $expectedFilename = str_replace('/', DIRECTORY_SEPARATOR, $expectedFilename);
        $designParams += array('_package' => 'package', '_theme' => 'custom_theme');

        $actualFilename = $this->_model->getSkinFile($skinFile, $designParams);
        $this->assertStringMatchesFormat($expectedFilename, $actualFilename);

        $this->assertFileExists($actualFilename);
    }

    public function getSkinFileDataProvider()
    {
        $prefix = '%s/design/frontend/package/';
        return array(
            'skin file inside theme' => array(
                'fixture_script.js',
                array('_skin' => 'theme_nested_skin'),
                $prefix . 'custom_theme/skin/theme_nested_skin/fixture_script.js',
            ),
            'localized skin file inside theme' => array(
                'fixture_script.js',
                array('_skin' => 'theme_nested_skin'),
                $prefix . 'custom_theme/skin/theme_nested_skin/locale/ru_RU/fixture_script.js',
                'ru_RU',
            ),
            'modular skin file inside theme' => array(
                'fixture_script.js',
                array('_skin' => 'theme_nested_skin', '_module' => 'Fixture_Module'),
                $prefix . 'custom_theme/skin/theme_nested_skin/Fixture_Module/fixture_script.js',
            ),
            'localized modular skin file inside theme' => array(
                'fixture_script.js',
                array('_skin' => 'theme_nested_skin', '_module' => 'Fixture_Module'),
                $prefix . 'custom_theme/skin/theme_nested_skin/locale/ru_RU/Fixture_Module/fixture_script.js',
                'ru_RU',
            ),
            'lib skin file inside theme' => array(
                'mage/jquery-no-conflict.js',
                array('_skin' => 'theme_nested_skin'),
                $prefix . 'custom_theme/skin/theme_nested_skin/mage/jquery-no-conflict.js',
            ),
            'primary theme fallback - same theme & default skin' => array(
                'fixture_script_two.js',
                array('_skin' => 'theme_nested_skin'),
                $prefix . 'custom_theme/skin/default/fixture_script_two.js',
            ),
            'secondary theme fallback - default theme & same skin' => array(
                'fixture_script_three.js',
                array('_skin' => 'theme_nested_skin'),
                $prefix . 'default/skin/theme_nested_skin/fixture_script_three.js',
            ),
            'final theme fallback - default theme & default skin' => array(
                'fixture_script_four.js',
                array('_skin' => 'theme_nested_skin'),
                $prefix . 'default/skin/default/fixture_script_four.js',
            ),
            'lib fallback' => array(
                'mage/jquery-no-conflict.js',
                array('_skin' => 'default'),
                '%s/pub/js/mage/jquery-no-conflict.js',
            ),
        );
    }

    /**
     * @dataProvider getSkinUrlDataProvider
     */
    public function testGetSkinUrl($file, $expectedName, $locale = null)
    {
        Mage::app()->getLocale()->setLocale($locale);
        $url = $this->_model->getSkinUrl($file);
        $this->assertStringEndsWith($expectedName, $url);
        $skinFile = $this->_model->getSkinFile($file);
        $this->assertFileExists($skinFile);
    }

    /**
     * @return array
     */
    public function getSkinUrlDataProvider()
    {
        return array(
            'theme file' => array(
                'css/styles.css',
                'skin/frontend/test/default/default/en_US/css/styles.css',
            ),
            'theme localized file' => array(
                'logo.gif',
                'skin/frontend/test/default/default/fr_FR/logo.gif',
                'fr_FR',
            ),
            'modular file' => array(
                'Module::favicon.ico',
                'skin/frontend/test/default/default/en_US/Module/favicon.ico',
            ),
            'lib file' => array(
                'varien/product.js',
                'http://localhost/pub/js/varien/product.js',
            ),
            'lib folder' => array(
                'varien',
                'http://localhost/pub/js/varien',
            )
        );
    }

    /**
     * @magentoConfigFixture default/design/theme/allow_skin_files_duplication 0
     * @dataProvider testGetSkinUrlNoFilesDuplicationDataProvider
     */
    public function testGetSkinUrlNoFilesDuplication($file, $expectedName, $locale = null)
    {
        $this->testGetSkinUrl($file, $expectedName, $locale);
    }

    /**
     * @return array
     */
    public function testGetSkinUrlNoFilesDuplicationDataProvider()
    {
        return array(
            'theme css file' => array(
                'css/styles.css',
                'skin/frontend/test/default/default/en_US/css/styles.css',
            ),
            'theme file' => array(
                'images/logo.gif',
                'skin/frontend/test/default/skin/default/images/logo.gif',
            ),
            'theme localized file' => array(
                'logo.gif',
                'skin/frontend/test/default/skin/default/locale/fr_FR/logo.gif',
                'fr_FR',
            )
        );
    }

    /**
     * @magentoConfigFixture default/design/theme/allow_skin_files_duplication 0
     */
    public function testGetSkinUrlNoFilesDuplicationWithCaching()
    {
        Mage::app()->getLocale()->setLocale('en_US');
        $skinParams = array('_package' => 'test', '_theme' => 'default', '_skin' => 'default');
        $cacheKey = 'frontend/test/default/default/en_US';
        Mage::app()->cleanCache();

        $skinFile = 'images/logo.gif';
        $this->_model->getSkinUrl($skinFile, $skinParams);
        $map = unserialize(Mage::app()->loadCache($cacheKey));
        $this->assertTrue(count($map) == 1);
        $this->assertStringEndsWith('logo.gif', (string)array_pop($map));

        $skinFile = 'images/logo_email.gif';
        $this->_model->getSkinUrl($skinFile, $skinParams);
        $map = unserialize(Mage::app()->loadCache($cacheKey));
        $this->assertTrue(count($map) == 2);
        $this->assertStringEndsWith('logo_email.gif', (string)array_pop($map));
    }

    /**
     * @param string $file
     * @expectedException Magento_Exception
     * @dataProvider getSkinUrlDataExceptionProvider
     */
    public function testGetSkinUrlException($file)
    {
        $this->_model->getSkinUrl($file);
    }

    /**
     * @return array
     */
    public function getSkinUrlDataExceptionProvider()
    {
        return array(
            'non-existing theme file'  => array('path/to/nonexisting-file.ext'),
            'non-existing module file' => array('Some_Module::path/to/nonexisting-file.ext'),
        );
    }


    /**
     * @param string $file
     * @expectedException Magento_Exception
     * @dataProvider findFileExceptionDataProvider
     */
    public function testFindFileException($file)
    {
        $this->_model->getTemplateFilename($file);
    }

    public function findFileExceptionDataProvider()
    {
        return array(
            array('::no_scope.ext'),
            array('./file.ext'),
            array('../file.ext'),
            array('dir/./file.ext'),
            array('dir/../file.ext'),
        );
    }

    /**
     * Materialization of skin files in development mode
     *
     * @param string $application
     * @param string $package
     * @param string $theme
     * @param string $skin
     * @param string $file
     * @dataProvider materializeSkinFileDataProvider
     */
    public function testMaterializeSkinFile($application, $package, $theme, $skin, $file)
    {
        // determine path where a materialized file is to be expected
        $expectedModule = false;
        $targetFile   = $file;
        if (false !== strpos($file, '::')) {
            $targetFile = explode('::', $file);
            $expectedModule = $targetFile[0];
            $targetFile = $targetFile[1];
        }
        $path = array(Mage::getBaseDir('media'), 'skin', $application, $package, $theme, $skin, 'en_US');
        if ($expectedModule) {
            $path[] = $expectedModule;
        }
        $path[] = $targetFile;
        $targetFile = implode(DIRECTORY_SEPARATOR, $path);

        // test doesn't make sense if the original file doesn't exist or the target file already exists
        $params = array(
            '_area'    => $application,
            '_package' => $package,
            '_theme'   => $theme,
            '_skin'    => $skin,
        );
        $originalFile = $this->_model->getSkinFile($file, $params);
        $this->assertFileExists($originalFile);

        // getSkinUrl() will trigger materialization in development mode
        $this->assertFileNotExists($targetFile, 'Please verify isolation from previous test(s).');
        $this->_model->getSkinUrl($file, $params);
        $this->assertFileExists($targetFile);

        // as soon as the files are materialized, they must have the same mtime as originals
        $this->assertEquals(filemtime($originalFile), filemtime($targetFile),
            "These files mtime must be equal: {$originalFile} / {$targetFile}"
        );
    }

    /**
     * @return array
     */
    public function materializeSkinFileDataProvider()
    {
        return array(
            array('frontend', 'test', 'default', 'default', 'images/logo_email.gif'),
            array('frontend', 'test', 'default', 'default', 'Mage_Page::favicon.ico'),
        );
    }

    /**
     * Materialization of CSS files located in the theme (development mode)
     */
    public function testMaterializeCssFileFromTheme()
    {
        $materializedDir = $this->_getSkinDir() . '/frontend/package/default/theme/en_US';
        $this->assertFileNotExists($materializedDir, 'Please verify isolation from previous test(s).');
        $this->_model->getSkinUrl('css/file.css', array(
            '_package' => 'package',
            '_skin' => 'theme',
        ));
        foreach (self::$_cssFiles as $file) {
            $this->assertFileExists("{$materializedDir}/{$file}");
        }
        $this->assertFileNotExists("{$materializedDir}/absolute.gif");
        $this->assertFileNotExists(dirname($materializedDir) . '/access_violation.php');
    }

    /**
     * Materialization of CSS files located in the module
     * @dataProvider materializeCssFileFromModuleDataProvider
     */
    public function testMaterializeCssFileFromModule(
        $cssSkinFile, $designParams, $expectedCssFile, $expectedCssContent, $expectedRelatedFiles
    ) {
        $baseDir = $this->_getSkinDir();
        $this->_model->getSkinUrl($cssSkinFile, $designParams);

        $expectedCssFile = $baseDir . '/' . $expectedCssFile;
        $this->assertFileExists($expectedCssFile);
        $actualCssContent = file_get_contents($expectedCssFile);

        $this->assertNotRegExp(
            '/url\(.*?' . Mage_Core_Model_Design_Package::SCOPE_SEPARATOR . '.*?\)/',
            $actualCssContent,
            'Materialized CSS file must not contain scope separators in URLs.'
        );

        foreach ($expectedCssContent as $expectedCssSubstring) {
            $this->assertContains($expectedCssSubstring, $actualCssContent);
        }

        foreach ($expectedRelatedFiles as $expectedFile) {
            $expectedFile = $baseDir . '/' . $expectedFile;
            $this->assertFileExists($expectedFile);
        }
    }

    public function materializeCssFileFromModuleDataProvider()
    {
        return array(
            'frontend' => array(
                'widgets.css',
                array(
                    '_area'    => 'frontend',
                    '_package' => 'default',
                    '_skin'    => 'default',
                    '_module'  => 'Mage_Reports',
                ),
                'frontend/default/default/default/en_US/Mage_Reports/widgets.css',
                array(
                    'url(../Mage_Catalog/images/i_block-list.gif)',
                ),
                array(
                    'frontend/default/default/default/en_US/Mage_Catalog/images/i_block-list.gif',
                ),
            ),
            'adminhtml' => array(
                'Mage_Paypal::boxes.css',
                array(
                    '_area'    => 'adminhtml',
                    '_package' => 'package',
                    '_theme'   => 'test',
                    '_skin'    => 'default',
                    '_module'  => false,
                ),
                'adminhtml/package/test/default/en_US/Mage_Paypal/boxes.css',
                array(
                    'url(logo.gif)',
                    'url(section.png)',
                ),
                array(
                    'adminhtml/package/test/default/en_US/Mage_Paypal/logo.gif',
                    'adminhtml/package/test/default/en_US/Mage_Paypal/section.png',
                ),
            ),
        );
    }


    /**
     * Test that modified CSS file and changed resources are re-materialized
     */
    public function testMaterializeResourcesAndCssWhenChangedCss()
    {
        $mTime = time();
        $this->_prepareMatCssFixture($mTime - 10);

        $this->_model->getSkinUrl('mat_style.css', $this->_getMatDesignParams());
        $this->_assertFilesMaterializedWell(array(
            'mat_style.css', 'mat_sub.css', 'images/square.png', 'images/hrectangle.png'
        ));

        $changedFiles = array(
            'mat_style_after.css' => 'mat_style.css',
            'mat_sub_after.css' => 'mat_sub.css',
            'images_after/square.png' => 'images/square.png',
            'images_after/vrectangle.png' => 'images/vrectangle.png'
        );
        $this->_copyFilesToFixtureDir($changedFiles, $mTime);

        $this->_model->getSkinUrl('mat_style.css', $this->_getMatDesignParams());
        $this->_assertFilesMaterializedWell($changedFiles);
    }

    /**
     * Test changed resources, referenced in non-modified CSS file, are re-materialized
     */
    public function testMaterializeChangedResourcesWhenUnchangedCss()
    {
        $mTime = time();
        $this->_prepareMatCssFixture($mTime - 10);

        $this->_model->getSkinUrl('mat_style.css', $this->_getMatDesignParams());
        $this->_assertFilesMaterializedWell(array(
            'mat_style.css', 'mat_sub.css', 'images/square.png'
        ));

        $changedFiles = array(
            'mat_sub_after.css' => 'mat_sub.css',
            'images_after/square.png' => 'images/square.png'
        );
        $this->_copyFilesToFixtureDir($changedFiles, $mTime);

        $this->_model->getSkinUrl('mat_style.css', $this->_getMatDesignParams());
        $this->_assertFilesMaterializedWell($changedFiles);
    }

    /**
     * Prepare design directory with initial css and resources.
     *
     * @param int $mTime
     * @return Mage_Core_Model_Design_PackageMaterializationTest
     */
    protected function _prepareMatCssFixture($mTime)
    {
        Mage::app()->getConfig()->getOptions()->setDesignDir($this->_getMatFixtureDir());
        mkdir($this->_getMatFixtureSkinPath() . '/images', 0777, true);
        $this->_copyFilesToFixtureDir(
            array(
                'mat_style_before.css' => 'mat_style.css',
                'mat_sub_before.css' => 'mat_sub.css',
                'images_before/square.png' => 'images/square.png',
                'images_before/hrectangle.png' => 'images/hrectangle.png'
            ),
            $mTime
        );

        return $this;
    }

    /**
     * Return design parameters for materialization
     *
     * @return array
     */
    protected function _getMatDesignParams()
    {
        return array(
            '_area'    => 'frontend',
            '_package' => 'default',
            '_theme' => 'default',
            '_skin'    => 'default',
            '_module'  => false
        );
    }

    /**
     * Assert that files are materialized and are equal to their originals.
     *
     * Indexes in $file array can be names of original files (then the files are checked for content equality) or just
     * usual numeric keys (then only existence of materialized files is checked).
     *
     * @param array $files
     * @return Mage_Core_Model_Design_PackageMaterializationTest
     */
    protected function _assertFilesMaterializedWell($files)
    {
        $materializedPath = $this->_getMatSkinPath();
        $sourcePath = $this->_getMatSourcePath();
        foreach ($files as $sourceFile => $materializedFile) {
            $this->assertFileExists($materializedPath . $materializedFile);
            if (!is_numeric($sourceFile)) {
                $this->assertFileEquals($sourcePath . $sourceFile, $materializedPath . $materializedFile);
            }
        }
        return $this;
    }

    /**
     * Copy required files to fixture skin directory
     *
     * @param array $files
     * @param int $mTime
     * @return Mage_Core_Model_Design_PackageMaterializationTest
     */
    protected function _copyFilesToFixtureDir($files, $mTime)
    {
        $sourcePath = $this->_getMatSourcePath();
        $fixtureSkinPath = $this->_getMatFixtureSkinPath();
        foreach ($files as $fileFrom => $fileTo) {
            copy($sourcePath . $fileFrom, $fixtureSkinPath . $fileTo);
            touch($fixtureSkinPath . $fileTo, $mTime);
        }
        return $this;
    }

    /**
     * Return path to source directory, where original files for materialization tests are located
     *
     * @return string
     */
    protected function _getMatSourcePath()
    {
        return dirname(__DIR__) . '/_files/materialization/';
    }

    /**
     * Return path to temp directory, where fixture design will be created
     *
     * @return string
     */
    protected function _getMatFixtureDir()
    {
        return Magento_Test_Bootstrap::getInstance()->getTmpDir() . '/materialization';
    }

    /**
     * Return path to fixture skin directory, where fixture design resource are located for current test
     *
     * @return string
     */
    protected function _getMatFixtureSkinPath()
    {
        return $this->_getMatFixtureDir() . '/frontend/default/default/skin/default/';
    }

    /**
     * Return path to skin directory, where files are materialized
     *
     * @return string
     */
    protected function _getMatSkinPath()
    {
        return $this->_getSkinDir() . '/frontend/default/default/default/en_US/';
    }
}
