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
class Mage_Selenium_Uimap_AbstractTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Uimap_Abstract::__call
     */
    public function test__call()
    {
        $uipage = $this->getUimapPage('admin', 'create_customer');

        //Test getAll
        $buttons = $uipage->getAllButtons();
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $buttons);

        $fieldsets = $uipage->getMainForm()->getAllFieldsets();
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $fieldsets);
        $this->assertGreaterThanOrEqual(1, count($fieldsets));

        $buttons = $uipage->getMainForm()->getAllButtons();
        $this->assertInstanceOf('Mage_Selenium_Uimap_ElementsCollection', $buttons);

        //Test get
        $tabs = $uipage->getMainForm()->getTabs();
        $this->assertInstanceOf('Mage_Selenium_Uimap_TabsCollection', $tabs);
        $this->assertGreaterThanOrEqual(1, count($tabs));

        //Test find
        $field = $uipage->findField('first_name');
        $this->assertInternalType('string', $field);

        $message = $uipage->findMessage('success_saved_customer');
        $this->assertInternalType('string', $message);
    }
}