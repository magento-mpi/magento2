<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Page\Asset\MergeStrategy;

use Magento\Filesystem,
    Magento\Filesystem\DirectoryList,
    Magento\Filesystem\Directory\Write;

class DirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Page\Asset\MergeStrategy\Direct
     */
    protected $_object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var Write | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_directory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cssUrlResolver;

    protected function setUp()
    {
        $this->_cssUrlResolver = $this->getMock('Magento\View\Url\CssResolver', array(), array(), '', false);
        $this->_filesystem = $this->getMock('Magento\Filesystem', array('getDirectoryWrite'), array(), '', false);
        $this->_directory = $this->getMock('Magento\Filesystem\Directory\Write', array(), array(), '', false);
        $this->_filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::PUB)
            ->will($this->returnValue($this->_directory));
        $this->_directory->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));

        $this->_object = new \Magento\Core\Model\Page\Asset\MergeStrategy\Direct(
            $this->_filesystem, $this->_cssUrlResolver
        );
    }

    /**
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage Unable to locate file 'no_file.js' for merging.
     */
    public function testMergeFilesNoFilesException()
    {
        $this->_object->mergeFiles(array('no_file.js'), 'some_file.js', 'js');
    }

    /**
     * Test mergeFiles() for css content type
     */
    public function testMergeFilesCss()
    {
        $this->_cssUrlResolver
            ->expects($this->exactly(2))
            ->method('replaceCssRelativeUrls')
            ->will($this->returnArgument(0));
        $this->_testMergeFiles('css');
    }

    /**
     * Test mergeFiles() for js content type
     */
    public function testMergeFilesJs()
    {
        $this->_cssUrlResolver
            ->expects($this->never())
            ->method('replaceCssRelativeUrls');
        $this->_testMergeFiles('js');
    }

    /**
     * Test mergeFiles itself
     *
     * @param string $contentType
     */
    protected function _testMergeFiles($contentType)
    {
        $mergedFile = '/merged_file.js';

        $this->_directory
            ->expects($this->any())
            ->method('isExist')
            ->will($this->returnValue(true));

        $this->_directory->expects($this->exactly(2))
            ->method('readFile')
            ->will($this->returnValueMap(
                array(
                    array('/pub/script_one.js', 'script1'),
                    array('/pub/script_two.js', 'script2'),
                )
            ));

        $this->_directory->expects($this->once())
            ->method('writeFile')
            ->with($mergedFile, 'script1script2');

        $this->_object->mergeFiles(array('/pub/script_one.js', '/pub/script_two.js'), $mergedFile, $contentType);
    }
}
