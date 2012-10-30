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

class Inspection_JsHint_CommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Inspection_JsHint_Command|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cmd;

    protected function setUp()
    {
        $this->_cmd = $this->getMock(
            'Inspection_JsHint_Command',
            array('_getHostScript', '_fileExists', '_getJsHintPath', '_executeCommand', 'getFileName'),
            array('mage.js', 'report.xml')
        );
    }


    public function testCanRun()
    {
        $this->_cmd
            ->expects($this->any())
            ->method('_getHostScript')
            ->will($this->returnValue('csript'));

        $this->_cmd
            ->expects($this->any())
            ->method('_executeCommand')
            ->with($this->stringContains('csript'))
            ->will($this->returnValue(array('sucess', 0)));

        $this->_cmd
            ->expects($this->any())
            ->method('_getJsHintPath')
            ->will($this->returnValue('jshint-path'));

        $this->_cmd
            ->expects($this->any())
            ->method('_fileExists')
            ->with($this->isType('string'))
            ->will($this->returnValue(true));

        $this->_cmd
            ->expects($this->any())
            ->method('getFileName')
            ->will($this->returnValue('mage.js'));

        $this->assertEquals(true, $this->_cmd->canRun());
    }

    public function testRun()
    {

    }

}