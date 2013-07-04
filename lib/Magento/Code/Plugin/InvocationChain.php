<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Code_Plugin_InvocationChain
{
    /**
     * Original instance whose behavior is decorated by plugins
     *
     * @var mixed
     */
    protected $_subject;

    /**
     * Name of the method to invoke
     *
     * @var string
     */
    protected $_methodName;

    /**
     * @var \Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * List of the plugins
     *
     * @var array
     */
    protected $_pluginList;

    /**
     * @param mixed $subject
     * @param string $methodName
     * @param \Magento_ObjectManager $objectManager
     * @param array $pluginList
     */
    public function __construct($subject, $methodName, \Magento_ObjectManager $objectManager, array $pluginList)
    {
        $this->_subject = $subject;
        $this->_methodName = $methodName;
        $this->_objectManager = $objectManager;
        $this->_pluginList = $pluginList;
    }

    /**
     * Propagate invocation through the chain
     *
     * @param array $arguments
     * @return mixed
     */
    public function proceed(array $arguments)
    {
        $pluginClassName = array_shift($this->_pluginList);
        $methodName = $this->_methodName;
        if (!is_null($pluginClassName)) {
            return $this->_objectManager->get($pluginClassName)->$methodName($arguments, $this);
        }
        return call_user_func_array(array($this->_subject, $methodName), $arguments);
    }
}
