<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Session;

class SaveHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate($handlers, $saveClass, $saveMethod)
    {
        $saveHandler = $this->getMock($saveClass, array(), array(), '', false);
        $objectManager = $this->getMock('\Magento\ObjectManager\ObjectManager', array('create'), array(), '', false);
        $objectManager->expects($this->once())->method('create')
            ->with($this->equalTo($saveClass), $this->equalTo(array()))->will($this->returnValue($saveHandler));
        $model = new SaveHandlerFactory($objectManager, $handlers);
        $result = $model->create($saveMethod);
        $this->assertInstanceOf($saveClass, $result);
        $this->assertInstanceOf('\Magento\Session\SaveHandler\Native', $result);
        $this->assertInstanceOf('\SessionHandler', $result);
    }

    /**
     * @return array
     */
    public static function createDataProvider()
    {
        return array(
            array(
                array(), 'Magento\Session\SaveHandler\Native', 'files'
            )
        );
    }
}
