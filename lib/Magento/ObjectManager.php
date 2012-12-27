<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_ObjectManager
{
    /**
     * Create new object instance
     *
     * @abstract
     * @param string $className
     * @param array $arguments
     * @param bool $isShared
     * @return mixed
     */
    public function create($className, array $arguments = array(), $isShared = true);

    /**
     * Retrieve cached object instance
     *
     * @abstract
     * @param string $className
     * @param array $arguments
     * @return mixed
     */
    public function get($className, array $arguments = array());

    /**
     * Load DI configuration for specified ares
     *
     * @abstract
     * @param string $areaCode
     * @return Magento_ObjectManager
     */
    public function loadAreaConfiguration($areaCode = null);

    /**
     * Add shared instance
     *
     * @param object $instance
     * @param string $classOrAlias
     * @return Magento_ObjectManager
     */
    public function addSharedInstance($instance, $classOrAlias);

    /**
     * Remove shared instance
     *
     * @param string $classOrAlias
     * @return Magento_ObjectManager
     */
    public function removeSharedInstance($classOrAlias);

    /**
     * Check whether object manager has shared instance of given class (alias)
     *
     * @param string $classOrAlias
     * @return bool
     */
    public function hasSharedInstance($classOrAlias);

    /**
     * Add alias
     *
     * @param  string $alias
     * @param  string $class
     * @param  array  $parameters
     * @return Magento_ObjectManager
     * @throws Zend\Di\Exception\InvalidArgumentException
     */
    public function addAlias($alias, $class, array $parameters = array());

    /**
     * Get class name by alias
     *
     * @param string
     * @return string|bool
     */
    public function getClassFromAlias($alias);
}
