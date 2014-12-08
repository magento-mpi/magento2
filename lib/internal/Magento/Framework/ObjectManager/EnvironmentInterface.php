<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\ObjectManager;

/**
 * Interface for ObjectManager Environment
 */
interface EnvironmentInterface
{
    /**
     * Return name of running mode
     *
     * @return string
     */
    public function getMode();

    /**
     * Return config object
     *
     * @return \Magento\Framework\Interception\ObjectManager\Config
     */
    public function getDiConfig();

    /**
     * Return factory object
     *
     * @param array $arguments
     * @return \Magento\Framework\ObjectManager\FactoryInterface
     */
    public function getObjectManagerFactory($arguments);

    /**
     * Return ConfigLoader object
     *
     * @return \Magento\Framework\App\ObjectManager\ConfigLoader | null
     */
    public function getObjectManagerConfigLoader();
}
