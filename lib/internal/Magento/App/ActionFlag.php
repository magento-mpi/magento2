<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

class ActionFlag
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var array
     */
    protected $_flags = array();

    /**
     * @param RequestInterface $request
     */
    public function __construct(\Magento\App\RequestInterface $request)
    {
        $this->_request = $request;
    }

    /**
     * Setting flag value
     *
     * @param   string $action
     * @param   string $flag
     * @param   string $value
     * @return void
     */
    public function set($action, $flag, $value)
    {
        if ('' === $action) {
            $action = $this->_request->getActionName();
        }
        $this->_flags[$this->_getControllerKey()][$action][$flag] = $value;
    }

    /**
     * Retrieve flag value
     *
     * @param   string $action
     * @param   string $flag
     * @return  bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function get($action, $flag = '')
    {
        if ('' === $action) {
            $action = $this->_request->getActionName();
        }
        if ('' === $flag) {
            return isset($this->_flags[$this->_getControllerKey()])
                ? $this->_flags[$this->_getControllerKey()]
                : array();
        } elseif (isset($this->_flags[$this->_getControllerKey()][$action][$flag])) {
            return $this->_flags[$this->_getControllerKey()][$action][$flag];
        } else {
            return false;
        }
    }

    /**
     * Get controller key
     *
     * @return string
     */
    protected function _getControllerKey()
    {
        return $this->_request->getRequestedRouteName() . '_' . $this->_request->getRequestedControllerName();
    }
} 