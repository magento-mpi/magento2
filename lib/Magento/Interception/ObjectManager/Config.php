<?php
/**
 * ObjectManager config with interception processing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Interception\ObjectManager;

class Config extends \Magento\ObjectManager\Config\Config
{
    /**
     * @var \Magento\Interception\Config
     */
    protected $interceptionConfig;

    /**
     * Set Interception config
     *
     * @param \Magento\Interception\Config $interceptionConfig
     */
    public function setInterceptionConfig(\Magento\Interception\Config $interceptionConfig)
    {
        $this->interceptionConfig = $interceptionConfig;
    }

    /**
     * Retrieve instance type with interception processing
     *
     * @param string $instanceName
     * @return string
     */
    public function getInstanceType($instanceName)
    {
        $type = parent::getInstanceType($instanceName);
        if ($this->interceptionConfig && $this->interceptionConfig->hasPlugins($instanceName)) {
            return $type . '\\Interceptor';
        }
        return $type;
    }

    /**
     * Retrieve instance type without interception processing
     *
     * @param string $instanceName
     * @return string
     */
    public function getOriginalInstanceType($instanceName)
    {
        return parent::getInstanceType($instanceName);
    }
}
