<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test;

use Magento\App\State;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\TestFramework\Application::getInstallDir()
     * @covers \Magento\TestFramework\Application::getDbInstance()
     * @covers \Magento\TestFramework\Application::getInitParams()
     */
    public function testConstructor()
    {
        $dbInstance = $this->getMockForAbstractClass('Magento\TestFramework\Db\AbstractDb', array(), '', false);
        $installDir = '/install/dir';
        $appMode = \Magento\App\State::MODE_DEVELOPER;
        $directoryList = new \Magento\Filesystem\DirectoryList(BP);
        $filesystem = new \Magento\App\Filesystem(
            $directoryList,
            new \Magento\Filesystem\Directory\ReadFactory(),
            new \Magento\Filesystem\Directory\WriteFactory(),
            new \Magento\Filesystem\File\ReadFactory(
                new \Magento\Filesystem\DriverFactory($directoryList)
            ),
            new \Magento\Filesystem\File\WriteFactory(
                new \Magento\Filesystem\DriverFactory($directoryList)
            )
        );

        $object = new \Magento\TestFramework\Application(
            $dbInstance,
            $installDir,
            new \Magento\Simplexml\Element('<data/>'),
            '',
            array(),
            $appMode,
            $filesystem
        );

        $this->assertSame($dbInstance, $object->getDbInstance(), 'Db instance is not set in Application');
        $this->assertEquals($installDir, $object->getInstallDir(), 'Install directory is not set in Application');

        $initParams = $object->getInitParams();
        $this->assertInternalType('array', $initParams, 'Wrong initialization parameters type');
        $this->assertArrayHasKey(\Magento\App\Filesystem::PARAM_APP_DIRS, $initParams,
            'Directories are not configured');
        $this->assertArrayHasKey(State::PARAM_MODE, $initParams,
            'Application mode is not configured');
        $this->assertEquals(
            \Magento\App\State::MODE_DEVELOPER,
            $initParams[State::PARAM_MODE],
            'Wrong application mode configured'
        );
    }
}
