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
        return '<IP:C>' . $next($param1) . '</IP:C>';
    }

    /**
     * @param InterceptedInterface $subject
     * @param \Closure $next
     * @param $param1
     * @return string
     */
    public function aroundF(InterceptedInterface $subject, \Closure $next, $param1)
    {
        return '<IP:F>' . $subject->D($next($subject->C($param1))) . '</IP:F>';
    }

    public function beforeG(InterceptedInterface $subject, $param1)
    {
        return array('<IP:bG>' . $param1 . '</IP:bG>');
    }

    public function aroundG(InterceptedInterface $subject, \Closure $next, $param1)
    {
        return $next('<IP:G>' . $param1 . '</IP:G>');
    }

    public function afterG(InterceptedInterface $subject, $result)
    {
        return '<IP:aG>' . $result . '</IP:aG>';
    }
} 
