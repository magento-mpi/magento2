<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\I18n\Code;

class FilesCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_testDir;

    /**
     * @var \Magento\Tools\I18n\Code\FilesCollector
     */
    protected $_filesCollector;

    protected function setUp()
    {
        // dev/tests/unit/testsuite/tools/I18n/Code/_files/files_collector
        $this->_testDir = str_replace('\\', '/', realpath(dirname(__FILE__))) . '/_files/files_collector/';

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_filesCollector = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\FilesCollector');
    }

    public function testGetFilesWithoutMask()
    {
        $expectedResult = array($this->_testDir . 'default.xml', $this->_testDir . 'file.js');
        $files = $this->_filesCollector->getFiles(array($this->_testDir));
        $this->assertEquals($expectedResult, $files);
    }

    public function testGetFilesWithMask()
    {
        $expectedResult = array($this->_testDir . 'file.js');
        $this->assertEquals($expectedResult, $this->_filesCollector->getFiles(array($this->_testDir), '/\.js$/'));
    }
}
