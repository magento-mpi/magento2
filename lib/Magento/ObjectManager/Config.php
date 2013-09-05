<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_ObjectManager_Config
{
    /**
     * Set class relations
     *
     * @param Magento_ObjectManager_Relations $relations
     */
    public function setRelations(Magento_ObjectManager_Relations $relations);

    /**
     * Set configuration cache instance
     *
     * @param Magento_ObjectManager_ConfigCache $cache
     */
    public function setCache(Magento_ObjectManager_ConfigCache $cache);

    /**
     * Retrieve list of arguments per type
     *
     * @param string $type
     * @param array $arguments
     * @return array
     */
    public function getArguments($type, $arguments);

    /**
     * Check whether type is shared
     *
     * @param string $type
     * @return bool
     */
    public function isShared($type);

    /**
     * Retrieve instance type
     *
     * @param string $instanceName
     * @return mixed
     */
    public function getInstanceType($instanceName);

    /**
     * Retrieve preference for type
     *
     * @param string $type
     * @return string
     * @throws LogicException
     */
    public function getPreference($type);

    /**
     * Extend configuration
     *
     * @param array $configuration
     */
    public function extend(array $configuration);
}
