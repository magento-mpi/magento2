<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Interception\Chain;

use Magento\Interception\Definition;
use Magento\Interception\PluginList;

class Chain implements \Magento\Interception\Chain
{
    /**
     * @var \Magento\Interception\PluginList
     */
    protected $pluginList;

    /**
     * @param PluginList $pluginList
     */
    public function __construct(PluginList $pluginList)
    {
        $this->pluginList = $pluginList;
    }

    /**
     * Invoke next plugin in chain
     *
     * @param string $type
     * @param string $method
     * @param string $previousPluginCode
     * @param $subject
     * @param $arguments
     * @return mixed|void
     */
    public function invokeNext($type, $method, $subject, array $arguments, $previousPluginCode = null)
    {
        $pluginInfo = $this->pluginList->getNext($type, $method, $previousPluginCode);
        $capMethod = ucfirst($method);
        $result = null;
        if (isset($pluginInfo[Definition::LISTENER_BEFORE])) {
            foreach ($pluginInfo[Definition::LISTENER_BEFORE] as $code) {
                $beforeResult = call_user_func_array(
                    array($this->pluginList->getPlugin($type, $code), 'before' . $capMethod),
                    array_merge(array($subject), $arguments)
                );
                if ($beforeResult) {
                    $arguments = $beforeResult;
                }
            }
        }
        if (isset($pluginInfo[Definition::LISTENER_AROUND])) {
            $chain = $this;
            $code = $pluginInfo[Definition::LISTENER_AROUND];
            $next = function() use ($chain, $type, $method, $subject, $code) {
                return $chain->invokeNext($type, $method, $subject, func_get_args(), $code);
            };
            $result = call_user_func_array(
                array($this->pluginList->getPlugin($type, $code), 'around' . $capMethod),
                array_merge(array($subject, $next), $arguments)
            );
        } else {
            $result = $subject->___callParent($method, $arguments);
        }
        if (isset($pluginInfo[Definition::LISTENER_AFTER])) {
            foreach ($pluginInfo[Definition::LISTENER_AFTER] as $code) {
                $result = $this->pluginList->getPlugin($type, $code)->{'after' . $capMethod}($subject, $result);
            }
        }
        return $result;
    }
} 
