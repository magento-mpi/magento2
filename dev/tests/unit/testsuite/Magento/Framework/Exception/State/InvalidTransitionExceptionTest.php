<?php
/**
 * Invalid state exception
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Exception\State;

class InvalidTransitionExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $instanceClass = 'Magento\Framework\Exception\State\InvalidTransitionException';
        $message =  'message %1 %2';
        $params = [
            'parameter1',
            'parameter2',
        ];
        $cause = new \Exception();
        $stateException = new InvalidTransitionException($message, $params, $cause);
        $this->assertInstanceOf($instanceClass, $stateException);
    }
}
