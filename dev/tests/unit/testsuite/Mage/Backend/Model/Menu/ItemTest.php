<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Menu_ItemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu_Item
     */
    protected  $_model;

    public function setUp()
    {
        /*$this->_model = new Mage_Backend_Model_Menu_Item(
            array('id' => 'item1')
        );*/
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresAcl()
    {
        $this->markTestIncomplete();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresObjectFactory()
    {
        $this->markTestIncomplete();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresUrlModel()
    {
        $this->markTestIncomplete();
    }

    public function testGetFullPathReturnsPathWithItemId()
    {
        $this->markTestIncomplete();
    }

    public function testGetUrlWithEmptyActionReturnsHashSign()
    {
        $this->markTestIncomplete();
    }

    public function testGetUrlWithValidActionReturnsUrl()
    {
        $this->markTestIncomplete();
    }

    public function testHasClickCallbackReturnsFalseIfItemHasAction()
    {
        $this->markTestIncomplete();
    }

    public function testGetClickCallbackReturnsStoppingJsIfItemDoesntHaveAction()
    {
        $this->markTestIncomplete();
    }

    public function testIsDisabledReturnsTrueIfModuleOutputIsDisabled()
    {
        $this->markTestIncomplete();
    }

    public function testIsDisabledReturnsTrueIfModuleDependenciesFail()
    {
        $this->markTestIncomplete();
    }

    public function testIsDisabledReturnsTrueIfConfigDependenciesFail()
    {
        $this->markTestIncomplete();
    }

    public function testIsDisabledReturnsFalseIfNoDependenciesFail()
    {
        $this->markTestIncomplete();
    }

    public function testIsAllowedReturnsTrueIfResourceIsAvailable()
    {
        $this->markTestIncomplete();
    }

    public function testIsAllowedReturnsFalseIfResourceIsNotAvailable()
    {
        $this->markTestIncomplete();
    }

    public function testAddChildCreatesSubmenuOnFirstCall()
    {
        $this->markTestIncomplete();
    }

    public function testSetParentUpdatesAllChildren()
    {
        $this->markTestIncomplete();
    }

    public function testGetFirstAvailableChildReturnsItemActionIfItemHasNoChildren()
    {
        $this->markTestIncomplete();
    }

    public function testGetFirstAvailableChildReturnsLeafNodeActionIfHasChildren()
    {
        $this->markTestIncomplete();
    }
}
