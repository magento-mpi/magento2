<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cache_Frontend_Decorator_BareTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $method
     * @param array $params
     * @param mixed $expectedResult
     * @dataProvider proxyMethodDataProvider
     */
    public function testProxyMethod($method, $params, $expectedResult)
    {
        $frontendMock = $this->getMock('Magento_Cache_FrontendInterface');

        $builder = $frontendMock->expects($this->once())
            ->method($method);
        $builder = call_user_func_array(array($builder, 'with'), $params);
        $builder->will($this->returnValue($expectedResult));

        $object = new Magento_Cache_Frontend_Decorator_Bare($frontendMock);
        $result = call_user_func_array(array($object, $method), $params);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public static function proxyMethodDataProvider()
    {
        return array(
            array('test', array('record_id'), 111),
            array('load', array('record_id'), '111'),
            array('save', array('record_value', 'record_id', array('tag'), 555), true),
            array('remove', array('record_id'), true),
            array('clean', array(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('tag')), true),
            array('getBackend', array(), PHPUnit_Framework_MockObject_Generator::getMock('Zend_Cache_Backend')),
            array('getLowLevelFrontend', array(),
                PHPUnit_Framework_MockObject_Generator::getMock('Zend_Cache_Core')
            ),
        );
    }
}
