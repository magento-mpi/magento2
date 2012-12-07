<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Selenium_Uimap_FormTest extends Mage_PHPUnit_TestCase
{
    public function test__construct()
    {
        $formContainer = array();
        $instance = new Mage_Selenium_Uimap_Form($formContainer);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Form', $instance);
    }

    /**
     * @covers Mage_Selenium_Uimap_Form::getTab
     */
    public function testGetTab()
    {
        $uipage = $this->getUimapPage('admin', 'create_customer');
        $tab = $uipage->getMainForm()->getTabs()->getTab('addresses');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Tab', $tab);
    }
}