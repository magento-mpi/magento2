<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Interception\Fixture\Intercepted;

use Magento\Interception\Fixture\Intercepted;

class Plugin
{
    /**
     * @var int
     */
    protected $_counter = 0;

    public function aroundC(Intercepted $subject, \Closure $next, $param1)
    {
        return '<I_P_C>' . $next($param1) . '</I_P_C>';
    }

    public function aroundD(Intercepted $subject, \Closure $next, $param1)
    {
        $this->_counter++;
        return '<I_P_D>' . $this->_counter . ': ' . $next($param1) . '</I_P_D>';
    }

    public function aroundK(Intercepted $subject, \Closure $next, $param1)
    {
        $result = $subject->C($param1);
        return '<I_P_K>' . $subject->F($result) . '</I_P_K>';
    }
}
