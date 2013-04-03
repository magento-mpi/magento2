<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Page
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_PageTest extends Mage_Backend_Area_TestCase
{
    public function testGetPage()
    {
        /** @var $block Saas_Launcher_Block_Adminhtml_Storelauncher_Page */
        $block = Mage::getObjectManager()->create('Saas_Launcher_Block_Adminhtml_Storelauncher_Page');

        $this->assertInstanceOf('Saas_Launcher_Model_Page', $block->getPage());
    }
}
