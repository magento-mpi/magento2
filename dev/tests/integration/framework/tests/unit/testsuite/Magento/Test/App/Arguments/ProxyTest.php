<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\App\Arguments;

class ProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test, that object proxies methods and returns their results
     *
     * @param string $method
     * @param array $params
     * @dataProvider proxiedMethodsDataProvider
     */
    public function testProxiedMethods($method, $params)
    {
        $subject = $this->getMock('\Magento\App\Arguments', array(), array(), '', false);
        $invocation = $subject->expects($this->once())
            ->method($method);
        $invocation = call_user_func_array(array($invocation, 'with'), $params);
        $expectedResult = new \stdClass();
        $invocation->will($this->returnValue($expectedResult));

        $object = new \Magento\TestFramework\App\Arguments\Proxy($subject);
        $actualResult = call_user_func_array(array($object, $method), $params);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public static function proxiedMethodsDataProvider()
    {
        return array(
            array('getConnection', array('connection name')),
            array('getConnections', array()),
            array('getResources', array()),
            array('getCacheFrontendSettings', array()),
            array('getCacheTypeFrontendId', array('cache type')),
            array('get', array('key', 'default')),
            array('reload', array()),
        );
    }

    public function testSetSubject()
    {
        $subject1 = $this->getMock('\Magento\App\Arguments', array(), array(), '', false);
        $subject1->expects($this->once())
            ->method('get');

        $subject2 = $this->getMock('\Magento\App\Arguments', array(), array(), '', false);
        $subject2->expects($this->once())
            ->method('get');

        $object = new \Magento\TestFramework\App\Arguments\Proxy($subject1);
        $object->get('data');

        $object->setSubject($subject2);
        $object->get('data');
    }


}
