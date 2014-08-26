<?php
/**
 * Console request
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Console;

class Request implements \Magento\Framework\App\RequestInterface
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->setParam($parameters);
    }

    /**
     * Initialize console parameters
     *
     * @param array $parameters
     * @return void
     */
    public function setParam($parameters)
    {
        $this->params = getopt(null, $parameters);
    }

    /**
     * Retrieve module name
     *
     * @return void
     */
    public function getModuleName()
    {
        return;
    }

    /**
     * Set Module name
     *
     * @param string $name
     * @return void
     */
    public function setModuleName($name)
    {
    }

    /**
     * Retrieve action name
     *
     * @return void
     */
    public function getActionName()
    {
        return;
    }

    /**
     * Set action name
     *
     * @param string $name
     * @return void
     */
    public function setActionName($name)
    {
    }

    /**
     * Retrieve param by key
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getParam($key, $defaultValue = null)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
        return $defaultValue;
    }

    /**
     * Stub to satisfy RequestInterface
     *
     * @param null|string $name
     * @param null|string $default
     * @return null|string|void
     */
    public function getCookie($name, $default)
    {
    }
}
