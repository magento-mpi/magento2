<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdminGws\Model;

class CallbackInvoker
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Invoke specified callback depending on whether it is a string or array
     *
     * @param string|array $callback
     * @param string $defaultFactoryClassName
     * @param object $passThroughObject
     * @return mixed
     */
    public function invoke($callback, $defaultFactoryClassName, $passThroughObject)
    {
        $class = $defaultFactoryClassName;
        $method = $callback;
        if (is_array($callback)) {
            list($class, $method) = $callback;
        }

        $object = $this->objectManager->get($class);
        if (method_exists($object, $method)) {
            return call_user_func_array(array($object, $method), array($passThroughObject));
        }
        return null;
    }
}
