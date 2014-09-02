<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Matcher;

class MethodInvokedAtIndexTest extends \PHPUnit_Framework_TestCase
{
    public function testMatches()
    {
        $invocationObject = new \PHPUnit_Framework_MockObject_Invocation_Object(
            'ClassName',
            'ValidMethodName',
            array(),
            new \StdClass()
        );
        $matcher = new MethodInvokedAtIndex(0);
        $this->assertTrue($matcher->matches($invocationObject));

        $matcher = new MethodInvokedAtIndex(1);
        $matcher->matches($invocationObject);
        $this->assertTrue($matcher->matches($invocationObject));
    }
}
