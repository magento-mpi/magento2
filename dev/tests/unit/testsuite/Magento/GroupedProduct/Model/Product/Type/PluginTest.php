<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Model\Product\Type;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManagerMock;

    /**
     * @var \Magento\GroupedProduct\Model\Product\Type\Plugin
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->moduleManagerMock = $this->getMock('\Magento\Module\Manager', array(), array(), '', false);
        $this->subjectMock = $this->getMock('Magento\Catalog\Model\Product\Type', array(), array(), '', false);
        $this->object = new \Magento\GroupedProduct\Model\Product\Type\Plugin($this->moduleManagerMock);
    }

    public function testAfterGetOptionArray()
    {
        $this->moduleManagerMock->expects($this->any())->method('isOutputEnabled')->will($this->returnValue(false));
        $this->assertEquals(array(),
            $this->object->afterGetOptionArray($this->subjectMock, array('grouped' => 'test'))
        );
    }
} 
