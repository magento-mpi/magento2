<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\ObjectManager;

interface Factory
{
    /**
     * Set object manager
     *
     * @param \Magento\ObjectManager $objectManager
     * @return void
     */
    public function setObjectManager(\Magento\ObjectManager $objectManager);

    /**
     * Set application arguments
     *
     * @param array $array
     * @return void
     */
    public function setArguments($array);

    /**
     * Create instance with call time arguments
     *
     * @param string $requestedType
     * @param array $arguments
     * @return object
     * @throws \LogicException
     * @throws \BadMethodCallException
     */
    public function create($requestedType, array $arguments = array());
}
