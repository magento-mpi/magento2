<?php
/**
 * ObjectManager config with interception processing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Interception\ObjectManager;

class Config extends \Magento\Framework\ObjectManager\Config\ProxyConfig
{
    /**
     * @var \Magento\Framework\Interception\ConfigInterface
     */
    protected $interceptionConfig;

    /**
     * Set Interception config
     *
     * @param \Magento\Framework\Interception\ConfigInterface $interceptionConfig
     * @return void
     */
    public function setInterceptionConfig(\Magento\Framework\Interception\ConfigInterface $interceptionConfig)
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
