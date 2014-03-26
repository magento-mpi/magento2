<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_toolkit
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ToolkitFramework;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @retrun void
     */
    public function testUnaccessibleConfig()
    {
        $this->setExpectedException('Exception', 'Profile configuration file `))` is not readable or does not exists.');
        \Magento\ToolkitFramework\Config::getInstance()->loadConfig('))');
    }
}
