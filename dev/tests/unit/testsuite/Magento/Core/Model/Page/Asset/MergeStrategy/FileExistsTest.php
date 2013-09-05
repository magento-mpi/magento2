<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Page_Asset_MergeStrategy_FileExistsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Page_Asset_MergeStrategy_FileExists
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_strategy;

    /**
     * @var string
     */
    protected $_mergedFile = 'destination_file.js';

    /**
     * @var array
     */
    protected $_filesArray = array('file1.js', 'file2.js');

    protected function setUp()
    {
        $this->_filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_strategy = $this->getMock('Magento_Core_Model_Page_Asset_MergeStrategyInterface');

        $this->_object = new Magento_Core_Model_Page_Asset_MergeStrategy_FileExists(
            $this->_strategy,
            $this->_filesystem
        );
    }

    public function testMergeFilesFileExists()
    {
        $this->_strategy
            ->expects($this->never())
            ->method('mergeFiles')
        ;

        $this->_filesystem->expects($this->once())
            ->method('has')
            ->with($this->_mergedFile)
            ->will($this->returnValue(true))
        ;

        $this->_object->mergeFiles($this->_filesArray, $this->_mergedFile, 'contentType');
    }

    public function testMergeFilesFileDoesNotExist()
    {
        $this->_strategy
            ->expects($this->once())
            ->method('mergeFiles')
            ->with($this->_filesArray, $this->_mergedFile, 'contentType')
        ;

        $this->_filesystem->expects($this->once())
            ->method('has')
            ->with($this->_mergedFile)
            ->will($this->returnValue(false))
        ;

        $this->_object->mergeFiles($this->_filesArray, $this->_mergedFile, 'contentType');
    }
}
