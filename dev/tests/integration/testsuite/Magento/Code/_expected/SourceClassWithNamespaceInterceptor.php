<?php
namespace Magento\Code\GeneratorTest\SourceClassWithNamespace;

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Interceptor extends \Magento\Code\GeneratorTest\SourceClassWithNamespace
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $pluginLocator = null;

    /**
     * List of plugins
     *
     * @var \Magento\Interception\PluginList
     */
    protected $pluginList = null;

    /**
     * Invocation chain
     *
     * @var \Magento\Interception\Chain
     */
    protected $chain = null;

    /**
     * Subject type name
     *
     * @var string
     */
    protected $subjectType = null;

    public function __construct(\Magento\ObjectManager $pluginLocator, \Magento\Interception\PluginList $pluginList, \Magento\Interception\Chain $chain, $param1 = '', $param2 = '\\', $param3 = '\'')
    {
        $this->pluginLocator = $pluginLocator;
        $this->pluginList = $pluginList;
        $this->chain = $chain;
        $this->subjectType = get_parent_class($this);
        parent::__construct($param1, $param2, $param3);
    }

    public function ___callParent($method, array $arguments)
    {
        return call_user_func_array(array('parent', $method), $arguments);
    }

    public function __sleep()
    {
        if (method_exists(get_parent_class($this), '__sleep')) {
            return parent::__sleep();
        } else {
            return array_keys(get_class_vars(get_parent_class($this)));
        }
    }

    public function __wakeup()
    {
        $this->pluginLocator = \Magento\Framework\App\ObjectManager::getInstance();
        $this->pluginList = $this->pluginLocator->get('Magento\Interception\PluginList');
        $this->chain = $this->pluginLocator->get('Magento\Interception\Chain');
        $this->subjectType = get_parent_class($this);
    }

    protected function ___call($method, array $arguments, array $pluginInfo)
    {
        $capMethod = ucfirst($method);
        $result = null;
        if (isset($pluginInfo[\Magento\Interception\Definition::LISTENER_BEFORE])) {
            foreach ($pluginInfo[\Magento\Interception\Definition::LISTENER_BEFORE] as $code) {
                $beforeResult = call_user_func_array(
                    array($this->pluginList->getPlugin($this->subjectType, $code), 'before'. $capMethod), array_merge(array($this), $arguments)
                );
                if ($beforeResult) {
                    $arguments = $beforeResult;
                }
            }
        }
        if (isset($pluginInfo[\Magento\Interception\Definition::LISTENER_AROUND])) {
            $chain = $this->chain;
            $type = $this->subjectType;
            $subject = $this;
            $code = $pluginInfo[\Magento\Interception\Definition::LISTENER_AROUND];
            $next = function () use ($chain, $type, $method, $subject, $code) {
                return $chain->invokeNext($type, $method, $subject, func_get_args(), $code);
            };
            $result = call_user_func_array(
                array($this->pluginList->getPlugin($this->subjectType, $code), 'around' . $capMethod),
                array_merge(array($this, $next), $arguments)
            );
        } else {
            $result = call_user_func_array(array('parent', $method), $arguments);
        }
        if (isset($pluginInfo[\Magento\Interception\Definition::LISTENER_AFTER])) {
            foreach ($pluginInfo[\Magento\Interception\Definition::LISTENER_AFTER] as $code) {
                $result = $this->pluginList->getPlugin($this->subjectType, $code)
                    ->{'after' . $capMethod}($this, $result);
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function publicChildMethod(\Zend\Code\Generator\ClassGenerator $classGenerator, $param1 = '', $param2 = '\\', $param3 = '\'', array $array = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'publicChildMethod');
        if (!$pluginInfo) {
            return parent::publicChildMethod($classGenerator, $param1, $param2, $param3, $array);
        } else {
            return $this->___call('publicChildMethod', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function publicMethodWithReference(\Zend\Code\Generator\ClassGenerator &$classGenerator, &$param1, array &$array)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'publicMethodWithReference');
        if (!$pluginInfo) {
            return parent::publicMethodWithReference($classGenerator, $param1, $array);
        } else {
            return $this->___call('publicMethodWithReference', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function publicChildWithoutParameters()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'publicChildWithoutParameters');
        if (!$pluginInfo) {
            return parent::publicChildWithoutParameters();
        } else {
            return $this->___call('publicChildWithoutParameters', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function publicParentMethod(\Zend\Code\Generator\DocBlockGenerator $docBlockGenerator, $param1 = '', $param2 = '\\', $param3 = '\'', array $array = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'publicParentMethod');
        if (!$pluginInfo) {
            return parent::publicParentMethod($docBlockGenerator, $param1, $param2, $param3, $array);
        } else {
            return $this->___call('publicParentMethod', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function publicParentWithoutParameters()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'publicParentWithoutParameters');
        if (!$pluginInfo) {
            return parent::publicParentWithoutParameters();
        } else {
            return $this->___call('publicParentWithoutParameters', func_get_args(), $pluginInfo);
        }
    }
}
