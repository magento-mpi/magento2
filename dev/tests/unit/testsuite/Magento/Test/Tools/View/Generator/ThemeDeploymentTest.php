<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Tools\View\Generator;


require_once realpath(
    __DIR__ . '/../../../../../../../../'
) . '/tools/Magento/Tools/View/Generator/ThemeDeployment.php';
class ThemeDeploymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Url\CssResolver
     */
    protected $_cssUrlResolver;

    /**
     * @var string
     */
    protected $_tmpDir;

    /**
     * @var \Magento\App\Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var \Magento\Filesystem\Driver\File
     */
    protected $filesystemAdapter;

    protected function setUp()
    {
        $methods = array('getDirectoryWrite', 'getPath', '__wakeup');
        $this->filesystem = $this->getMock('Magento\App\Filesystem', $methods, array(), '', false);
        $this->filesystem->expects(
            $this->any()
        )->method(
            'getPath'
        )->with(
            \Magento\App\Filesystem::ROOT_DIR
        )->will(
            $this->returnValue(str_replace('\\', '/', BP))
        );

        $viewFilesystem = $this->getMock('Magento\View\Filesystem', array('normalizePath'), array(), '', false);
        $viewFilesystem->expects($this->any())->method('normalizePath')->will($this->returnArgument(0));

        $this->_cssUrlResolver = new \Magento\View\Url\CssResolver($this->filesystem, $viewFilesystem);
        $this->_tmpDir = TESTS_TEMP_DIR . '/tool_theme_deployment';

        $this->filesystemAdapter = new \Magento\Filesystem\Driver\File();
        $this->filesystemAdapter->createDirectory($this->_tmpDir, 0777);
    }

    protected function tearDown()
    {
        $this->filesystemAdapter->deleteDirectory($this->_tmpDir);
    }

    /**
     * @param string $permitted
     * @param string $forbidden
     * @param string $exceptionMessage
     * @dataProvider constructorExceptionDataProvider
     */
    public function testConstructorException($permitted, $forbidden, $exceptionMessage)
    {
        $this->setExpectedException('Magento\Exception', $exceptionMessage);
        $this->_createThemeDeployment($permitted, $forbidden);
    }

    public static function constructorExceptionDataProvider()
    {
        $conflictPermitted = __DIR__ . '/_files/ThemeDeployment/constructor_exception/permitted.php';
        $conflictForbidden = __DIR__ . '/_files/ThemeDeployment/constructor_exception/forbidden.php';
        return array(
            'no permitted config' => array(
                'non_existing_config.txt',
                $conflictForbidden,
                'Config file does not exist: non_existing_config.txt'
            ),
            'no forbidden config' => array(
                $conflictPermitted,
                'non_existing_config.txt',
                'Config file does not exist: non_existing_config.txt'
            ),
            'config conflicts' => array(
                $conflictPermitted,
                $conflictForbidden,
                'Conflicts: the following extensions are added both to permitted and forbidden lists: ' .
                'conflict1, conflict2'
            )
        );
    }

    public function testRun()
    {
        $permitted = __DIR__ . '/_files/ThemeDeployment/run/permitted.php';
        $forbidden = __DIR__ . '/_files/ThemeDeployment/run/forbidden.php';
        $fixture = include __DIR__ . '/_files/ThemeDeployment/run/fixture.php';

        $object = $this->_createThemeDeployment($permitted, $forbidden);
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
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
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
            $path = str_replace(array('/', '\\'), '/', $path);
        }
        return $paths;
    }

    public function testRunInDryRun()
    {
        $permitted = __DIR__ . '/_files/ThemeDeployment/run/permitted.php';
        $forbidden = __DIR__ . '/_files/ThemeDeployment/run/forbidden.php';
        $fixture = include __DIR__ . '/_files/ThemeDeployment/run/fixture.php';

        $object = $this->_createThemeDeployment($permitted, $forbidden, true);
        $object->run($fixture['copyRules']);

        $actualPaths = $this->_getRelativePaths($this->_tmpDir);
        $this->assertEmpty($actualPaths, 'Nothing must be copied/created in dry-run mode');
    }

    /**
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage The file extension "php" must be added either to the permitted or forbidden list
     */
    public function testRunWithUnknownExtension()
    {
        $permitted = __DIR__ . '/_files/ThemeDeployment/run/permitted.php';
        $forbidden = __DIR__ . '/_files/ThemeDeployment/run/forbidden_without_php.php';
        $fixture = include __DIR__ . '/_files/ThemeDeployment/run/fixture.php';

        $object = $this->_createThemeDeployment($permitted, $forbidden, true);
        $object->run($fixture['copyRules']);
    }

    public function testRunWithCasedExtension()
    {
        $permitted = __DIR__ . '/_files/ThemeDeployment/run/permitted_cased_js.php';

        $object = $this->_createThemeDeployment($permitted);
        $copyRules = array(
            array(
                'source' => __DIR__ . '/_files/ThemeDeployment/run/source_cased_js',
                'destinationContext' => array(
                    'area' => 'frontend',
                    'locale' => 'not_important',
                    'themePath' => 'theme_path',
                    'module' => null
                )
            )
        );
        $object->run($copyRules);
        $this->assertFileExists($this->_tmpDir . '/frontend/theme_path/file.JS');
    }

    /**
     * Create Theme Deployment instance
     *
     * @param string $permitted
     * @param string|null $forbidden
     * @param bool $isDryRun
     * @return \Magento\Tools\View\Generator\ThemeDeployment
     */
    protected function _createThemeDeployment($permitted, $forbidden = null, $isDryRun = false)
    {
        $filesystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $preProcessor = $this->getMock(
            'Magento\View\Asset\PreProcessor\PreProcessorInterface',
            array(),
            array(),
            '',
            false
        );
        $fileFactory = $this->getMock('Magento\View\Publisher\FileFactory', array(), array(), '', false);
        $appState = $this->getMock('Magento\App\State', array(), array(), '', false);
        $themeFactory = $this->getMock('Magento\Core\Model\Theme\DataFactory', array('create'), array(), '', false);

        $object = new \Magento\Tools\View\Generator\ThemeDeployment(
            $this->_cssUrlResolver,
            $filesystem,
            $preProcessor,
            $fileFactory,
            $appState,
            $themeFactory,
            $this->_tmpDir,
            $permitted,
            $forbidden,
            $isDryRun
        );

        $fileObject = $this->getMock('Magento\View\Publisher\File', array(), array(), '', false);
        $fileFactory->expects($this->any())->method('create')->will($this->returnValue($fileObject));
        $appState->expects($this->any())->method('emulateAreaCode')->will($this->returnValue($fileObject));
        $fileObject->expects($this->any())->method('getSourcePath')->will($this->returnValue(false));

        return $object;
    }
}
