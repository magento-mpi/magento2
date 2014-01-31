<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translate\Inline;

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $result = 'result';
        $objectManager = $this->getMockForAbstractClass('Magento\ObjectManager');
        $objectManager
            ->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\Translate\Inline\ConfigInterface'))
            ->will($this->returnValue($result));
        $factory = new \Magento\Translate\Inline\ConfigFactory($objectManager);
        $this->assertEquals($result, $factory->get());
    }
}
