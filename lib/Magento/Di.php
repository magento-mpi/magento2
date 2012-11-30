<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Di
{
    /**
     * Get instance manager object
     *
     * @return Magento_Di_InstanceManager
     */
    public function instanceManager();

    /**
     * Retrieve a new instance of a class
     *
     * @param mixed $name
     * @param array $parameters
     * @param bool $isShared
     * @return null|object
     */
    public function newInstance($name, array $parameters = array(), $isShared = true);

    /**
     * Get object of given class
     *
     * @param  string $name   Class name or service alias
     * @param  null|array  $params Parameters to pass to the constructor
     * @return object|null
     */
    public function get($name, array $params = array());
}
