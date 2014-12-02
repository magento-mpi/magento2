<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\ObjectManager\Environment;

class Developer extends \Magento\Framework\ObjectManager\Environment\Developer
{
    public function getDiConfig()
    {
        if (!$this->config) {
            $this->config = new \Magento\TestFramework\ObjectManager\Config(
                new \Magento\Framework\ObjectManager\Config\Config(
                    $this->envFactory->getRelations(),
                    $this->envFactory->getDefinitions()
                )
            );
        }

        return $this->config;
    }
}
