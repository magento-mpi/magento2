<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Page_Asset_MergeStrategy_ChecksumTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Page_Asset_MergeStrategy_Checksum
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
     * @var string
     */
    protected $_mergedMetaFile = 'destination_file.js.dat';

    /**
     * @var array
     */
    protected $_filesArray = array('file1.js', 'file2.js');

    protected function setUp()
    {
        $this->_filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);

        $this->_strategy = $this->getMock('Mage_Core_Model_Page_Asset_MergeStrategy_MergeStrategyInterface');

        $this->_object = new Mage_Core_Model_Page_Asset_MergeStrategy_Checksum($this->_strategy, $this->_filesystem);
    }

    /**
     * Prepare common expectations for all mergeFiles tests
     */
    protected function _prepareStrategy()
    {
        $this->_filesystem->expects($this->exactly(2))
            ->method('getMTime')
            ->will($this->returnValueMap(
                array(
                    array('file1.js', null, '123'),
                    array('file2.js', null, '456'),
                )
            ));
    }

    /**
     * Test when everything is valid, no merging required
     */
    public function testMergeFilesNoMergeRequired()
    {
        $this->_prepareStrategy();

        $this->_filesystem
            ->expects($this->exactly(2))
            ->method('has')
            ->will($this->returnValueMap(
                array(
                    array($this->_mergedFile, null, true),
                    array($this->_mergedMetaFile, null, true),
                )
            ));
        ;

        $this->_filesystem
            ->expects($this->once())
            ->method('read')
            ->with($this->_mergedMetaFile)
            ->will($this->returnValue('123456'));
        ;

        $this->_filesystem
            ->expects($this->never())
            ->method('write')
        ;

        $this->_strategy
            ->expects($this->never())
            ->method('mergeFiles')
        ;

        $this->_object->mergeFiles($this->_filesArray, $this->_mergedFile);
    }

    /**
     * Test whether merged file or meta file does not exist
     *
     * @dataProvider mergeFilesFilesDoNotExistDataProvider
     */
    public function testMergeFilesFilesDoNotExist($isFileExists, $isMetaFileExists)
    {
        $this->_prepareStrategy();

        $this->_filesystem->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap(
                array(
                    array($this->_mergedFile, null, $isFileExists),
                    array($this->_mergedMetaFile, null, $isMetaFileExists),
                )
            ));
        ;

        $this->_strategy
            ->expects($this->once())
            ->method('mergeFiles')
            ->with($this->_filesArray, $this->_mergedFile)
        ;

        $this->_filesystem
            ->expects($this->once())
            ->method('write')
            ->with($this->_mergedMetaFile, '123456')
        ;

        $this->_object->mergeFiles($this->_filesArray, $this->_mergedFile);
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
        $this->_prepareStrategy();

        $this->_filesystem
            ->expects($this->exactly(2))
            ->method('has')
            ->will($this->returnValueMap(
                array(
                    array($this->_mergedFile, null, true),
                    array($this->_mergedMetaFile, null, true),
                )
            ));
        ;

        $this->_filesystem
            ->expects($this->once())
            ->method('read')
            ->with($this->_mergedMetaFile)
            ->will($this->returnValue('000000'));
        ;

        $this->_strategy
            ->expects($this->once())
            ->method('mergeFiles')
            ->with($this->_filesArray, $this->_mergedFile)
        ;

        $this->_filesystem
            ->expects($this->once())
            ->method('write')
            ->with($this->_mergedMetaFile, '123456')
        ;

        $this->_object->mergeFiles($this->_filesArray, $this->_mergedFile);
    }

    /**
     * @dataProvider setIsCssDataProvider
     */
    public function testSetIsCss($value)
    {
        $this->_strategy
            ->expects($this->once())
            ->method('setIsCss')
            ->with($value);
        $this->_object->setIsCss($value);
    }

    public function setIsCssDataProvider()
    {
        return array(array(true), array(false));
    }
}
