<?php
/**
 * Parent plugin
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Interception\Fixture\InterceptedParent;

use Magento\Interception\Fixture\InterceptedParent;

class Plugin
{
    /**
     * @param InterceptedParent $subject
     * @param InterceptedParent $next
     * @param string $param1
     * @param string $param2
     */
    public function aroundB(InterceptedParent $subject, InterceptedParent $next, $param1, $param2)
    {
    }
} 
