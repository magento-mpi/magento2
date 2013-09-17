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
