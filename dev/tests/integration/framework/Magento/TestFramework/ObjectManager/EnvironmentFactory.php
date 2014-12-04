<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\ObjectManager;

use Magento\TestFramework\ObjectManager\Environment\Developer;

class EnvironmentFactory extends \Magento\Framework\ObjectManager\EnvironmentFactory
{
    public function createEnvironment()
    {
        return new Developer($this);
    }
}
