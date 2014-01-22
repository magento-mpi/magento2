<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\MergeStrategy;

class ChecksumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\MergeStrategy\Checksum
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
     * @var string
     */
    protected $_mergedMetaFile = 'destination_file.js.dat';

    /**
     * @var array
     */
    protected $_filesArray = array('file1.js', 'file2.js');

    protected function setUp()
    {
        $this->_filesystem = $this->getMock('Magento\App\Filesystem', array('getDirectoryWrite'), array(), '', false);
        $this->_directory = $this->getMock('Magento\Filesystem\Directory\Write', array(), array(), '', false);
        $this->_filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->will($this->returnValue($this->_directory));
        $this->_directory->expects($this->exactly(2))
            ->method('stat')
            ->will($this->returnValueMap(
                array(
                    array('file1.js', array('mtime' => '123')),
                    array('file2.js', array('mtime' => '456')),
                )
            ));
        $this->_directory->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));

        $this->_strategy = $this->getMock('Magento\View\Asset\MergeStrategyInterface');

        $this->_object = new \Magento\View\Asset\MergeStrategy\Checksum($this->_strategy,
            $this->_filesystem);
    }

    /**
     * Test when everything is valid, no merging required
     */
    public function testMergeFilesNoMergeRequired()
    {
        $this->_directory
            ->expects($this->exactly(2))
            ->method('isExist')
            ->will($this->returnValue(true));

        $this->_directory
            ->expects($this->once())
            ->method('readFile')
            ->with($this->_mergedMetaFile)
            ->will($this->returnValue('123456'));

        $this->_directory
            ->expects($this->never())
            ->method('writeFile');

        $this->_strategy
            ->expects($this->never())
            ->method('mergeFiles');

        $this->_object->mergeFiles($this->_filesArray, $this->_mergedFile, 'contentType');
    }

    /**
     * Test whether merged file or meta file does not exist
     *
     * @dataProvider mergeFilesFilesDoNotExistDataProvider
     */
    public function testMergeFilesFilesDoNotExist($isFileExists, $isMetaFileExists)
    {
        $this->_directory
            ->expects($this->any())
            ->method('isExist')
            ->will($this->returnValueMap(
                array(array($this->_mergedFile, $isFileExists), array($this->_mergedMetaFile, $isMetaFileExists))));


        $this->_strategy
            ->expects($this->once())
            ->method('mergeFiles')
            ->with($this->_filesArray, $this->_mergedFile, 'contentType');

        $this->_directory
            ->expects($this->once())
            ->method('writeFile')
            ->with($this->_mergedMetaFile, '123456');

        $this->_object->mergeFiles($this->_filesArray, $this->_mergedFile, 'contentType');
    }

    /**
     * @return array
     */
    public function mergeFilesFilesDoNotExistDataProvider()
    {
        return array(
            'no file' => array(false, true),
            'no meta file' => array(true, false)
        );
    }

    /**
     * Test whether merged file and meta-file exist, though checksum is wrong (files were updated)
     */
    public function testMergeFilesExistWrongChecksum()
    {
        $this->_directory
            ->expects($this->exactly(2))
            ->method('isExist')
            ->will($this->returnValue(true));

        $this->_directory
            ->expects($this->once())
            ->method('readFile')
            ->with($this->_mergedMetaFile)
            ->will($this->returnValue('000000'));

        $this->_strategy
            ->expects($this->once())
            ->method('mergeFiles')
            ->with($this->_filesArray, $this->_mergedFile, 'contentType');

        $this->_directory
            ->expects($this->once())
            ->method('writeFile')
            ->with($this->_mergedMetaFile, '123456');

        $this->_object->mergeFiles($this->_filesArray, $this->_mergedFile, 'contentType');
    }
}
