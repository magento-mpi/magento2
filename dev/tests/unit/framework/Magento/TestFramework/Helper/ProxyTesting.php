<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class for testing the proxy objects
 */
class Magento_TestFramework_Helper_ProxyTesting
{
    /**
     * Invoke the proxy's method, imposing expectations on proxied object, that it must be invoked as well with
     * appropriate parameters.
     *
     * @param mixed $object Proxy
     * @param PHPUnit_Framework_MockObject_MockObject $proxiedObject
     * @param string $method Proxy's method to invoke
     * @param array $params Parameters to be passed to proxy
     * @param null $proxiedResult Result, that must be returned by the proxied object
     * @param null $expectedMethod Expected method, to be invoked in the proxied method
     * @param null $expectedParams Expected parameters, to be passed to the proxied method
     * @return mixed
     */
    public function invokeWithExpectations($object, PHPUnit_Framework_MockObject_MockObject $proxiedObject, $method,
        $params = array(), $proxiedResult = null, $expectedMethod = null, $expectedParams = null
    ) {
        if ($expectedMethod === null) {
            $expectedMethod = $method;
        }
        if ($expectedParams === null) {
            $expectedParams = $params;
        }
        $builder = $proxiedObject->expects(new PHPUnit_Framework_MockObject_Matcher_InvokedCount(1))
            ->method($expectedMethod);
        $builder = call_user_func_array(array($builder, 'with'), $expectedParams);
        $builder->will(new PHPUnit_Framework_MockObject_Stub_Return($proxiedResult));

        return call_user_func_array(array($object, $method), $params);
    }
}
