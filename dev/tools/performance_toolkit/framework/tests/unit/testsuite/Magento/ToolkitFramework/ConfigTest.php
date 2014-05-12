<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ToolkitFramework;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return void
     */
    public function testUnaccessibleConfig()
    {
        $this->setExpectedException('Exception', 'Profile configuration file `))` is not readable or does not exists.');
        \Magento\ToolkitFramework\Config::getInstance()->loadConfig('))');
    }
}
