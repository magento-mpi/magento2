<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Inspection_JsHint_CommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Inspection_JsHint_Command|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cmd;

    protected function setUp()
    {
        $this->_cmd = $this->getMock(
            'Magento_TestFramework_Inspection_JsHint_Command',
            array('_getHostScript', '_fileExists', '_getJsHintPath',
                '_executeCommand', 'getFileName', '_execShellCmd', '_getJsHintOptions'),
            array('mage.js', 'report.xml')
        );
    }

    public function testCanRun()
    {
        $this->_cmd->expects($this->any())->method('_getHostScript')->will($this->returnValue('cscript'));
        $this->_cmd->expects($this->any())->method('_executeCommand')->with($this->stringContains('cscript'))
            ->will($this->returnValue(array('output', 0)));
        $this->_cmd->expects($this->any())->method('_getJsHintPath')->will($this->returnValue('jshint-path'));
        $this->_cmd->expects($this->any())->method('_fileExists')->with($this->isType('string'))
            ->will($this->returnValue(true));
        $this->_cmd->expects($this->any())->method('getFileName')->will($this->returnValue('mage.js'));
        $this->assertEquals(true, $this->_cmd->canRun());
    }

    public function testCanRunHostScriptDoesNotExistException()
    {
        $this->_cmd->expects($this->any())->method('_getHostScript')->will($this->returnValue('cscript'));
        $this->_cmd->expects($this->any())->method('_executeCommand')->with($this->stringContains('cscript'))
            ->will($this->returnValue(array('output', 1)));
        try {
            $this->_cmd->canRun();
        } catch(Exception $e){
            $this->assertEquals('cscript does not exist.', $e->getMessage());
        }
    }

    public function testCanRunJsHintPathDoesNotExistException()
    {
        $this->_cmd->expects($this->any())->method('_getHostScript')->will($this->returnValue('cscript'));
        $this->_cmd->expects($this->any())->method('_executeCommand')->with($this->stringContains('cscript'))
            ->will($this->returnValue(array('output', 0)));
        $this->_cmd->expects($this->any())->method('_getJsHintPath')->will($this->returnValue('jshint-path'));
        $this->_cmd->expects($this->any())->method('_fileExists')->with('jshint-path')->will($this->returnValue(false));
        try {
            $this->_cmd->canRun();
        } catch(Exception $e){
            $this->assertEquals('jshint-path does not exist.', $e->getMessage());
        }
    }

    public function testCanRunJsFileDoesNotExistException()
    {
        $this->_cmd->expects($this->any())->method('_getHostScript')->will($this->returnValue('cscript'));
        $this->_cmd->expects($this->any())->method('_executeCommand')->with($this->stringContains('cscript'))
            ->will($this->returnValue(array('output', 0)));
        $this->_cmd->expects($this->any())->method('_getJsHintPath')->will($this->returnValue('jshint-path'));
        $this->_cmd->expects($this->any())->method('_fileExists')->will($this->returnCallback(function () {
            $arg = func_get_arg(0);
            if ($arg == 'jshint-path') {
                return true;
            }
            if ($arg == 'mage.js') {
                return false;
            }
        }));
        $this->_cmd->expects($this->any())->method('getFileName')->will($this->returnValue('mage.js'));
        try {
            $this->_cmd->canRun();
        } catch(Exception $e){
            $this->assertEquals('mage.js does not exist.', $e->getMessage());
        }
    }

    public function testRun()
    {
        $this->_cmd->expects($this->any())->method('_getHostScript')->will($this->returnValue('cscript'));
        $this->_cmd->expects($this->any())->method('_getJsHintPath')->will($this->returnValue('jshint-path'));
        $this->_cmd->expects($this->any())->method('getFileName')->will($this->returnValue('mage.js'));
        $this->_cmd->expects($this->once())->method('_execShellCmd')->with('cscript "jshint-path" "mage.js" ');
        $this->_cmd->run(array());
    }
}
