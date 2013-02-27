<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cache_Frontend_Adapter_ZendTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $method
     * @param array $params
     * @param array $expectedParams
     * @param mixed $expectedResult
     * @dataProvider proxyMethodDataProvider
     */
    public function testProxyMethod($method, $params, $expectedParams, $expectedResult)
    {
        $frontendMock = $this->getMock('Zend_Cache_Core');

        $builder = $frontendMock->expects($this->once())
            ->method($method);
        $builder = call_user_func_array(array($builder, 'with'), $expectedParams);
        $builder->will($this->returnValue($expectedResult));

        $object = new Magento_Cache_Frontend_Adapter_Zend($frontendMock);
        $result = call_user_func_array(array($object, $method), $params);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public static function proxyMethodDataProvider()
    {
        return array(
            array('test', array('record_id'), array('RECORD_ID'), 111),
            array('load', array('record_id'), array('RECORD_ID'), '111'),
            array(
                'save',
                array('record_value', 'record_id', array('tag1', 'tag2'), 555),
                array('record_value', 'RECORD_ID', array('TAG1', 'TAG2'), 555),
                true,
            ),
            array('remove', array('record_id'), array('RECORD_ID'), true),
            array(
                'clean',
                array(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('tag1', 'tag2')),
                array(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('TAG1', 'TAG2')),
                true,
            ),
            array(
                'getBackend',
                array(),
                array(),
                PHPUnit_Framework_MockObject_Generator::getMock('Zend_Cache_Backend'),
            ),
        );
    }

    public function testGetLowLevelFrontend()
    {
        $frontendMock = $this->getMock('Zend_Cache_Core');
        $object = new Magento_Cache_Frontend_Adapter_Zend($frontendMock);
        $this->assertSame($frontendMock, $object->getLowLevelFrontend());
    }
}
