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
     * @var array
     */
    protected $_definitions = array('type' => 'definitions');

    /**
     * @covers \Magento\Interception\Definition\Compiled::getMethodList
     */
    public function testGetMethodList()
    {
        $model = new \Magento\Interception\Definition\Compiled($this->_definitions);
        $this->assertEquals('definitions', $model->getMethodList('type'));
    }
}
