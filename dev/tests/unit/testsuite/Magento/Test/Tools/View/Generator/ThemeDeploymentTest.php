<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/Magento/Tools/View/Generator/ThemeDeployment.php';

class Magento_Test_Tools_View_Generator_ThemeDeploymentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Helper_Css
     */
    protected $_cssHelper;

    /**
     * @var string
     */
    protected $_tmpDir;

    protected function setUp()
    {
        $filesystem =  new Magento_Filesystem(new Magento_Filesystem_Adapter_Local());
        $dirs = new Magento_Core_Model_Dir($filesystem->normalizePath(__DIR__ . '/../../../../../../'));
        $this->_cssHelper = new Magento_Core_Helper_Css($filesystem, $dirs);
        $this->_tmpDir = TESTS_TEMP_DIR . DIRECTORY_SEPARATOR . 'tool_theme_deployment';
        mkdir($this->_tmpDir);
    }

    protected function tearDown()
    {
        Magento_Io_File::rmdirRecursive($this->_tmpDir);
    }

    /**
     * @param string $permitted
     * @param string $forbidden
     * @param string $exceptionMessage
     * @dataProvider constructorExceptionDataProvider
     */
    public function testConstructorException($permitted, $forbidden, $exceptionMessage)
    {
        $this->setExpectedException('Magento_Exception', $exceptionMessage);
        new Magento_tools_View_Generator_ThemeDeployment($this->_cssHelper, $this->_tmpDir, $permitted, $forbidden);
    }

    public static function constructorExceptionDataProvider()
    {
        $conflictPermitted = __DIR__ . '/_files/ThemeDeployment/constructor_exception/permitted.php';
        $conflictForbidden = __DIR__ . '/_files/ThemeDeployment/constructor_exception/forbidden.php';
        return array(
            'no permitted config' => array(
                'non_existing_config.txt',
                $conflictForbidden,
                'Config file does not exist: non_existing_config.txt',
            ),
            'no forbidden config' => array(
                $conflictPermitted,
                'non_existing_config.txt',
                'Config file does not exist: non_existing_config.txt',
            ),
            'config conflicts' => array(
                $conflictPermitted,
                $conflictForbidden,
                'Conflicts: the following extensions are added both to permitted and forbidden lists: ' .
                    'conflict1, conflict2',
            ),
        );
    }

    public function testRun()
    {
        $permitted = __DIR__ . '/_files/ThemeDeployment/run/permitted.php';
        $forbidden = __DIR__ . '/_files/ThemeDeployment/run/forbidden.php';
        $fixture = include __DIR__ . '/_files/ThemeDeployment/run/fixture.php';

        $object = new Magento_tools_View_Generator_ThemeDeployment($this->_cssHelper, $this->_tmpDir, $permitted,
            $forbidden);
        $object->run($fixture['copyRules']);

        // Verify expected paths
        $actualPaths = $this->_getRelativePaths($this->_tmpDir);
        $actualPaths = $this->_canonizePathArray($actualPaths);
        $expectedPaths = $this->_canonizePathArray($fixture['expectedRelPaths']);
        $this->assertEquals($expectedPaths, $actualPaths, "Actual result of copying is different from expected paths");

        // Verify content of files
        foreach ($fixture['expectedFileContent'] as $relFile => $expectedContent) {
            $actualContent = trim(file_get_contents($this->_tmpDir . '/' . $relFile));
            $this->assertEquals($expectedContent, $actualContent, "Actual content is wrong in file {$relFile}");
        }
    }

    /**
     * Recursively go through directory and compose relative paths of all its files
     *
     * @param string $dir
     * @return array
     */
    protected function _getRelativePaths($dir)
    {
        $dirLen = strlen($dir);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        $result = array();
        foreach ($files as $file) {
            $result[] = substr($file, $dirLen + 1);
        }
        return $result;
    }

    /**
     * Process $paths, sorting them and making system separator in them
     *
     * @param array $paths
     * @return array
     */
    protected function _canonizePathArray($paths)
    {
        rsort($paths, SORT_STRING);
        foreach ($paths as &$path) {
            $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        }
        return $paths;
    }

    public function testRunInDryRun()
    {
        $permitted = __DIR__ . '/_files/ThemeDeployment/run/permitted.php';
        $forbidden = __DIR__ . '/_files/ThemeDeployment/run/forbidden.php';
        $fixture = include __DIR__ . '/_files/ThemeDeployment/run/fixture.php';

        $object = new Magento_Tools_View_Generator_ThemeDeployment($this->_cssHelper, $this->_tmpDir, $permitted,
            $forbidden, true);
        $object->run($fixture['copyRules']);

        $actualPaths = $this->_getRelativePaths($this->_tmpDir);
        $this->assertEmpty($actualPaths, 'Nothing must be copied/created in dry-run mode');
    }


    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage The file extension "php" must be added either to the permitted or forbidden list
     */
    public function testRunWithUnknownExtension()
    {
        $permitted = __DIR__ . '/_files/ThemeDeployment/run/permitted.php';
        $forbidden = __DIR__ . '/_files/ThemeDeployment/run/forbidden_without_php.php';
        $fixture = include __DIR__ . '/_files/ThemeDeployment/run/fixture.php';

        $object = new Magento_tools_View_Generator_ThemeDeployment($this->_cssHelper, $this->_tmpDir, $permitted,
            $forbidden, true);
        $object->run($fixture['copyRules']);
    }

    public function testRunWithCasedExtension()
    {
        $permitted = __DIR__ . '/_files/ThemeDeployment/run/permitted_cased_js.php';

        $object = new Magento_tools_View_Generator_ThemeDeployment($this->_cssHelper, $this->_tmpDir, $permitted);
        $copyRules = array(
            array(
                'source' => __DIR__ . '/_files/ThemeDeployment/run/source_cased_js',
                'destinationContext' => array(
                    'area' => 'frontend',
                    'locale' => 'not_important',
                    'themePath' => 'theme_path',
                    'module' => null
                ),
            ),
        );
        $object->run($copyRules);
        $this->assertFileExists(
            $this->_tmpDir . '/frontend/theme_path/file.JS'
        );
    }
}
