<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\State;
use Magento\Framework\Autoload\AutoloaderRegistry;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\TestFramework\Application::getTempDir
     * @covers \Magento\TestFramework\Application::getDbInstance()
     * @covers \Magento\TestFramework\Application::getInitParams()
     */
    public function testConstructor()
    {
        $shell = $this->getMock('\Magento\Framework\Shell', [], [], '', false);
        $autoloadWrapper = $this->getMockBuilder('Magento\Framework\Autoload\ClassLoaderWrapper')
            ->disableOriginalConstructor()->getMock();
        $tempDir = '/temp/dir';
        $appMode = \Magento\Framework\App\State::MODE_DEVELOPER;

        $object = new \Magento\TestFramework\Application(
            $shell,
            $tempDir,
            'config.php',
            '',
            $appMode,
            $autoloadWrapper
        );

        $this->assertEquals($tempDir, $object->getTempDir(), 'Temp directory is not set in Application');

        $initParams = $object->getInitParams();
        $this->assertInternalType('array', $initParams, 'Wrong initialization parameters type');
        $this->assertArrayHasKey(
            Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS,
            $initParams,
            'Directories are not configured'
        );
        $this->assertArrayHasKey(State::PARAM_MODE, $initParams, 'Application mode is not configured');
        $this->assertEquals(
            \Magento\Framework\App\State::MODE_DEVELOPER,
            $initParams[State::PARAM_MODE],
            'Wrong application mode configured'
        );
    }
}
