<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Filesystem\DirectoryList;

use Magento\App\State;

class VerificationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for createAndVerifyDirectories method
     *
     * @param string $mode
     * @param array $expectedDirs
     * @dataProvider createAndVerifyDirectoriesDataProvider
     */
    public function testCreateAndVerifyDirectories($mode, $expectedDirs)
    {
        $verification = $this->getVerificationInstance($expectedDirs, $mode);
        $verification->createAndVerifyDirectories();
    }

    /**
     * Data provider for testCreateAndVerifyDirectories
     *
     * @return array
     */
    public static function createAndVerifyDirectoriesDataProvider()
    {
        return array(
            'developer mode' => array(
                State::MODE_DEVELOPER,
                array(
                    \Magento\App\Filesystem::CACHE_DIR => array(true, true, 'base_dir/var/cache'),
                    \Magento\App\Filesystem::LOG_DIR => array(true, true, 'base_dir/var/log'),
                    \Magento\App\Filesystem::SESSION_DIR => array(true, true, 'base_dir/var/session')
                ),
            ),
            'with_not_existing_dirs' => array(
                State::MODE_DEFAULT,
                array(
                    \Magento\App\Filesystem::CACHE_DIR => array(false, true, 'base_dir/var/cache'),
                    \Magento\App\Filesystem::LOG_DIR => array(true, true, 'base_dir/var/log'),
                    \Magento\App\Filesystem::SESSION_DIR => array(false, true, 'base_dir/var/session')
                ),
            ),
            'production mode' => array(
                State::MODE_PRODUCTION,
                array(
                    \Magento\App\Filesystem::CACHE_DIR => array(true, true, 'base_dir/var/cache'),
                    \Magento\App\Filesystem::LOG_DIR => array(true, true, 'base_dir/var/log'),
                    \Magento\App\Filesystem::SESSION_DIR => array(true, true, 'base_dir/var/session')
                ),
            ),
        );
    }

    /**
     * Test for createAndVerifyDirectories method if some directories are not writable
     *
     * @param string $mode
     * @param array $expectedDirs
     * @dataProvider notWritableDataProvider
     * @expectedException \Magento\BootstrapException
     */
    public function testNotWritable($mode, $expectedDirs)
    {
        $verification = $this->getVerificationInstance($expectedDirs, $mode);
        $verification->createAndVerifyDirectories();
    }

    /**
     * Data provider for testNotWritable
     *
     * @return array
     */
    public static function notWritableDataProvider()
    {
        return array(
            'developer mode' => array(
                State::MODE_DEVELOPER,
                array(
                    \Magento\App\Filesystem::CACHE_DIR => array(true, false, 'base_dir/var/cache'),
                    \Magento\App\Filesystem::LOG_DIR => array(true, true, 'base_dir/var/log'),
                    \Magento\App\Filesystem::SESSION_DIR => array(true, false, 'base_dir/var/session')
                ),
            )
        );
    }

    /**
     * Test for createAndVerifyDirectories method if some directories cannot be created
     *
     * @param string $mode
     * @param array $expectedDirs
     * @dataProvider createExceptionDataProvider
     * @expectedException \Magento\BootstrapException
     */
    public function testCreateException($mode, $expectedDirs)
    {
        $verification = $this->getVerificationInstance($expectedDirs, $mode);
        $verification->createAndVerifyDirectories();
    }

    /**
     * Data provider for testCreateException
     *
     * @return array
     */
    public static function createExceptionDataProvider()
    {
        return array(
            'developer mode' => array(
                State::MODE_DEVELOPER,
                array(
                    \Magento\App\Filesystem::CACHE_DIR => array(true, false, 'base_dir/var/cache'),
                    \Magento\App\Filesystem::LOG_DIR => array(true, true, 'base_dir/var/log'),
                    \Magento\App\Filesystem::SESSION_DIR => array(true, false, 'base_dir/var/session', true)
                ),
            )
        );
    }

    /**
     * Get verification instance
     *
     * @param array $expectedDirs
     * @param string $mode
     * @return Verification
     */
    protected function getVerificationInstance(array $expectedDirs, $mode)
    {
        $filesystem = $this->getFilesystemMock($expectedDirs);
        $appState = $this->getMock('Magento\App\State', array('getMode'), array(), '', false);
        $appState->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($mode));
        return new Verification($filesystem, $appState);
    }

    /**
     * Get filesystem mock
     *
     * @param array $dirsToVerify
     * @return \Magento\Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFilesystemMock(array $dirsToVerify)
    {
        $filesystem = $this->getMock(
            'Magento\App\Filesystem',
            array('getDirectoryWrite', '__wakeup'),
            array(),
            '',
            false
        );
        $valueMap = array();
        foreach ($dirsToVerify as $code => $config) {
            $createException = isset($config[3]) ? $config[3] : false;
            $directory = $this->getDirectoryMock($config[0], $config[1], $config[2], $createException);
            $valueMap[] = array($code, $directory);
        }
        $filesystem->expects($this->exactly(count($dirsToVerify)))
            ->method('getDirectoryWrite')
            ->will($this->returnValueMap($valueMap));

        return $filesystem;
    }

    /**
     * Get directory mock
     *
     * @param bool $existing
     * @param bool $writable
     * @param string $absolutePath
     * @param bool $createException
     * @return \Magento\Filesystem\Directory\Write | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDirectoryMock($existing, $writable, $absolutePath, $createException)
    {
        $directory = $this->getMock(
            'Magento\Filesystem\Directory\Write',
            array('isExist', 'isWritable', 'getAbsolutePath', 'create'),
            array(),
            '',
            false
        );
        $directory->expects($this->once())
            ->method('isExist')
            ->will($this->returnValue($existing));

        if (!$existing) {
            if (!$createException) {
                $directory->expects($this->once())
                    ->method('create');
            } else {
                $directory->expects($this->once())
                    ->method('create')
                    ->will($this->throwException(new \Magento\Filesystem\FilesystemException('')));
            }
            return $directory;
        }

        $directory->expects($this->once())
            ->method('isWritable')
            ->will($this->returnValue($writable));

        if (!$writable) {
            $directory->expects($this->once())
                ->method('getAbsolutePath')
                ->will($this->returnValue($absolutePath));
        }

        return $directory;
    }
}
