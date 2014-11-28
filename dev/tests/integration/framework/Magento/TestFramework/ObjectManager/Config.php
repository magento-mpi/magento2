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
     * Clean configuration
     */
    public function clean()
    {
        $reflection = new \ReflectionClass(get_class($this->subjectConfig));
        $this->subjectConfig = $reflection->newInstanceArgs();
    }
}
