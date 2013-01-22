<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Storage model test
 */
class Mage_Theme_Model_Wysiwyg_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var null|string
     */
    protected $_storageRoot;

    /**
     * @var null|Varien_Io_File
     */
    protected $_fileIo;

    /**
     * @var null|Mage_Theme_Model_Wysiwyg_Storage
     */
    protected $_storageModel;

    public function setUp()
    {
        $this->_storageModel = $this->getMock('Mage_Theme_Model_Wysiwyg_Storage', null, array(), '', false);

        $this->_fileIo = $this->getMock('Varien_Io_File', null, array(), '', false);

        $fileIoProperty = new ReflectionProperty($this->_storageModel, '_fileIo');
        $fileIoProperty->setAccessible(true);
        $fileIoProperty->setValue($this->_storageModel, $this->_fileIo);

        $this->_storageRoot = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'root';
    }

    public function testCreateFolder()
    {
        /** @var $options Mage_Core_Model_Config_Options */
        $options = Mage::getObjectManager()->create('Mage_Core_Model_Config_Options');
        $designDir = $this->_storageRoot;
        $newDirectoryName = 'dir1';

        $storageModel = $this->_storageModel;

        /** @var $helper Mage_Theme_Helper_Storage */
        $helper = $this->getMock(
            'Mage_Theme_Helper_Storage', array('getShortFilename', 'convertPathToId'), array(), '', false
        );

        $helper->expects($this->once())
            ->method('getShortFilename')
            ->will($this->returnValue($newDirectoryName));

        $helper->expects($this->once())
            ->method('convertPathToId')
            ->will($this->returnValue($newDirectoryName));

        $helperProperty = new ReflectionProperty($storageModel, '_helper');
        $helperProperty->setAccessible(true);
        $helperProperty->setValue($storageModel, $helper);

        $expectedResult = array(
            'name'       => 'dir1',
            'short_name' => 'dir1',
            'path'       => $designDir . DIRECTORY_SEPARATOR . $newDirectoryName,
            'id'         => 'dir1'
        );

        $this->assertEquals($expectedResult, $storageModel->createFolder($newDirectoryName, $designDir));
    }

    public function testGetDirsCollection()
    {
        $storageModel = $this->_storageModel;

        $expectedResult = array(
            $this->_storageRoot . DIRECTORY_SEPARATOR . 'dir1',
            $this->_storageRoot . DIRECTORY_SEPARATOR . 'dir2'
        );

        $this->assertEquals($expectedResult, $storageModel->getDirsCollection($this->_storageRoot));
    }

    public function testGetFilesCollection()
    {
        $storageModel = $this->_storageModel;

        /** @var $helper Mage_Theme_Helper_Storage */
        $helper = $this->getMock(
            'Mage_Theme_Helper_Storage', array('getCurrentPath'), array(), '', false
        );

        $helper->expects($this->once())
            ->method('getCurrentPath')
            ->will($this->returnValue($this->_storageRoot));

        $helperProperty = new ReflectionProperty($storageModel, '_helper');
        $helperProperty->setAccessible(true);
        $helperProperty->setValue($storageModel, $helper);

        $result = $storageModel->getFilesCollection();

        $this->assertEquals(2, count($result));

        $this->assertEquals('font1.ttf', $result[0]['text']);
        $this->assertEquals('font2.ttf', $result[1]['text']);
    }

    public function testTreeArray()
    {
        $storageModel = $this->_storageModel;

        /** @var $helper Mage_Theme_Helper_Storage */
        $helper = $this->getMock(
            'Mage_Theme_Helper_Storage', array('getCurrentPath', 'getStorageRoot'), array(), '', false
        );

        $helper->expects($this->once())
            ->method('getCurrentPath')
            ->will($this->returnValue($this->_storageRoot . DIRECTORY_SEPARATOR . 'dir2'));

        $helper->expects($this->once())
            ->method('getStorageRoot')
            ->will($this->returnValue($this->_storageRoot));

        $helperProperty = new ReflectionProperty($storageModel, '_helper');
        $helperProperty->setAccessible(true);
        $helperProperty->setValue($storageModel, $helper);

        $result = $storageModel->getTreeArray();

        $this->assertEquals(1, count($result));

        $this->assertEquals('L2RpcjIvTW9yZVRoYW4yMFN5bWJvbHNJbk5hbWU,', $result[0]['id']);
        $this->assertEquals('MoreThan20SymbolsInN...', $result[0]['text']);
    }
}
