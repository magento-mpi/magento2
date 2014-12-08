<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Session;

class SaveHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate($handlers, $saveClass, $saveMethod)
    {
        $saveHandler = $this->getMock($saveClass);
        $objectManager = $this->getMock(
            '\Magento\Framework\ObjectManager\ObjectManager',
            ['create'],
            [],
            '',
            false
        );
        $objectManager->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $this->equalTo($saveClass),
            $this->equalTo([])
        )->will(
            $this->returnValue($saveHandler)
        );
        $model = new SaveHandlerFactory($objectManager, $handlers);
        $result = $model->create($saveMethod);
        $this->assertInstanceOf($saveClass, $result);
        $this->assertInstanceOf('\Magento\Framework\Session\SaveHandler\Native', $result);
        $this->assertInstanceOf('\SessionHandler', $result);
    }

    /**
     * @return array
     */
    public static function createDataProvider()
    {
        return [[[], 'Magento\Framework\Session\SaveHandler\Native', 'files']];
    }
}
