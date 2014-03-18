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

interface RequestInterface
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
     * @param string $name
     * @return $this
     */
    public function setModuleName($name);

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
     * @return $this
     */
    public function setActionName($name);

    /**
     * Retrieve param by key
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getParam($key, $defaultValue = null);
}