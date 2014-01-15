<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Config;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Config\View
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = new \Magento\Config\View(array(
            file_get_contents(__DIR__ . '/_files/view_one.xml'), file_get_contents(__DIR__ . '/_files/view_two.xml')
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructException()
    {
        new \Magento\Config\View(array());
    }

    public function testGetSchemaFile()
    {
        $this->assertFileExists($this->_model->getSchemaFile());
    }

    public function testGetVars()
    {
        $this->assertEquals(array('one' => 'Value One', 'two' => 'Value Two'), $this->_model->getVars('Two'));
    }

    public function testGetVarValue()
    {
        $this->assertFalse($this->_model->getVarValue('Unknown', 'nonexisting'));
        $this->assertEquals('Value One', $this->_model->getVarValue('Two', 'one'));
        $this->assertEquals('Value Two', $this->_model->getVarValue('Two', 'two'));
        $this->assertEquals('Value Three', $this->_model->getVarValue('Three', 'three'));
    }

    /**
     * @expectedException \Magento\Exception
     */
    public function testInvalidXml()
    {
        new \Magento\Config\View(array(file_get_contents(__DIR__ . '/_files/view_invalid.xml')));
    }
}
