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
    }
}
 