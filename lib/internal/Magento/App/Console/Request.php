<?php
/**
 * Console request
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Console;

class Request implements \Magento\App\RequestInterface
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @param array $parameters
     */
    public function __construct($parameters = array())
    {
        $this->params = $this->setParam($parameters);
    }

    /**
     * Initialize console parameters
     *
     * @param $parameters
     */
    public function setParam($parameters)
    {
        $this->params = getopt(null, $parameters);
    }

    /**
     * Retrieve module name
     *
     * @return string
     */
    public function getModuleName()
    {
        return;
    }

    /**
     * Set Module name
     *
     * @param string $name
     */
    public function setModuleName($name)
    {
    }

    /**
     * Retrieve action name
     *
     * @return string
     */
    public function getActionName()
    {
        return;
    }

    /**
     * Set action name
     *
     * @param string $name
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
}
