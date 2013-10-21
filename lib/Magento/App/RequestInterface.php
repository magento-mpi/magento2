<?php
/**
 * Application request
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

interface RequestInterface extends \Magento\HTTP\RequestInterface
{
    /**
     * Retrieve module name
     *
     * @return string
     */
    public function getModuleName();

    /**
     * Set Module name
     *
     * @param  string $name
     * @return self
     */
    public function setModuleName($name);

    /**
     * Retrieve request front name
     *
     * @return string|null
     */
    public function getFrontName();

    /**
     * Check if code declared as direct access frontend name
     *
     * @param   string $code
     * @return  bool
     */
    public function isDirectAccessFrontendName($code);

    /**
     * Retrieve name of route
     *
     * @return string
     */
    public function getRouteName();

    /**
     * Set route name
     *
     * @param string $route
     * @return $this
     */
    public function setRouteName($route);

    /**
     * Get route name used in request (ignore rewrite)
     *
     * @return string
     */
    public function getRequestedRouteName();

    /**
     * Set routing info data
     *
     * @param  array $data
     * @return self
     */
    public function setRoutingInfo($data);

    /**
     * Specify module name where was found currently used controller
     *
     * @param  string $module
     * @return self
     */
    public function setControllerModule($module);

    /**
     * Get module name of currently used controller
     *
     * @return string
     */
    public function getControllerModule();

    /**
     * Retrieve the controller name
     *
     * @return string
     */
    public function getControllerName();

    /**
     * Set the controller name to use
     *
     * @param  string $value
     * @return self
     */
    public function setControllerName($value);

    /**
     * Get controller name used in request (ignore rewrite)
     *
     * @return string
     */
    public function getRequestedControllerName();

    /**
     * Retrieve action name
     *
     * @return string
     */
    public function getActionName();

    /**
     * Set action name
     *
     * @param string $name
     */
    public function setActionName($name);

    /**
     * Get action name used in request (ignore rewrite)
     *
     * @return string
     */
    public function getRequestedActionName();

    /**
     * Retrieve param by key
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getParam($key, $defaultValue = null);

    /**
     * Retrieve only user params (i.e, any param specific to the object and not the environment)
     *
     * @return array
     */
    public function getUserParams();

    /**
     * Retrieve a single user param (i.e, a param specific to the object and not the environment)
     *
     * @param  string $key
     * @param  string $default Default value to use if key not found
     * @return mixed
     */
    public function getUserParam($key, $default = null);

    /**
     * Set an action parameter
     *
     * @param  string $key
     * @param  mixed $value
     * @return self
     */
    public function setParam($key, $value);

    /**
     * Get all action parameters
     *
     * @return array
     */
    public function getParams();

    /**
     * Set action parameters; does not overwrite
     *
     * @param  array $array
     * @return self
     */
    public function setParams(array $array);

    /**
     * Unset all user parameters
     *
     * @return self
     */
    public function clearParams();

    /**
     * Get list of allowed parameter sources
     *
     * @return array
     */
    public function getParamSources();

    /**
     * Set allowed parameter sources
     *
     * Can be empty array, or contain one or more of '_GET' or '_POST'.
     *
     * @param  array $paramSources
     * @return self
     */
    public function setParamSources(array $paramSources = array());

    /**
     * Set flag indicating whether or not request has been dispatched
     *
     * @param  boolean $flag
     * @return self
     */
    public function setDispatched($flag = true);

    /**
     * Determine if the request has been dispatched
     *
     * @return boolean
     */
    public function isDispatched();

    /**
     * Retrieve an alias
     *
     * Retrieve the actual key represented by the alias $name.
     *
     * @param  string $name
     * @return string|null Returns null when no alias exists
     */
    public function getAlias($name);

    /**
     * Set a key alias
     *
     * Set an alias used for key lookUps. $name specifies the alias, $target
     * specifies the actual key to use.
     *
     * @param  string $name
     * @param  string $target
     * @return self
     */
    public function setAlias($name, $target);

    /**
     * Retrieve the list of all aliases
     *
     * @return array
     */
    public function getAliases();

    /**
     * Initialization process before forward
     *
     * Collect properties changed by _forward in protected storage
     * before _forward was called first time.
     *
     * @return self
     */
    public function initForward();

    /**
     * Retrieve property's value which was before _forward call.
     *
     * @param  string $name
     * @return array|string|null
     */
    public function getBeforeForwardInfo($name = null);

    /**
     * Specify/Get _isStraight flag value
     *
     * @param  bool $flag
     * @return bool
     */
    public function isStraight($flag = null);

    /**
     * Return the raw body of the request, if present
     *
     * @return string|false Raw body, or false if not present
     */
    public function getRawBody();
}
