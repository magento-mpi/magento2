<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Di_InstanceManager
{
    /**
     * Add shared instance
     *
     * @param object $instance
     * @param string $classOrAlias
     * @return Magento_Di_InstanceManager
     */
    public function addSharedInstance($instance, $classOrAlias);

    /**
     * Check whether instance manager has shared instance of given class (alias)
     *
     * @param string $classOrAlias
     * @return bool
     */
    public function hasSharedInstance($classOrAlias);

    /**
     * Remove shared instance
     *
     * @param string $classOrAlias
     * @return Magento_Di_InstanceManager
     */
    public function removeSharedInstance($classOrAlias);

    /**
     * Add type preference
     *
     * @param string $interfaceOrAbstract
     * @param string $implementation
     * @return Zend\Di\InstanceManager
     */
    public function addTypePreference($interfaceOrAbstract, $implementation);

    /**
     * Set parameters
     *
     * @param string $aliasOrClass
     * @param array $parameters
     */
    public function setParameters($aliasOrClass, array $parameters);
}
