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
 * Test class for Mage_Launcher_Block_Adminhtml_Storelauncher_Page
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_PageTest extends PHPUnit_Framework_TestCase
{
    public function testGetPage()
    {
        /** @var $block Mage_Launcher_Block_Adminhtml_Storelauncher_Page */
        $block = Mage::getObjectManager()->create('Mage_Launcher_Block_Adminhtml_Storelauncher_Page');

        $this->assertInstanceOf('Mage_Launcher_Model_Page', $block->getPage());
    }
}
