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
        $object = new \Magento\Cache\Frontend\Adapter\Zend($frontendMock);
        $helper = new Magento_TestFramework_Helper_ProxyTesting();
        $result = $helper->invokeWithExpectations($object, $frontendMock, $method, $params, $expectedResult, $method,
            $expectedParams);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public static function proxyMethodDataProvider()
    {
        return array(
            'test' => array('test', array('record_id'), array('RECORD_ID'), 111),
            'load' => array('load', array('record_id'), array('RECORD_ID'), '111'),
            'save' => array(
                'save',
                array('record_value', 'record_id', array('tag1', 'tag2'), 555),
                array('record_value', 'RECORD_ID', array('TAG1', 'TAG2'), 555),
                true,
            ),
            'remove' => array('remove', array('record_id'), array('RECORD_ID'), true),
            'clean mode "all"' => array(
                'clean',
                array(Zend_Cache::CLEANING_MODE_ALL, array()),
                array(Zend_Cache::CLEANING_MODE_ALL, array()),
                true,
            ),
            'clean mode "matching tag"' => array(
                'clean',
                array(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('tag1', 'tag2')),
                array(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('TAG1', 'TAG2')),
                true,
            ),
            'clean mode "matching any tag"' => array(
                'clean',
                array(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('tag1', 'tag2')),
                array(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('TAG1', 'TAG2')),
                true,
            ),
            'getBackend' => array(
                'getBackend',
                array(),
                array(),
                PHPUnit_Framework_MockObject_Generator::getMock('Zend_Cache_Backend'),
            ),
        );
    }

    /**
     * @param string $cleaningMode
     * @param string $expectedErrorMessage
     * @dataProvider cleanExceptionDataProvider
     */
    public function testCleanException($cleaningMode, $expectedErrorMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedErrorMessage);
        $object = new \Magento\Cache\Frontend\Adapter\Zend($this->getMock('Zend_Cache_Core'));
        $object->clean($cleaningMode);
    }

    public function cleanExceptionDataProvider()
    {
        return array(
            'cleaning mode "expired"' => array(
                Zend_Cache::CLEANING_MODE_OLD,
                "Magento cache frontend does not support the cleaning mode 'old'.",
            ),
            'cleaning mode "not matching tag"' => array(
                Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG,
                "Magento cache frontend does not support the cleaning mode 'notMatchingTag'.",
            ),
            'non-existing cleaning mode' => array(
                'nonExisting',
                "Magento cache frontend does not support the cleaning mode 'nonExisting'.",
            ),
        );
    }

    public function testGetLowLevelFrontend()
    {
        $frontendMock = $this->getMock('Zend_Cache_Core');
        $object = new \Magento\Cache\Frontend\Adapter\Zend($frontendMock);
        $this->assertSame($frontendMock, $object->getLowLevelFrontend());
    }
}
