<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Cache_Type_AccessProxyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $method
     * @param array $params
     * @param bool $disabledResult
     * @param mixed $enabledResult
     * @dataProvider proxyMethodDataProvider
     */
    public function testProxyMethod($method, $params, $disabledResult, $enabledResult)
    {
        $identifier = 'cache_type_identifier';

        $frontendMock = $this->getMock('Magento_Cache_FrontendInterface');
        $builder = $frontendMock->expects($this->once())
            ->method($method);
        $builder = call_user_func_array(array($builder, 'with'), $params);
        $builder->will($this->returnValue($enabledResult));

        $cacheEnabler = $this->getMock('Mage_Core_Model_Cache_Types', array(), array(), '', false);
        $cacheEnabler->expects($this->at(0))
            ->method('isEnabled')
            ->with($identifier)
            ->will($this->returnValue(false));
        $cacheEnabler->expects($this->at(1))
            ->method('isEnabled')
            ->with($identifier)
            ->will($this->returnValue(true));

        $object = new Mage_Core_Model_Cache_Type_AccessProxy($frontendMock, $cacheEnabler, $identifier);

        // For the first call the cache is disabled - so fake default result is returned
        $result = call_user_func_array(array($object, $method), $params);
        $this->assertSame($disabledResult, $result);

        // For the second call the cache is enabled - so real cache result is returned
        $result = call_user_func_array(array($object, $method), $params);
        $this->assertSame($enabledResult, $result);
    }

    /**
     * @return array
     */
    public static function proxyMethodDataProvider()
    {
        return array(
            array('test', array('record_id'), false, 111),
            array('load', array('record_id'), false, '111'),
            array('save', array('record_value', 'record_id', array('tag'), 555), true, false),
            array('remove', array('record_id'), true, false),
            array('clean', array(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('tag')), true, false),
        );
    }
}
