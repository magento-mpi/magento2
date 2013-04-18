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
     * @var Magento_Filesystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**#@+
     * Directory to be used for testing
     * @var string
     */
    protected $_existingWritableDir;
    protected $_existingReadonlyDir;
    protected $_absentWritableDir;
    protected $_absentReadonlyDir;
    /**#@-*/

    public function setUp()
    {
        $this->_existingWritableDir = 'existing_writable';
        $this->_existingReadonlyDir = 'existing_readonly';
        $this->_absentWritableDir = 'non-existing_writable';
        $this->_absentReadonlyDir = 'non-existing_readonly';

        $this->_filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $this->_filesystem->expects($this->any())
            ->method('isDirectory')
            ->will($this->returnValueMap(
                array(
                    array($this->_existingWritableDir, null, true),
                    array($this->_existingReadonlyDir, null, true),
                    array($this->_absentWritableDir, null, false),
                    array($this->_absentReadonlyDir, null, false),
                )
            )
        );

        $this->_filesystem->expects($this->any())
            ->method('isWritable')
            ->will($this->returnValueMap(
                array(
                    array($this->_existingWritableDir, null, true),
                    array($this->_existingReadonlyDir, null, false),
                    array($this->_absentWritableDir, null, true),
                    array($this->_absentReadonlyDir, null, false),
                )
            )
        );
    }

    public function testCreateMissingDirectoriesWithDefaultCodes()
    {
        // Plan
        $dirs = new Mage_Core_Model_Dir('base_dir');

        $actualCreatedDirs = array();
        $callback = function ($dir) use (&$actualCreatedDirs) {
            $actualCreatedDirs[] = $dir;
        };
        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->any())
            ->method('createDirectory')
            ->will($this->returnCallback($callback));

        // Do
        $model = new Mage_Core_Model_Dir_Verification(
            $filesystem,
            $dirs
        );
        $model->createMissingDirectories();

        // Check
        foreach ($actualCreatedDirs as $index => $dir) {
            $actualCreatedDirs[$index] = str_replace(DIRECTORY_SEPARATOR, '/', $dir);
        }
        $expectedCreatedDirs = array('base_dir/pub/media', 'base_dir/pub/static', 'base_dir/var', 'base_dir/var/tmp',
            'base_dir/var/cache','base_dir/var/log','base_dir/var/session');
        $this->assertEquals($expectedCreatedDirs, $actualCreatedDirs);
    }

    public function testCreateMissingDirectoriesCustomized()
    {
        $this->_filesystem->expects($this->once())
            ->method('createDirectory')
            ->with($this->_absentWritableDir);
        $model = $this->_createModelWithCustomDir(Mage_Core_Model_Dir::MEDIA, $this->_absentWritableDir);
        $model->createMissingDirectories();
    }

    public function testCreateMissingDirectoriesWithExistingDirectory()
    {
        $this->_filesystem->expects($this->never())
            ->method('createDirectory');
        $model = $this->_createModelWithCustomDir(Mage_Core_Model_Dir::MEDIA, $this->_existingWritableDir);
        $model->createMissingDirectories();
    }

    public function testCreateMissingDirectoriesWhenReadonly()
    {
        $this->setExpectedException(
            'Magento_BootstrapException',
            'Cannot create directory(ies), check write access: ' . $this->_absentReadonlyDir
        );

        $this->_filesystem->expects($this->once())
            ->method('createDirectory')
            ->with($this->_absentReadonlyDir)
            ->will($this->throwException(new Magento_Filesystem_Exception()));
        $model = $this->_createModelWithCustomDir(Mage_Core_Model_Dir::MEDIA, $this->_absentReadonlyDir);
        $model->createMissingDirectories();
    }

    public function testVerifyWriteAccess()
    {
        $model = $this->_createModelWithCustomDir(Mage_Core_Model_Dir::MEDIA, $this->_existingWritableDir);
        $model->verifyWriteAccess();
    }

    public function testVerifyWriteAccessWhenReadonly()
    {
        $this->setExpectedException(
            'Magento_BootstrapException',
            'The directory(ies) must have write access: ' . $this->_existingReadonlyDir
        );
        $model = $this->_createModelWithCustomDir(Mage_Core_Model_Dir::MEDIA, $this->_existingReadonlyDir);
        $model->verifyWriteAccess();
    }

    /**
     * Instantiate and return the model, which has just one custom dir to check
     *
     * @param string $dirCode
     * @param string $path
     * @return Mage_Core_Model_Dir_Verification
     */
    protected function _createModelWithCustomDir($dirCode, $path)
    {
        $dirs = new Mage_Core_Model_Dir('base_dir', array(), array($dirCode => $path));

        $model = new Mage_Core_Model_Dir_Verification(
            $this->_filesystem,
            $dirs,
            array($dirCode)
        );
        return $model;
    }
}
