<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ToolkitFramework;

class FixtureSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return void
     */
    public function testUnaccessibleConfig()
    {
        $this->setExpectedException('Exception', 'Fixtures set file `))` is not readable or does not exists.');
        new \Magento\ToolkitFramework\FixtureSet('))');
    }
}
