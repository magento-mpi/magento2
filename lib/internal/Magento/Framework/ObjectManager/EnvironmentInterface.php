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
     * @param EnvironmentFactory $envFactory
     */
    public function __construct(EnvironmentFactory $envFactory);

    /**
     * Return name of running mode
     *
     * @return string
     */
    public static function getMode();

    /**
     * Return config object
     *
     * @return \Magento\Framework\Interception\ObjectManager\Config
     */
    public function getDiConfig();

    /**
     * Return factory object
     *
     * @return \Magento\Framework\ObjectManager\FactoryInterface
     */
    public function getObjectManagerFactory();

    /**
     * Return ConfigLoader object
     *
     * @return \Magento\Framework\App\ObjectManager\ConfigLoader | null
     */
    public function getObjectManagerConfigLoader();
}
