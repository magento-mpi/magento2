<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\TestFramework\ObjectManager;

class Config extends \Magento\Framework\Interception\ObjectManager\Config
{
    /**
     * Clean configuration by recreating subject for proxy config
     */
    public function clean()
    {
        $className = get_class($this->subjectConfig);
        $this->subjectConfig = new $className();
    }
}
