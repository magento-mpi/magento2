<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_ObjectManager_Factory
{
    /**
     * Set object manager
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function setObjectManager(Magento_ObjectManager $objectManager);

    /**
     * Set object manager configuration
     *
     * @param Magento_ObjectManager_Config $config
     */
    public function setConfig(Magento_ObjectManager_Config $config);

    /**
     * Retrieve definitions
     *
     * @return Magento_ObjectManager_Definition
     */
    public function getDefinitions();

    /**
     * Create instance with call time arguments
     *
     * @param string $requestedType
     * @param array $arguments
     * @return object
     * @throws LogicException
     * @throws BadMethodCallException
     */
    public function create($requestedType, array $arguments = array());
}
