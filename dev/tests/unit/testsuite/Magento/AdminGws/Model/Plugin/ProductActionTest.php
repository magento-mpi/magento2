<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminGws_Model_Plugin_ProductActionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_AdminGws_Model_Plugin_ProductAction
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_roleMock;

    protected function setUp()
    {
        $this->_roleMock = $this->getMock('Magento_AdminGws_Model_Role', array(), array(), '', false);
        $this->_model = new Magento_AdminGws_Model_Plugin_ProductAction($this->_roleMock);
    }

    public function testBeforeUpdateWebsitesDoesNotCheckWebsiteAccessWhenRoleIsNotRestricted()
    {
        $this->_roleMock->expects($this->once())
            ->method('getIsAll')
            ->will($this->returnValue(true));
        $this->_roleMock->expects($this->never())
            ->method('getIsWebsiteLevel');
        $this->_roleMock->expects($this->never())
            ->method('hasWebsiteAccess');
        $this->_model->beforeUpdateWebsites(array());
    }

    /**
     * @param boolean $isWebsiteLevelRole
     * @param boolean $hasWebsiteAccess
     * @param string $actionType
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage You need more permissions to save this item.
     * @dataProvider beforeUpdateWebsitesThrowsExceptionWhenAccessIsRestrictedDataProvider
     */
    public function testBeforeUpdateWebsitesThrowsExceptionWhenAccessIsRestricted(
        $isWebsiteLevelRole,
        $hasWebsiteAccess,
        $actionType
    ) {
        $this->_roleMock->expects($this->once())
            ->method('getIsAll')
            ->will($this->returnValue(false));
        $this->_roleMock->expects($this->any())
            ->method('getIsWebsiteLevel')
            ->will($this->returnValue($isWebsiteLevelRole));
        $websiteIds = array(1);
        $this->_roleMock->expects($this->any())
            ->method('hasWebsiteAccess')
            ->with($websiteIds, true)
            ->will($this->returnValue($hasWebsiteAccess));
        $this->_model->beforeUpdateWebsites(array(array(), $websiteIds, $actionType));
    }

    public function beforeUpdateWebsitesThrowsExceptionWhenAccessIsRestrictedDataProvider()
    {
        return array(
            array(true, false, 'remove'),
            array(false, true, 'remove'),
            array(false, false, 'remove'),
            array(true, false, 'add'),
            array(false, true, 'add'),
            array(false, false, 'add'),
        );
    }

    public function testBeforeUpdateWebsitesDoesNotThrowExceptionWhenUserHasAccessToGivenWebsites()
    {
        $this->_roleMock->expects($this->once())
            ->method('getIsAll')
            ->will($this->returnValue(false));
        $this->_roleMock->expects($this->once())
            ->method('getIsWebsiteLevel')
            ->will($this->returnValue(true));
        $websiteIds = array(1);
        $this->_roleMock->expects($this->once())
            ->method('hasWebsiteAccess')
            ->with($websiteIds, true)
            ->will($this->returnValue(true));
        $this->_model->beforeUpdateWebsites(array(array(), $websiteIds, 'add'));
    }
}
