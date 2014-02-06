<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Matcher;

use Magento\TestFramework\Matcher\MethodInvokedAtIndex as MethodInvokedAtIndex;

class MethodInvokedAtIndexTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $matcher = new MethodInvokedAtIndex(5, 'method');
        $this->assertEquals('invoked at sequence index 5', $matcher->toString());
    }

    public function testMatchesValid()
    {
        $invocationObject = new \PHPUnit_Framework_MockObject_Invocation_Object(
            'ClassName',
            'ValidMethodName',
            array(),
            new \StdClass()
        );
        $matcher = new MethodInvokedAtIndex(0, 'ValidMethodName');
        $this->assertTrue($matcher->matches($invocationObject));
    }

    public function testMatchesInvalid()
    {
        $invocationObject = new \PHPUnit_Framework_MockObject_Invocation_Object(
            'ClassName',
            'ValidMethodName',
            array(),
            new \StdClass()
        );
        $matcher = new MethodInvokedAtIndex(0, 'InvalidMethodName');
        $this->assertFalse($matcher->matches($invocationObject));
    }

    public function testVerifyValid()
    {
        $invocationObject = new \PHPUnit_Framework_MockObject_Invocation_Object(
            'ClassName',
            'ValidMethodName',
            array(),
            new \StdClass()
        );
        $matcher = new MethodInvokedAtIndex(0, 'ValidMethodName');
        $matcher->matches($invocationObject);

        $this->assertNull($matcher->verify());
    }

    /**
     * @expectedException \PHPUnit_Framework_ExpectationFailedException
     */
    public function testVerifyInvalid()
    {
        $invocationObject = new \PHPUnit_Framework_MockObject_Invocation_Object(
            'ClassName',
            'ValidMethodName',
            array(),
            new \StdClass()
        );
        $matcher = new MethodInvokedAtIndex(0, 'InvalidMethodName');
        $matcher->matches($invocationObject);

        $this->assertNull($matcher->verify());
    }
}
 