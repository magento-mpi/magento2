<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Dir_VerificationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $mode
     * @param array $expectedDirs
     * @dataProvider createAndVerifyDirectoriesDataProvider
     */
    public function testCreateAndVerifyDirectoriesNonExisting($mode, $expectedDirs)
    {
        $model = $this->_createModelForVerification($mode, false, $actualCreatedDirs, $actualVerifiedDirs);
        $model->createAndVerifyDirectories();

        // Check
        $this->assertEquals($expectedDirs, $actualCreatedDirs);
        $this->assertEmpty($actualVerifiedDirs,
            'Non-existing directories must be just created, no write access verification is needed');
    }

    /**
     * @param string $mode
     * @param array $expectedDirs
     * @dataProvider createAndVerifyDirectoriesDataProvider
     */
    public function testCreateAndVerifyDirectoriesExisting($mode, $expectedDirs)
    {
        $model = $this->_createModelForVerification($mode, true, $actualCreatedDirs, $actualVerifiedDirs);
        $model->createAndVerifyDirectories();

        // Check
        $this->assertEmpty($actualCreatedDirs, 'Directories must not be created, when they exist');
        $this->assertEquals($expectedDirs, $actualVerifiedDirs);
    }

    /**
     * Create model to test creation of directories and verification of their write-access
     *
     * @param string $mode
     * @param bool $isExist
     * @param array $actualCreatedDirs
     * @param array $actualVerifiedDirs
     * @return Magento_Core_Model_Dir_Verification
     */
    protected function _createModelForVerification($mode, $isExist, &$actualCreatedDirs, &$actualVerifiedDirs)
    {
        $dirs = new Magento_Core_Model_Dir('base_dir');
        $appState = new Magento_Core_Model_App_State(time(), $mode);

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->any())
            ->method('isDirectory')
            ->will($this->returnValue($isExist));

        $actualCreatedDirs = array();
        $callbackCreate = function ($dir) use (&$actualCreatedDirs) {
            $actualCreatedDirs[] = $dir;
        };
        $filesystem->expects($this->any())
            ->method('createDirectory')
            ->will($this->returnCallback($callbackCreate));

        $actualVerifiedDirs = array();
        $callbackVerify = function ($dir) use (&$actualVerifiedDirs) {
            $actualVerifiedDirs[] = $dir;
            return true;
        };
        $filesystem->expects($this->any())
            ->method('isWritable')
            ->will($this->returnCallback($callbackVerify));

        return new Magento_Core_Model_Dir_Verification(
            $filesystem,
            $dirs,
            $appState
        );
    }

    /**
     * @return array
     */
    public static function createAndVerifyDirectoriesDataProvider()
    {
        return array(
            'developer mode' => array(
                Magento_Core_Model_App_State::MODE_DEVELOPER,
                array(
                    'base_dir/pub/media',
                    'base_dir/pub/static',
                    'base_dir/var',
                    'base_dir/var/tmp',
                    'base_dir/var/cache',
                    'base_dir/var/log',
                    'base_dir/var/session'
                ),
            ),
            'default mode' => array(
                Magento_Core_Model_App_State::MODE_DEFAULT,
                array(
                    'base_dir/pub/media',
                    'base_dir/pub/static',
                    'base_dir/var',
                    'base_dir/var/tmp',
                    'base_dir/var/cache',
                    'base_dir/var/log',
                    'base_dir/var/session'
                ),
            ),
            'production mode' => array(
                Magento_Core_Model_App_State::MODE_PRODUCTION,
                array(
                    'base_dir/pub/media',
                    'base_dir/var',
                    'base_dir/var/tmp',
                    'base_dir/var/cache',
                    'base_dir/var/log',
                    'base_dir/var/session'
                ),
            ),
        );
    }

    public function testCreateAndVerifyDirectoriesCreateException()
    {
        // Plan
        $this->setExpectedException('Magento_BootstrapException',
            'Cannot create or verify write access: base_dir/var/log, base_dir/var/session');

        $dirs = new Magento_Core_Model_Dir('base_dir');
        $appState = new Magento_Core_Model_App_State(time());

        $callback = function ($dir) {
            if (($dir == 'base_dir/var/log') || ($dir == 'base_dir/var/session')) {
                throw new Magento_Filesystem_Exception();
            }
        };
        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->any())
            ->method('createDirectory')
            ->will($this->returnCallback($callback));

        // Do
        $model = new Magento_Core_Model_Dir_Verification(
            $filesystem,
            $dirs,
            $appState
        );
        $model->createAndVerifyDirectories();
    }

    public function testCreateAndVerifyDirectoriesWritableException()
    {
        // Plan
        $this->setExpectedException('Magento_BootstrapException',
            'Cannot create or verify write access: base_dir/var/log, base_dir/var/session');

        $dirs = new Magento_Core_Model_Dir('base_dir');
        $appState = new Magento_Core_Model_App_State(time());

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->any())
            ->method('isDirectory')
            ->will($this->returnValue(true));

        $dirWritableMap = array(
            array('base_dir/pub/media',     null, true),
            array('base_dir/pub/static',    null, true),
            array('base_dir/var',           null, true),
            array('base_dir/var/tmp',       null, true),
            array('base_dir/var/cache',     null, true),
            array('base_dir/var/log',       null, false),
            array('base_dir/var/session',   null, false),
        );
        $filesystem->expects($this->any())
            ->method('isWritable')
            ->will($this->returnValueMap($dirWritableMap));

        // Do
        $model = new Magento_Core_Model_Dir_Verification(
            $filesystem,
            $dirs,
            $appState
        );
        $model->createAndVerifyDirectories();
    }
}
