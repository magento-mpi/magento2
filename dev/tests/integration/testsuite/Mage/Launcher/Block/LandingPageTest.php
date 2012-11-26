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
class Mage_Launcher_Block_LandingPageTest extends PHPUnit_Framework_TestCase
{
    public function testGetTiles()
    {
        $page = Mage::getModel('Mage_Launcher_Model_Page')->loadByCode('store_launcher');
        $block = Mage::getObjectManager()->create('Mage_Launcher_Block_LandingPage', array('page'=>$page));
        $tiles = $block->getTiles();

        $this->assertNotEmpty($tiles);

        foreach ($tiles as $tile) {
            $this->assertInstanceOf('Mage_Launcher_Block_Tile', $tile);
        }
    }
}
