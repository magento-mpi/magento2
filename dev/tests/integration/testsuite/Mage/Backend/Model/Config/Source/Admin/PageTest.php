<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Mage_Backend_Model_Config_Source_Admin_Page
 */
class Mage_Backend_Model_Config_Source_Admin_PageTest extends Mage_Adminhtml_Utility_Controller
{
    public function testToOptionArray()
    {
        Mage::getConfig()->setCurrentAreaCode('adminhtml');
        $this->dispatch('backend/admin/system_config/edit/section/admin');

        $dom = PHPUnit_Util_XML::load($this->getResponse()->getBody(), true);
        $select = $dom->getElementById('admin_startup_menu_item_id');

        $this->assertNotEmpty($select, 'Startup Page select missed');
        $options = $select->getElementsByTagName('option');
        $optionsCount = $options->length;

        $this->assertGreaterThan(0, $optionsCount, 'There must be present menu items at the admin backend');

        $this->assertEquals('Dashboard', $options->item(0)->nodeValue, 'First element is not Dashboard');
        $this->assertContains('Configuration', $options->item($optionsCount - 1)->nodeValue);
    }
}
