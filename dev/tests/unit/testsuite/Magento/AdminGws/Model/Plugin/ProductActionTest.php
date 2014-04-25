<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model\Plugin;

class ProductActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminGws\Model\Plugin\ProductAction
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $roleMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->roleMock = $this->getMock('Magento\AdminGws\Model\Role', array(), array(), '', false);
        $this->subjectMock = $this->getMock('Magento\Catalog\Model\Product\Action', array(), array(), '', false);
        $this->model = new \Magento\AdminGws\Model\Plugin\ProductAction($this->roleMock);
    }

    public function testBeforeUpdateWebsitesDoesNotCheckWebsiteAccessWhenRoleIsNotRestricted()
    {
        $this->roleMock->expects($this->once())->method('getIsAll')->will($this->returnValue(true));
        $this->roleMock->expects($this->never())->method('getIsWebsiteLevel');
        $this->roleMock->expects($this->never())->method('hasWebsiteAccess');
        $this->model->beforeUpdateWebsites($this->subjectMock, array(), array(), 'type');
    }

    /**
     * @param boolean $isWebsiteLevelRole
     * @param boolean $hasWebsiteAccess
     * @param string $actionType
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage You need more permissions to save this item.
     * @dataProvider beforeUpdateWebsitesThrowsExceptionWhenAccessIsRestrictedDataProvider
     */
    public function testBeforeUpdateWebsitesThrowsExceptionWhenAccessIsRestricted(
        $isWebsiteLevelRole,
        $hasWebsiteAccess,
        $actionType
    ) {
        $this->roleMock->expects($this->once())->method('getIsAll')->will($this->returnValue(false));
        $this->roleMock->expects(
            $this->any()
        )->method(
            'getIsWebsiteLevel'
        )->will(
            $this->returnValue($isWebsiteLevelRole)
        );
        $websiteIds = array(1);
        $this->roleMock->expects(
            $this->any()
        )->method(
            'hasWebsiteAccess'
        )->with(
            $websiteIds,
            true
        )->will(
            $this->returnValue($hasWebsiteAccess)
        );
        $this->model->beforeUpdateWebsites($this->subjectMock, array(), $websiteIds, $actionType);
    }

    public function beforeUpdateWebsitesThrowsExceptionWhenAccessIsRestrictedDataProvider()
    {
        return array(
            array(true, false, 'remove'),
            array(false, true, 'remove'),
            array(false, false, 'remove'),
            array(true, false, 'add'),
            array(false, true, 'add'),
            array(false, false, 'add')
        );
    }

    public function testBeforeUpdateWebsitesDoesNotThrowExceptionWhenUserHasAccessToGivenWebsites()
    {
        $this->roleMock->expects($this->once())->method('getIsAll')->will($this->returnValue(false));
        $this->roleMock->expects($this->once())->method('getIsWebsiteLevel')->will($this->returnValue(true));
        $websiteIds = array(1);
        $this->roleMock->expects(
            $this->once()
        )->method(
            'hasWebsiteAccess'
        )->with(
            $websiteIds,
            true
        )->will(
            $this->returnValue(true)
        );
        $this->model->beforeUpdateWebsites($this->subjectMock, array(), $websiteIds, 'add');
    }
}
