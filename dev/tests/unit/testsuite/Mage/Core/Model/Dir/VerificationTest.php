<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Dir_VerificationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $mode
     * @param array $expectedDirs
     * @dataProvider createAndVerifyDirectoriesDataProvider
     */
    public function testCreateAndVerifyDirectories($mode, $expectedDirs)
    {
        // Plan
        $dirs = new Mage_Core_Model_Dir('base_dir');
        $appState = new Mage_Core_Model_App_State($mode);

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);

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

        // Do
        $model = new Mage_Core_Model_Dir_Verification(
            $filesystem,
            $dirs,
            $appState
        );
        $model->createAndVerifyDirectories();

        // Check
        foreach ($actualCreatedDirs as $index => $dir) {
            $actualCreatedDirs[$index] = str_replace(DIRECTORY_SEPARATOR, '/', $dir);
        }
        foreach ($actualVerifiedDirs as $index => $dir) {
            $actualVerifiedDirs[$index] = str_replace(DIRECTORY_SEPARATOR, '/', $dir);
        }
        $this->assertEquals($expectedDirs, $actualCreatedDirs);
        $this->assertEquals($expectedDirs, $actualVerifiedDirs);
    }

    /**
     * @return array
     */
    public static function createAndVerifyDirectoriesDataProvider()
    {
        return array(
            'developer mode' => array(
                Mage_Core_Model_App_State::MODE_DEVELOPER,
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
                Mage_Core_Model_App_State::MODE_DEFAULT,
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
                Mage_Core_Model_App_State::MODE_PRODUCTION,
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

    public function testCreateAndVerifyDirectoriesWithExistingDirectory()
    {
        $dirs = new Mage_Core_Model_Dir('base_dir');
        $appState = new Mage_Core_Model_App_State();

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->any())
            ->method('isDirectory')
            ->will($this->returnValue(true));
        $filesystem->expects($this->any())
            ->method('isWritable')
            ->will($this->returnValue(true));
        $filesystem->expects($this->never())
            ->method('createDirectory');

        $model = new Mage_Core_Model_Dir_Verification(
            $filesystem,
            $dirs,
            $appState
        );
        $model->createAndVerifyDirectories();
    }

    public function testCreateAndVerifyDirectoriesCreateException()
    {
        // Plan
        $message = str_replace('/', DIRECTORY_SEPARATOR,
            'Cannot create all required directories, check write access: base_dir/var/log, base_dir/var/session');
        $this->setExpectedException('Magento_BootstrapException', $message);

        $dirs = new Mage_Core_Model_Dir('base_dir');
        $appState = new Mage_Core_Model_App_State();

        $callback = function ($dir) {
            $dir = str_replace(DIRECTORY_SEPARATOR, '/', $dir);
            if (($dir == 'base_dir/var/log') || ($dir == 'base_dir/var/session')) {
                throw new Magento_Filesystem_Exception();
            }
        };
        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->any())
            ->method('createDirectory')
            ->will($this->returnCallback($callback));

        // Do
        $model = new Mage_Core_Model_Dir_Verification(
            $filesystem,
            $dirs,
            $appState
        );
        $model->createAndVerifyDirectories();
    }

    public function testCreateAndVerifyDirectoriesWritableException()
    {
        // Plan
        $message = str_replace('/', DIRECTORY_SEPARATOR,
            'Write access is needed: base_dir/var/log, base_dir/var/session');
        $this->setExpectedException('Magento_BootstrapException', $message);

        $dirs = new Mage_Core_Model_Dir('base_dir');
        $appState = new Mage_Core_Model_App_State();

        $dirWritableMap = array(
            array('base_dir/pub/media',     null, true),
            array('base_dir/pub/static',    null, true),
            array('base_dir/var',           null, true),
            array('base_dir/var/tmp',       null, true),
            array('base_dir/var/cache',     null, true),
            array('base_dir/var/log',       null, false),
            array('base_dir/var/session',   null, false),
        );
        foreach ($dirWritableMap as $key => $val) {
            $dirWritableMap[$key][0] = str_replace('/', DIRECTORY_SEPARATOR, $val[0]);
        }
        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->any())
            ->method('isWritable')
            ->will($this->returnValueMap($dirWritableMap));

        // Do
        $model = new Mage_Core_Model_Dir_Verification(
            $filesystem,
            $dirs,
            $appState
        );
        $model->createAndVerifyDirectories();
    }
}
