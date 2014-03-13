<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Definition;

class CompiledTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $_definitions = array('type' => 'definitions');

    /**
     * @covers \Magento\Interception\Definition\Compiled::getMethodList
     * @covers \Magento\Interception\Definition\Compiled::__construct
     */
    public function testGetMethodList()
    {
        $model = new \Magento\Interception\Definition\Compiled($this->_definitions);
        $this->assertEquals('definitions', $model->getMethodList('type'));
    }
}
