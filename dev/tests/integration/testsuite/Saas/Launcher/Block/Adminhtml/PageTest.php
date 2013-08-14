<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Launcher_Block_Adminhtml_Page
 *
 * @magentoDataFixture Saas/Launcher/_files/pages.php
 * @magentoDataFixture Saas/Launcher/_files/config_bootstrap.php
 */
class Saas_Launcher_Block_Adminhtml_PageTest extends PHPUnit_Framework_TestCase
{
    public function testGetTileBlocks()
    {
        $this->markTestIncomplete('Incorrect usage of magentoDataFixture');
        $page = Mage::getObjectManager()->create('Saas_Launcher_Model_Page')->loadByPageCode('landing_page_1');
        $block = Mage::getObjectManager()->create('Saas_Launcher_Block_Adminhtml_Page');
        $block->setPage($page);
        $tiles = $block->getTileBlocks();

        $this->assertNotEmpty($tiles);

        foreach ($tiles as $tile) {
            $this->assertInstanceOf('Saas_Launcher_Block_Adminhtml_Tile', $tile);
        }
    }
}
