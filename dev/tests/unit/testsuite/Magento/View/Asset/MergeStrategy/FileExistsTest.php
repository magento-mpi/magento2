<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\MergeStrategy;

class FileExistsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\MergeStrategy\FileExists
     */
    protected $_object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var \Magento\Filesystem\Directory\Write | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_directory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
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
        $this->_filesystem = $this->getMock(
            'Magento\Filesystem',
            array('getDirectoryWrite', 'getDirectoryRead'),
            array(),
            '',
            false
        );
        $this->_directory = $this->getMock('Magento\Filesystem\Directory\Write', array(), array(), '', false);
        $this->_filesystem->expects($this->any())
            ->method('getDirectoryRead')
            ->will($this->returnValue($this->_directory));
        $this->_strategy = $this->getMock('Magento\View\Asset\MergeStrategyInterface');

        $this->_object = new \Magento\View\Asset\MergeStrategy\FileExists(
            $this->_strategy,
            $this->_filesystem
        );
    }

    public function testMergeFilesFileExists()
    {
        $this->_strategy
            ->expects($this->never())
            ->method('mergeFiles');

        $this->_directory->expects($this->once())
            ->method('isExist')
            ->will($this->returnValue(true));

        $this->_object->mergeFiles($this->_filesArray, $this->_mergedFile, 'contentType');
    }

    public function testMergeFilesFileDoesNotExist()
    {
        $this->_strategy
            ->expects($this->once())
            ->method('mergeFiles')
            ->with($this->_filesArray, $this->_mergedFile, 'contentType');

        $this->_directory->expects($this->once())
            ->method('isExist')
            ->will($this->returnValue(false));

        $this->_object->mergeFiles($this->_filesArray, $this->_mergedFile, 'contentType');
    }
}
