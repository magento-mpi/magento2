<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Launcher_Block_LandingPage
 */
class Mage_Launcher_Block_Page_StoreLauncherTest extends PHPUnit_Framework_TestCase
{
    public function testGetTiles()
    {
        /** @var $block Mage_Launcher_Block_Page_StoreLauncher */
        $block = Mage::getObjectManager()->create('Mage_Launcher_Block_Page_StoreLauncher');

        $this->assertInstanceOf('Mage_Launcher_Model_Page', $block->getPage());
    }
}
