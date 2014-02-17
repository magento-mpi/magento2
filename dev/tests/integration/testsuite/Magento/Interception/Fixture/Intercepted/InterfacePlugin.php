<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Interception\Fixture\Intercepted;

use Magento\Interception\Fixture\InterceptedInterface;

class InterfacePlugin
{
    /**
     * @param InterceptedInterface $subject
     * @param \Closure $next
     * @param string $param1
     * @return string
     */
    public function aroundC(InterceptedInterface $subject, \Closure $next, $param1)
    {
        return '<II_P_C>' . $next($param1) . '</II_P_C>';
    }

    /**
     * @param InterceptedInterface $subject
     * @param \Closure $next
     * @param $param1
     * @return string
     */
    public function aroundF(InterceptedInterface $subject, \Closure $next, $param1)
    {
        return '<II_P_F>' . $subject->D($next($subject->C($param1))) . '</II_P_F>';
    }
} 
