<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Translate\Inline;

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $result = 'result';
        $objectManager = $this->getMockForAbstractClass('Magento\ObjectManager');
        $objectManager
            ->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\Core\Model\Translate\Inline\Config'))
            ->will($this->returnValue($result));
        $factory = new ConfigFactory($objectManager);
        $this->assertEquals($result, $factory->create());
    }
}
