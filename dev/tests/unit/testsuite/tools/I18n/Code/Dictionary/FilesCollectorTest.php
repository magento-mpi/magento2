<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary;

class FilesCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_testDir;

    /**
     * @var \Magento\Tools\I18n\Code\Dictionary\FilesCollector
     */
    protected $_filesCollector;

    protected function setUp()
    {
        // dev/tests/unit/testsuite/tools/I18n/_files
        $this->_testDir = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../..')) . '/_files';

        $objectManagerHelper = new \Magento_Test_Helper_ObjectManager($this);
        $this->_filesCollector = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Dictionary\FilesCollector');
    }

    public function testGetFilesWithoutMask()
    {
        $expectedResult = array(
            $this->_testDir . DS . 'file.js',
            $this->_testDir . DS . 'layout.xml',
        );
        $this->assertEquals($expectedResult, $this->_filesCollector->getFiles(array($this->_testDir)));
    }

    public function testGetFilesWithMask()
    {
        $expectedResult = array(
            $this->_testDir . DS .'file.js',
        );
        $this->assertEquals($expectedResult, $this->_filesCollector->getFiles(array($this->_testDir), '/\.js$/'));
    }
}
