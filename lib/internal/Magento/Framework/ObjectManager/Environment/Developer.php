<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\ObjectManager\Environment;

use Magento\Framework\ObjectManager\EnvironmentInterface;

class Developer extends AbstractEnvironment implements EnvironmentInterface
{
    /**#@+
     * Mode name
     */
    const MODE = 'developer';
    protected $mode = self::MODE;
    /**#@- */

    /**
     * @var \Magento\Framework\Interception\ObjectManager\Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $configPreference = '\Magento\Framework\ObjectManager\Factory\Dynamic\Developer';

    /**
     * @return \Magento\Framework\Interception\ObjectManager\Config
     */
    public function getDiConfig()
    {
        if (!$this->config) {
            $this->config = new \Magento\Framework\Interception\ObjectManager\Config(
                new \Magento\Framework\ObjectManager\Config\Config(
                    $this->envFactory->getRelations(),
                    $this->envFactory->getDefinitions()
                )
            );
        }

        return $this->config;
    }

    /**
     * @return null
     */
    public function getObjectManagerConfigLoader()
    {
        return null;
    }
}
