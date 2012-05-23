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
class Mage_Selenium_Uimap_PageTest extends Mage_PHPUnit_TestCase
{
    public function test__construct()
    {
        $pageContainer = array();
        $uipage = new Mage_Selenium_Uimap_Page('pageId', $pageContainer);
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);
    }

    /**
     * @covers Mage_Selenium_Uimap_Page::getId
     */
    public function testGetId()
    {
        $pageId = 'testId';
        $pageContainer = array();
        $uipage = new Mage_Selenium_Uimap_Page($pageId, $pageContainer);
        $this->assertEquals($uipage->getPageId(), $pageId);
    }
}