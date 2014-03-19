<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Translate\Inline;

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $result = 'result';
        $objectManager = $this->getMockForAbstractClass('Magento\ObjectManager');
        $objectManager->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            $this->equalTo('Magento\Backend\Model\Translate\Inline\Config')
        )->will(
            $this->returnValue($result)
        );
        $factory = new ConfigFactory($objectManager);
        $this->assertEquals($result, $factory->get());
    }
}
