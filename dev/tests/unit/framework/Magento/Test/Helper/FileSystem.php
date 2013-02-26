<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class for instantiation of file system mocks
 */
class Magento_Test_Helper_FileSystem
{
    /**
     * @var PHPUnit_Framework_TestCase
     */
    protected $_testCase;

    /**
     * Constructor
     *
     * @param PHPUnit_Framework_TestCase $testCase
     */
    public function __construct(PHPUnit_Framework_TestCase $testCase)
    {
        $this->_testCase = $testCase;
    }

    /**
     * Create and return the application directories instance, suppressing any interactions with the file system
     *
     * @param string $baseDir
     * @param array $uris
     * @param array $dirs
     * @return Mage_Core_Model_Dir
     */
    public function createDirInstance($baseDir, array $uris = array(), array $dirs = array())
    {
        $filesystemAdapter = $this->_testCase->getMockForAbstractClass('Magento_Filesystem_AdapterInterface');
        $filesystemAdapter
            ->expects($this->_testCase->any())
            ->method('isDirectory')
            ->will($this->_testCase->returnValue(true))
        ;
        $filesystemAdapter
            ->expects($this->_testCase->any())
            ->method('isWritable')
            ->will($this->_testCase->returnValue(true))
        ;
        $filesystem = new Magento_Filesystem($filesystemAdapter);
        return new Mage_Core_Model_Dir($filesystem, $baseDir, $uris, $dirs);
    }
}
