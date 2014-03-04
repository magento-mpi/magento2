<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class CompiledTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Interception\Definition\Compiled
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_definitions = array('type' => 'definitions');

    protected function setUp()
    {
        $this->_model = new \Magento\Interception\Definition\Compiled($this->_definitions);
    }

    /**
     * @covers \Magento\Interception\Definition\Compiled::getMethodList
     */
    public function testGetMethodList()
    {
        $this->assertEquals('definitions', $this->_model->getMethodList('type'));
    }
}
