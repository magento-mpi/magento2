<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Launcher_Block_Adminhtml_Page
 *
 * @magentoDataFixture Mage/Launcher/_files/pages.php
 * @magentoDataFixture Mage/Launcher/_files/config_bootstrap.php
 */
class Mage_Launcher_Block_Adminhtml_PageTest extends PHPUnit_Framework_TestCase
{
    public function testGetTileBlocks()
    {
        $page = Mage::getObjectManager()->create('Mage_Launcher_Model_Page')->loadByCode('landing_page_1');
        $block = Mage::getObjectManager()->create('Mage_Launcher_Block_Adminhtml_Page');
        $block->setPage($page);
        $tiles = $block->getTileBlocks();

        $this->assertNotEmpty($tiles);

        foreach ($tiles as $tile) {
            $this->assertInstanceOf('Mage_Launcher_Block_Adminhtml_Tile', $tile);
        }
    }
}
